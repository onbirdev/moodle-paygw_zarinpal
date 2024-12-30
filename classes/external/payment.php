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

namespace paygw_zarinpal\external;

use core_payment\helper;
use external_api;
use external_function_parameters;
use external_single_structure;
use external_value;
use paygw_zarinpal\zarinpal_helper;

defined('MOODLE_INTERNAL') || die();

require_once($CFG->libdir . '/externallib.php');

/**
 * Payment API class for ZarinPal payment gateway.
 *
 * This class provides methods to handle the parameters, execution, and return structure
 * for interacting with the ZarinPal payment gateway in Moodle.
 */
class payment extends external_api {
    /**
     * Defines the input parameters for the payment execution function.
     *
     * @return external_function_parameters The parameters structure.
     */
    public static function execute_parameters(): external_function_parameters {
        return new external_function_parameters([
            'component' => new external_value(PARAM_COMPONENT, get_string('param_component', 'paygw_zarinpal')),
            'paymentarea' => new external_value(PARAM_AREA, get_string('param_paymentarea', 'paygw_zarinpal')),
            'itemid' => new external_value(PARAM_INT, get_string('param_itemid', 'paygw_zarinpal')),
            'description' => new external_value(PARAM_TEXT, get_string('param_description', 'paygw_zarinpal')),
        ]);
    }

    /**
     * Executes the payment request for the ZarinPal gateway.
     *
     * Validates the parameters, retrieves configuration and payable details,
     * and sends a payment request to the ZarinPal gateway.
     *
     * @param string $component The component associated with the payment (e.g., "mod_cart").
     * @param string $paymentarea The payment area within the component (e.g., "checkout").
     * @param int $itemid The ID of the item in the payment context.
     * @param string $description A description of the payment.
     *
     * @return array An array containing the result of the payment request:
     *               - success (bool): Indicates if the request was successful.
     *               - url (string): The URL to redirect to for completing the payment.
     *               - message (string): Additional information or error message.
     *
     * @throws \invalid_parameter_exception If the provided parameters are invalid.
     * @throws \moodle_exception For errors during payment processing.
     */
    public static function execute(string $component, string $paymentarea, int $itemid, string $description): array {
        // Validate the provided parameters.
        self::validate_parameters(self::execute_parameters(), [
            'component' => $component,
            'paymentarea' => $paymentarea,
            'itemid' => $itemid,
            'description' => $description,
        ]);

        // Load the gateway configuration for the given component and payment area.
        $config = (object) helper::get_gateway_configuration($component, $paymentarea, $itemid, 'zarinpal');

        // Calculate the payment amount including any surcharges.
        $payable = helper::get_payable($component, $paymentarea, $itemid);
        $surcharge = helper::get_gateway_surcharge('zarinpal');
        $amount = helper::get_rounded_cost($payable->get_amount(), $payable->get_currency(), $surcharge);

        // Initialize the ZarinPal payment helper with the merchant ID.
        $zarinpal = new zarinpal_helper($config->merchant_id, $config->environment === 'sandbox');

        // Send a payment request to ZarinPal and return the response.
        return $zarinpal->request_payment(
            $payable->get_account_id(),
            $amount,
            $payable->get_currency(),
            $component,
            $paymentarea,
            $itemid,
            $description
        );
    }

    /**
     * Defines the return structure for the payment execution function.
     *
     * @return external_single_structure The return structure.
     */
    public static function execute_returns(): external_single_structure {
        return new external_single_structure([
            'success' => new external_value(PARAM_BOOL, get_string('return_success', 'paygw_zarinpal')),
            'url' => new external_value(PARAM_RAW, get_string('return_url', 'paygw_zarinpal')),
            'message' => new external_value(PARAM_RAW, get_string('return_message', 'paygw_zarinpal')),
        ]);
    }
}
