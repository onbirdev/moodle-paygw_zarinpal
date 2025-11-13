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

use core\notification;
use paygw_zarinpal\external\process;
use paygw_zarinpal\zarinpal_helper;

require_once(__DIR__ . '/../../../config.php');
require_login();

global $DB, $USER, $PAGE, $OUTPUT;

// Retrieve required parameters from the URL.
$id = required_param('id', PARAM_INT);
$authority = required_param('Authority', PARAM_TEXT);
$status = required_param('Status', PARAM_TEXT);

// Get payment information from the database using the authority.
$payment = zarinpal_helper::get_payment($authority);

// If the payment is not found, throw an error.
if (!$payment) {
    throw new moodle_exception('error_payment_not_found', 'paygw_zarinpal');
}

// Check if the logged-in user is the same user who initiated the payment.
if ($USER->id != $payment->user_id) {
    throw new moodle_exception('error_user_not_allowed', 'paygw_zarinpal');
}

// Process the payment with the provided authority and status.
$response = process::execute($authority, $status);

// If the payment was successful, display success message and redirect the user.
if ($response['success']) {
    notification::success($response['message']);
    redirect($response['url']);
}

// If the payment failed, display an error message.
$PAGE->set_context(context_system::instance());
$PAGE->set_pagelayout('base');
$PAGE->set_url(new moodle_url('/payment/gateway/zarinpal/process.php'));
$PAGE->set_title(get_string('payment_result', 'paygw_zarinpal'));
echo $OUTPUT->header();
echo $OUTPUT->render_from_template('paygw_zarinpal/error', [
    'message' => $response['message'],
]);
echo $OUTPUT->footer();
