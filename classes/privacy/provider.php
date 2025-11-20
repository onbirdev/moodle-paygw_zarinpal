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

use context;
use context_system;
use core_payment\privacy\paygw_provider;
use core_privacy\local\metadata\collection;
use core_privacy\local\request\approved_contextlist;
use core_privacy\local\request\approved_userlist;
use core_privacy\local\request\contextlist;
use core_privacy\local\request\core_userlist_provider;
use core_privacy\local\request\userlist;
use core_privacy\local\request\writer;
use stdClass;

/**
 * Class provider
 * Implements privacy-related methods for the ZarinPal payment gateway plugin.
 *
 * This class manages the export, deletion, and privacy-related functionality for
 * payment records associated with the ZarinPal payment gateway.
 */
class provider implements
    core_userlist_provider,
    paygw_provider,
    \core_privacy\local\metadata\provider,
    \core_privacy\local\request\plugin\provider {
    /**
     * Export all user data for the specified payment record within the given context.
     *
     * @param context $context The Moodle context for the data.
     * @param array $subcontext An array representing the location within the context.
     * @param stdClass $payment The payment record for which data is being exported.
     * @return void
     */
    public static function export_payment_data(context $context, array $subcontext, stdClass $payment): void {
        global $DB;

        // Add the plugin name to the subcontext for better categorization.
        $subcontext[] = get_string('pluginname', 'paygw_zarinpal');

        // Retrieve the ZarinPal-specific payment record from the database.
        $record = $DB->get_record('paygw_zarinpal', ['payment_id' => $payment->id]);

        // Prepare the data for export.
        $data = (object) [
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
     * @param string $paymentsql A SQL query that selects the `id` field for payments to delete.
     * @param array $paymentparams An array of parameters for the SQL query.
     * @return void
     */
    public static function delete_data_for_payment_sql(string $paymentsql, array $paymentparams): void {
        global $DB;

        // Execute the delete operation on the ZarinPal payment table.
        $DB->delete_records_select('paygw_zarinpal', "payment_id IN ({$paymentsql})", $paymentparams);
    }

    /**
     * Returns metadata about the data stored by the paygw_zarinpal plugin.
     *
     * @param collection $collection The metadata collection to add items to.
     * @return collection Updated metadata collection.
     */
    public static function get_metadata(collection $collection): collection {
        $collection->add_database_table(
            'paygw_zarinpal',
            [
                'user_id' => 'privacy:metadata:paygw_zarinpal:user_id',
            ],
            'privacy:metadata:paygw_zarinpal',
        );

        return $collection;
    }

    /**
     * Returns a list of contexts that contain user information for the specified user.
     *
     * @param int $userid The ID of the user whose data is being requested.
     * @return contextlist
     */
    public static function get_contexts_for_userid(int $userid): contextlist {
        $contextlist = new contextlist();
        $contextlist->add_system_context();
        return $contextlist;
    }

    /**
     * Export all user data for the specified user within the system context.
     *
     * @param approved_contextlist $contextlist The list of contexts for which data is being exported.
     * @return void
     */
    public static function export_user_data(approved_contextlist $contextlist): void {
        global $DB;

        $userid = $contextlist->get_user()->id;
        $sql = "SELECT zp.*, p.component, p.paymentarea, p.itemid
                FROM {paygw_zarinpal} zp
                JOIN {payments} p ON p.id = zp.payment_id
                WHERE zp.user_id = :user_id";
        $params = ['user_id' => $userid];
        $records = $DB->get_records_sql($sql, $params);

        foreach ($records as $record) {
            $context = context_system::instance();
            $subcontext = [
                get_string('pluginname', 'paygw_zarinpal'),
                $record->component,
                $record->paymentarea,
                $record->itemid,
            ];

            $data = (object) [
                'component' => $record->component,
                'payment_area' => $record->payment_area,
                'item_id' => $record->item_id,
                'user_id' => $record->user_id,
                'payment_id' => $record->payment_id,
                'amount' => $record->amount,
                'currency' => $record->currency,
                'status' => $record->status,
                'merchant_id' => $record->merchant_id,
                'authority' => $record->authority,
                'code' => $record->code,
                'ref_id' => $record->ref_id,
                'data' => $record->data,
                'created_at' => $record->created_at,
                'updated_at' => $record->updated_at,
            ];

            writer::with_context($context)->export_data($subcontext, $data);
        }
    }

    /**
     * Delete all user data for all users in the system context.
     *
     * @param context $context The system context.
     * @return void
     */
    public static function delete_data_for_all_users_in_context(context $context): void {
        global $DB;

        if ($context->contextlevel !== CONTEXT_SYSTEM) {
            return;
        }

        $DB->delete_records('paygw_zarinpal');
    }

    /**
     * Delete all user data related to the given user.
     *
     * @param approved_contextlist $contextlist The context list containing the user whose data should be deleted.
     * @return void
     */
    public static function delete_data_for_user(approved_contextlist $contextlist): void {
        global $DB;

        $userid = $contextlist->get_user()->id;
        $DB->delete_records('paygw_zarinpal', ['user_id' => $userid]);
    }

    /**
     * Retrieve and add users related to the current context to the user list.
     *
     * @param userlist $userlist The list of users to populate, based on the context.
     * @return void
     */
    public static function get_users_in_context(userlist $userlist): void {
        $context = $userlist->get_context();

        if ($context->contextlevel !== CONTEXT_SYSTEM) {
            return;
        }

        $sql = "SELECT DISTINCT zp.user_id
                FROM {paygw_zarinpal} zp
                JOIN {payments} p ON p.id = zp.payment_id";

        $userlist->add_from_sql('user_id', $sql, []);
    }

    /**
     * Deletes all user data related to the specified users.
     *
     * @param approved_userlist $userlist A list of approved users whose data should be deleted.
     * @return void
     */
    public static function delete_data_for_users(approved_userlist $userlist): void {
        global $DB;

        $context = $userlist->get_context();

        if ($context->contextlevel !== CONTEXT_SYSTEM) {
            return;
        }

        $userids = $userlist->get_userids();
        if (empty($userids)) {
            return;
        }

        [$usersql, $userparams] = $DB->get_in_or_equal($userids, SQL_PARAMS_NAMED);
        $DB->delete_records_select('paygw_zarinpal', "user_id {$usersql}", $userparams);
    }
}
