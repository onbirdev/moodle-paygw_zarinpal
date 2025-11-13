<?php
// This file is part of Moodle - http://moodle.org/
//
// Moodle is free software: you can redistribute it and/or modify
// it under the terms of the GNU General Public License as published by
// the Free Software Foundation, either version 3 of the License, or
// (at your option) any later version.
//
// Moodle is distributed in the hope that it will be useful,
// but WITHOUT ANY WARRANTY; without even the implied warranty of
// MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
// GNU General Public License for more details.
//
// You should have received a copy of the GNU General Public License
// along with Moodle.  If not, see <http://www.gnu.org/licenses/>.

/**
 * ZarinPal Payment Gateway Plugin for Moodle
 *
 * @package     paygw_zarinpal
 * @author      MohammadReza PourMohammad <onbirdev@gmail.com>
 * @copyright   2024 MohammadReza PourMohammad
 * @link        https://onbir.dev
 * @license     http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

namespace paygw_zarinpal;

use coding_exception;
use core_payment\helper as payment_helper;
use core_payment\local\callback\service_provider;
use curl;
use dml_exception;
use ReflectionClass;
use stdClass;

defined('MOODLE_INTERNAL') || die();

require_once($CFG->libdir . '/filelib.php');

/**
 * A helper class for interacting with the ZarinPal payment gateway.
 */
class zarinpal_helper {
    /**
     * Payment status constants representing various states of the payment process.
     */
    /** @var int The payment has been initiated but is not yet completed. */
    public const STATUS_PENDING = 10;
    /** @var int The payment process has failed or encountered an error. */
    public const STATUS_ERROR = 20;
    /** @var int The payment process has been successfully completed. */
    public const STATUS_COMPLETED = 80;

    /**
     * @var string $merchantid The unique identifier assigned to the merchant by the ZarinPal payment gateway.
     */
    private string $merchantid;

    /**
     * @var string $baseurl The base URL of the ZarinPal API, used for sending requests to either the sandbox or production
     *     environment.
     */
    private string $baseurl;

    /**
     * Constructor for the `zarinpal_helper` class.
     *
     * @param string $merchantid The ZarinPal merchant ID.
     * @param bool $sandbox Determines if the sandbox environment should be used.
     */
    public function __construct(string $merchantid, bool $sandbox = false) {
        $this->merchantid = $merchantid;
        $this->baseurl = $sandbox ? 'https://sandbox.zarinpal.com' : 'https://payment.zarinpal.com';
    }

    /**
     * Retrieves the service provider class name for a given component.
     *
     * @param string $component The component name.
     * @return string The fully qualified class name of the service provider.
     * @throws coding_exception If the service provider class doesn't exist or doesn't implement the required interface.
     */
    private function get_service_provider_classname(string $component): string {
        $providerclass = "$component\\payment\\service_provider";

        if (class_exists($providerclass)) {
            $rc = new ReflectionClass($providerclass);
            if ($rc->implementsInterface(service_provider::class)) {
                return $providerclass;
            }
        }

        throw new coding_exception("$component does not have an eligible implementation of payment service_provider.");
    }

    /**
     * Retrieves payment information from the database based on the authority code.
     *
     * @param string $authority The unique authority code for the payment.
     * @return false|stdClass|null The payment record from the database or false if not found.
     * @throws dml_exception If there is a database error.
     */
    public static function get_payment(string $authority) {
        global $DB;
        return $DB->get_record('paygw_zarinpal', ['authority' => $authority]);
    }

    /**
     * Sends a payment request to the ZarinPal API.
     *
     * @param int $accountid The account ID associated with the payment.
     * @param float $amount The payment amount.
     * @param string $currency The payment currency.
     * @param string $component The component name.
     * @param string $paymentarea The payment area.
     * @param int $itemid The item ID being purchased.
     * @param string $description A description of the payment.
     * @return array The result of the payment request, including success status, URL, and possible error messages.
     * @throws dml_exception|coding_exception If there is a database error.
     */
    public function request_payment(
        int $accountid,
        float $amount,
        string $currency,
        string $component,
        string $paymentarea,
        int $itemid,
        string $description = ''
    ): array {
        global $CFG, $USER, $DB;

        // Insert the initial payment record into the database.
        $id = $DB->insert_record('paygw_zarinpal', [
            'component' => $component,
            'payment_area' => $paymentarea,
            'item_id' => $itemid,
            'user_id' => (int)$USER->id,
            'account_id' => $accountid,
            'merchant_id' => $this->merchantid,
            'amount' => $amount,
            'currency' => $currency,
            'status' => self::STATUS_PENDING,
            'created_at' => time(),
        ]);

        // Prepare request parameters.
        $params = [
            'merchant_id' => $this->merchantid,
            'amount' => $amount,
            'callback_url' => "{$CFG->wwwroot}/payment/gateway/zarinpal/process.php?id={$id}",
            'description' => $description ?: $component,
        ];

        // Send the payment request via cURL.
        $curl = new curl();
        $result = $curl->post("{$this->baseurl}/pg/v4/payment/request.json", $params);
        $result = json_decode($result);

        if (!empty($result->data->authority)) {
            // Update the payment record with authority code.
            $DB->update_record(
                'paygw_zarinpal',
                (object)[
                    'id' => $id,
                    'status' => self::STATUS_PENDING,
                    'authority' => $result->data->authority,
                ],
            );

            return [
                'success' => true,
                'url' => "{$this->baseurl}/pg/StartPay/{$result->data->authority}",
                'message' => '',
            ];
        }

        // Update the payment record in case of an error.
        $DB->update_record(
            'paygw_zarinpal',
            (object)[
                'id' => $id,
                'status' => self::STATUS_ERROR,
                'data' => json_encode($result),
            ],
        );

        return [
            'success' => false,
            'url' => '',
            'message' => $result->errors->message ?? get_string('error_unknown', 'paygw_zarinpal'),
        ];
    }

    /**
     * Processes the payment based on the provided status and payment information.
     *
     * @param stdClass $payment The payment record object from the database.
     * @param string $status The payment status, such as 'OK' for success.
     * @return array The result of the payment processing, including success status, URL, and messages.
     * @throws coding_exception If a coding error occurs.
     * @throws dml_exception If there is a database error.
     */
    public function process_payment(stdClass $payment, string $status): array {
        global $DB;

        $paymentdata = (object)[
            'status' => $status,
        ];

        // If the payment status is not 'OK', mark the payment as failed.
        if ($status !== 'OK') {
            $DB->update_record(
                'paygw_zarinpal',
                (object)[
                    'id' => $payment->id,
                    'status' => self::STATUS_ERROR,
                    'data' => json_encode($paymentdata),
                    'updated_at' => time(),
                ],
            );

            return [
                'success' => false,
                'url' => '',
                'message' => get_string('payment_failed', 'paygw_zarinpal', ['payment_id' => $payment->id]),
            ];
        }

        // Verify if the order can be delivered before payment verification.
        $providerclass = $this->get_service_provider_classname($payment->component);
        if (method_exists($providerclass, 'can_deliver_order')) {
            $result = component_class_callback($providerclass, 'can_deliver_order', [
                $payment->payment_area,
                (int)$payment->item_id,
                $payment->id,
                (int)$payment->user_id,
            ]);
            if ($result !== true) {
                return [
                    'success' => false,
                    'url' => '',
                    'message' => $result,
                ];
            }
        }

        // Verify the payment with ZarinPal.
        $curl = new curl();
        $result = $curl->post("{$this->baseurl}/pg/v4/payment/verify.json", [
            'merchant_id' => $this->merchantid,
            'amount' => $payment->amount,
            'authority' => $payment->authority,
        ]);
        $result = json_decode($result);

        $paymentdata->verify_result = $result;

        // Handle failed payment verification.
        if (empty($result->data->code) || $result->data->code != 100) {
            $DB->update_record(
                'paygw_zarinpal',
                (object)[
                    'id' => $payment->id,
                    'status' => self::STATUS_ERROR,
                    'code' => $result->data->code ?? 0,
                    'data' => json_encode($paymentdata),
                    'updated_at' => time(),
                ],
            );

            return [
                'success' => false,
                'url' => '',
                'message' => get_string('payment_failed', 'paygw_zarinpal', ['payment_id' => $payment->id]),
            ];
        }

        // Save successful payment in system.
        $paymentid = payment_helper::save_payment(
            $payment->account_id,
            $payment->component,
            $payment->payment_area,
            $payment->item_id,
            $payment->user_id,
            $payment->amount,
            $payment->currency,
            'zarinpal',
        );

        // Update the ZarinPal payment record.
        $DB->update_record(
            'paygw_zarinpal',
            (object)[
                'id' => $payment->id,
                'payment_id' => $paymentid,
                'status' => self::STATUS_COMPLETED,
                'code' => $result->data->code,
                'ref_id' => $result->data->ref_id,
                'data' => json_encode($paymentdata),
                'updated_at' => time(),
            ],
        );

        // Deliver the order using the payment helper.
        payment_helper::deliver_order(
            $payment->component,
            $payment->payment_area,
            (int)$payment->item_id,
            $paymentid,
            (int)$payment->user_id,
        );

        $successurl = payment_helper::get_success_url(
            $payment->component,
            $payment->payment_area,
            (int)$payment->item_id,
        );

        return [
            'success' => true,
            'url' => $successurl,
            'message' => get_string('payment_successful', 'paygw_zarinpal'),
        ];
    }
}
