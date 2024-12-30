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
use core_payment\form\account_gateway;
use stdClass;

/**
 * Class gateway
 * Handles the configuration and management of the ZarinPal payment gateway.
 */
class gateway extends \core_payment\gateway {
    /**
     * Get the list of currencies supported by this gateway.
     *
     * @return array An array of supported currency codes.
     */
    public static function get_supported_currencies(): array {
        // ZarinPal supports only Iranian Rial (IRR).
        return ['IRR'];
    }

    /**
     * Add configuration fields to the gateway instance configuration form.
     *
     * This method is responsible for adding necessary fields to the form for setting up
     * the ZarinPal payment gateway. The form allows administrators to input gateway-specific settings.
     *
     * @param account_gateway $form The gateway configuration form.
     *
     * @return void
     *
     * @throws coding_exception If there is an issue accessing the form.
     */
    public static function add_configuration_to_gateway_form(account_gateway $form): void {
        $mform = $form->get_mform();

        // Add the merchant ID field to the form.
        $mform->addElement('text', 'merchant_id', get_string('merchant_id', 'paygw_zarinpal'));
        $mform->setType('merchant_id', PARAM_TEXT);

        // Add a dropdown select element to the form for choosing the environment (Product or Sandbox).
        // The 'product' option corresponds to the live environment, and 'sandbox' is for testing purposes.
        $mform->addElement('select', 'environment', get_string('environment', 'paygw_paypal'), [
            'production' => get_string('env_production', 'paygw_zarinpal'),
            'sandbox' => get_string('env_sandbox', 'paygw_zarinpal'),
        ]);
        // Add a help button to the 'environment' field for additional explanation to users.
        $mform->addHelpButton('environment', 'environment', 'paygw_zarinpal');
    }

    /**
     * Validate the data submitted in the gateway configuration form.
     *
     * Ensures that required fields are filled out correctly and that the gateway
     * can only be enabled if the configuration is complete.
     *
     * @param account_gateway $form The gateway configuration form instance.
     * @param stdClass $data The submitted form data.
     * @param array $files Any uploaded files associated with the form.
     * @param array $errors Reference to the errors array for reporting validation issues.
     *
     * @return void
     *
     * @throws coding_exception If there is an issue with form validation logic.
     */
    public static function validate_gateway_form(
        account_gateway $form,
        stdClass $data,
        array $files,
        array &$errors
    ): void {
        // Ensure the merchant ID is provided if the gateway is enabled.
        if ($data->enabled && empty($data->merchant_id)) {
            $errors['enabled'] = get_string('gatewaycannotbeenabled', 'payment');
        }
    }
}
