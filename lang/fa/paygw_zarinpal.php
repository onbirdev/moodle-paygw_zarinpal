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

$string['env_production'] = 'واقعی';
$string['env_sandbox'] = 'آزمایشی';
$string['environment'] = 'محیط';
$string['environment_help'] =
    'در این بخش می‌توانید محیط پرداخت خود را انتخاب کنید. محیط واقعی برای پرداخت‌های واقعی و محیط آزمایشی برای تست و آزمایش استفاده می‌شود.';
$string['error_payment_not_found'] =
    'پرداخت موردنظر یافت نشد. لطفاً کد Authority ارائه‌شده را بررسی کرده و دوباره تلاش کنید.';
$string['error_unknown'] = 'خطای نامشخص';
$string['error_user_not_allowed'] = 'شما مجاز به دسترسی به این پرداخت نیستید.';
$string['gatewaydescription'] = 'پرداخت آنلاین با کلیه کارت‌های بانکی';
$string['gatewayname'] = 'زرین‌پال';
$string['merchant_id'] = 'کد پذیرنده';
$string['param_authority'] = 'کد یکتای Authority که توسط درگاه پرداخت زرین‌پال ارائه می‌شود.';
$string['param_component'] = 'نام کامپوننت مرتبط با پرداخت، به عنوان مثال، "mod_assign" برای فعالیت تکلیف.';
$string['param_description'] = 'توضیحی کوتاه و قابل فهم درباره تراکنش یا پرداخت انجام‌شده.';
$string['param_itemid'] = 'شناسه آیتم در محدوده ناحیه کامپوننت، که پرداخت به آن مرتبط است.';
$string['param_paymentarea'] = 'ناحیه مربوط به پرداخت در کامپوننت، مانند "submission" یا "grading".';
$string['param_status'] = 'وضعیت پرداخت که توسط درگاه پرداخت بازگشت داده شده است.';
$string['payment_failed'] =
    '<p><b>پرداخت ناموفق</b></p><p>در صورت کسر مبلغ از حساب شما طی ۷۲ ساعت آینده به حساب شما بازگردانده خواهد شد.</p><p>کدپیگیری: <b>{$a->payment_id}</b></p>';
$string['payment_result'] = 'نتیجه پرداخت';
$string['payment_successful'] = 'پرداخت شما با موفقیت انجام شد.';
$string['pluginname'] = 'زرین‌پال';
$string['pluginname_desc'] =
    'با استفاده از پلاگین زرین‌پال می‌توانید مبالغ پرداختی را از طریق درگاه زرین‌پال دریافت کنید.';
$string['privacy:metadata:paygw_zarinpal'] = 'جزئیات اطلاعات پرداخت زرین پال.';
$string['privacy:metadata:paygw_zarinpal:user_id'] = 'شناسه کاربر در مودل.';
$string['return_message'] = 'پیام، معمولاً شامل جزئیات خطا یا اطلاعات موفقیت.';
$string['return_success'] = 'نشان می‌دهد که آیا فرآیند با موفقیت انجام شده است یا خیر.';
$string['return_url'] = 'آدرس برای هدایت در صورت موفقیت‌آمیز بودن درخواست.';
