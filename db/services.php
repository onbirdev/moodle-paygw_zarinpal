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

defined('MOODLE_INTERNAL') || die();

$functions = [
    'paygw_zarinpal_payment' => [
        'classname' => 'paygw_zarinpal\external\payment',
        'classpath' => '',
        'description' => 'Generates and returns the payment URL for processing the payment through ZarinPal.',
        'type' => 'read',
        'ajax' => true,
    ],
    'paygw_zarinpal_process' => [
        'classname' => 'paygw_zarinpal\external\process',
        'classpath' => '',
        'description' => 'Handles the completion of the payment process, including verification and status updates.',
        'type' => 'write',
        'ajax' => true,
    ],
];
