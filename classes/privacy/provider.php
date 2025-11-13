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

namespace paygw_zarinpal\privacy;

use coding_exception;
use context;
use core_payment\privacy\paygw_provider;
use core_privacy\local\metadata\null_provider;
use core_privacy\local\request\writer;
use dml_exception;
use stdClass;

/**
 * Class provider
 * Implements privacy-related methods for the ZarinPal payment gateway plugin.
 *
 * This class manages the export, deletion, and privacy-related functionality for
 * payment records associated with the ZarinPal payment gateway.
 */
class provider implements null_provider, paygw_provider {
    /**
     * Get the language string identifier from the component's language file.
     *
     * This explains why this plugin does not store any user-related data
     * for privacy compliance purposes.
     *
     * @return string The language string identifier for metadata privacy explanation.
     */
    public static function get_reason(): string {
        return 'privacy:metadata';
    }

    /**
     * Export all user data for the specified payment record within the given context.
     *
     * This method retrieves payment-related data from the database and exports
     * it to the appropriate location within the provided context.
     *
     * @param context $context The Moodle context for the data.
     * @param array $subcontext An array representing the location within the context.
     * @param stdClass $payment The payment record for which data is being exported.
     *
     * @return void
     *
     * @throws coding_exception If there is a coding error.
     * @throws dml_exception If there is a database-related issue.
     */
    public static function export_payment_data(context $context, array $subcontext, stdClass $payment) {
        global $DB;

        // Add the plugin name to the subcontext for better categorization.
        $subcontext[] = get_string('pluginname', 'paygw_zarinpal');

        // Retrieve the ZarinPal-specific payment record from the database.
        $record = $DB->get_record('paygw_zarinpal', ['payment_id' => $payment->id]);

        // Prepare the data for export.
        $data = (object)[
            'authority' => $record->authority,
            'status' => $record->status,
            'data' => $record->data,
        ];

        // Write the data to the export location using the writer utility.
        writer::with_context($context)->export_data($subcontext, $data);
    }

    /**
     * Delete all user data related to the given payments.
     *
     * This method deletes ZarinPal-related payment records from the database
     * using a SQL query that selects the payment IDs.
     *
     * @param string $paymentsql A SQL query that selects the `id` field for payments to delete.
     * @param array $paymentparams An array of parameters for the SQL query.
     *
     * @return void
     *
     * @throws dml_exception If there is a database-related issue.
     */
    public static function delete_data_for_payment_sql(string $paymentsql, array $paymentparams) {
        global $DB;

        // Execute the delete operation on the ZarinPal payment table.
        $DB->delete_records_select('paygw_zarinpal', "payment_id IN ({$paymentsql})", $paymentparams);
    }
}
