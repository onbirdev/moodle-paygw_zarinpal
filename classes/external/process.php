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

use coding_exception;
use core_payment\helper;
use dml_exception;
use external_api;
use external_function_parameters;
use external_single_structure;
use external_value;
use invalid_parameter_exception;
use moodle_exception;
use paygw_zarinpal\zarinpal_helper;

defined('MOODLE_INTERNAL') || die();

require_once($CFG->libdir . '/externallib.php');

/**
 * Class process
 * Handles the processing of ZarinPal payment results.
 */
class process extends external_api {
    /**
     * Defines the input parameters for the payment status verification function.
     *
     * @return external_function_parameters The parameters structure.
     */
    public static function execute_parameters(): external_function_parameters {
        return new external_function_parameters([
            'Authority' => new external_value(PARAM_TEXT, get_string('param_authority', 'paygw_zarinpal')),
            'Status' => new external_value(PARAM_TEXT, get_string('param_status', 'paygw_zarinpal')),
        ]);
    }

    /**
     * Processes the payment result from the ZarinPal gateway.
     *
     * This method validates the payment parameters, retrieves the payment details,
     * loads the ZarinPal gateway configuration, and processes the payment status.
     *
     * @param string $authority The unique authority code provided by the ZarinPal gateway.
     * @param string $status The status of the payment returned by the ZarinPal gateway.
     *
     * @return array An associative array containing:
     *               - success (bool): Whether the operation was successful.
     *               - url (string): Redirect URL for successful payment (if applicable).
     *               - message (string): Error or informational message.
     *
     * @throws invalid_parameter_exception If the provided parameters are invalid.
     * @throws moodle_exception For any errors during the payment processing.
     * @throws coding_exception If the code encounters unexpected behavior.
     * @throws dml_exception If there are database-related issues.
     */
    public static function execute(string $authority, string $status): array {
        // Validate the provided parameters.
        self::validate_parameters(self::execute_parameters(), [
            'Authority' => $authority,
            'Status' => $status,
        ]);

        // Retrieve the payment record using the authority code.
        $payment = zarinpal_helper::get_payment($authority);
        if (!$payment) {
            return [
                'success' => false,
                'message' => get_string('error_payment_not_found', 'paygw_zarinpal'),
            ];
        }

        // Load the gateway configuration for the specific payment context.
        $config = (object) helper::get_gateway_configuration(
            $payment->component,
            $payment->payment_area,
            (int) $payment->item_id,
            'zarinpal',
        );

        // Initialize the ZarinPal helper.
        $zarinpal = new zarinpal_helper($config->merchant_id, $config->environment === 'sandbox');

        // Process the payment using the ZarinPal helper and return the result.
        return $zarinpal->process_payment($payment, $status);
    }

    /**
     * Defines the return structure for the payment processing function.
     *
     * @return external_single_structure The return structure, including:
     *         - success (bool): Whether the payment processing was successful.
     *         - url (string): The redirect URL for successful payments (if applicable).
     *         - message (string): Additional information or error message.
     */
    public static function execute_returns(): external_single_structure {
        return new external_single_structure([
            'success' => new external_value(PARAM_BOOL, get_string('return_success', 'paygw_zarinpal')),
            'url' => new external_value(PARAM_RAW, get_string('return_url', 'paygw_zarinpal')),
            'message' => new external_value(PARAM_RAW, get_string('return_message', 'paygw_zarinpal')),
        ]);
    }
}
