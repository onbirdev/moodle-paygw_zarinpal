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

$string['env_production'] = 'Production';
$string['env_sandbox'] = 'Sandbox';
$string['environment'] = 'Environment';
$string['environment_help'] =
    'In this section, you can choose your payment environment. The production environment is for live payments, and the sandbox environment is for testing and experimentation.';
$string['error_payment_not_found'] =
    'The payment could not be found. Please verify the provided Authority code and try again.';
$string['error_unknown'] = 'Unknown error';
$string['error_user_not_allowed'] = 'You are not authorized to access this payment.';
$string['gatewaydescription'] =
    'ZarinPal is an authorised payment gateway provider for processing credit card transactions.';
$string['gatewayname'] = 'ZarinPal';
$string['merchant_id'] = 'Merchant ID';
$string['param_authority'] = 'The unique authority code provided by the ZarinPal payment gateway.';
$string['param_component'] =
    'The name of the component associated with the payment, e.g., "mod_assign" for the assignment activity.';
$string['param_description'] = 'A brief and clear description of the transaction or payment.';
$string['param_itemid'] = 'The ID of the item in the context of the component area that the payment is linked to.';
$string['param_paymentarea'] = 'The payment area within the component, such as "submission" or "grading".';
$string['param_status'] = 'The status of the payment, as returned by the payment gateway.';
$string['payment_failed'] = '<p><b>Payment failed.</b></p><p>Tracking code: <b>{$a->payment_id}</b></p>';
$string['payment_result'] = 'Payment result';
$string['payment_successful'] = 'Your payment was successfully completed.';
$string['pluginname'] = 'ZarinPal';
$string['pluginname_desc'] = 'The ZarinPal plugin allows you to receive payments via ZarinPal.';
$string['privacy:metadata'] = 'The ZarinPal plugin does not store any personal data.';
$string['return_message'] = 'A message, typically containing error details or success information.';
$string['return_success'] = 'Indicates whether the process was successful or not.';
$string['return_url'] = 'The URL to redirect to when the request is successful.';
