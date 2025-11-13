# درگاه پرداخت مودل: زرین‌پال

این پلاگین امکان پرداخت از طریق درگاه پرداخت زرین‌پال را به زیرسیستم پرداخت‌های مودل اضافه می‌کند.

فراگیران می‌توانند اقلامی را که از پرداخت‌های مودل استفاده می‌کنند خریداری کنند (به عنوان مثال، ثبت‌نام‌های پولی مانند روش ثبت‌نام سبد خرید، یا سایر اجزای یکپارچه‌شده با `core_payment`). مدیران می‌توانند زرین‌پال را به عنوان یک درگاه پیکربندی کنند، محیط (Sandbox/Production) را انتخاب کنند و تراکنش‌ها را به صورت ایمن پردازش نمایند.


## الزامات
1. مودل نسخه 3.11 یا بالاتر
2. PHP نسخه 7.4 یا بالاتر
3. یک حساب کاربری معتبر زرین‌پال با شناسه پذیرنده (Merchant ID)


## ترجمه‌های موجود
- انگلیسی (en)
- فارسی (fa)


## نصب
1. فایل ".zip" آخرین نسخه درگاه را دانلود کنید.
2. نصب از مسیر: مدیریت سایت > پلاگین‌ها > نصب پلاگین‌ها


## پیکربندی
مسیر: مدیریت سایت > پرداخت‌ها > حساب‌های پرداخت > زرین‌پال (تنظیمات)

گزینه‌های زیر را تنظیم کنید:
- شناسه پذیرنده (Merchant ID): شناسه پذیرنده زرین‌پال شما.
- محیط (Environment): "Production" را برای تراکنش‌های واقعی یا "Sandbox" را برای تست انتخاب کنید.


## استفاده
پس از فعال‌سازی و پیکربندی، زرین‌پال به عنوان یک درگاه موجود در هر جایی که از پرداخت‌های مودل استفاده می‌شود، ظاهر خواهد شد. به عنوان مثال:
- ثبت‌نام‌های پولی با استفاده از روش ثبت‌نام سبد خرید (`enrol_cart`)، در صورت نصب و پیکربندی.
- سایر اجزای مودل که پرداخت را از طریق `core_payment` آغاز می‌کنند.

فراگیران فرآیند پرداخت را تکمیل می‌کنند، به زرین‌پال هدایت می‌شوند تا پرداخت کنند، و به مودل بازگردانده می‌شوند که در آنجا نتیجه تأیید می‌شود. در صورت موفقیت‌آمیز بودن پرداخت، دسترسی به صورت خودکار بر اساس زمینه خرید (مثلاً ثبت‌نام در دوره) اعطا می‌شود.


## مجوز
منتشرشده تحت مجوز GNU GPL نسخه 3 یا بالاتر: http://www.gnu.org/copyleft/gpl.html


# Moodle Payment Gateway: ZarinPal

This plugin adds support for accepting payments via the ZarinPal payment gateway in Moodle's core Payments subsystem.

Learners can purchase items that use Moodle Payments (for example, paid enrolments such as the Cart enrolment method, or other components integrated with `core_payment`). Administrators can configure ZarinPal as a gateway, choose environment (Sandbox/Production), and process transactions securely.


## Requirements
1. Moodle version 3.11 or later
2. PHP 7.4 or later
3. A valid ZarinPal account with a Merchant ID


## Translations available
- English (en)
- Persian (fa)


## Installation
1. Download the latest release ".zip" file of the gateway.
2. Install from: Site administration > Plugins > Install plugins


## Configuration
Open: Site administration > Payments > Payment accounts > ZarinPal (Settings)

Set the following options:
- Merchant ID: Your ZarinPal Merchant ID.
- Environment: Select "Production" for live transactions or "Sandbox" for testing.


## Usage
Once enabled and configured, ZarinPal will appear as an available gateway wherever Moodle Payments is used. For example:
- Paid enrolments using the Cart enrolment method (`enrol_cart`), if installed and configured.
- Other Moodle components that initiate payments via `core_payment`.

Learners complete checkout, are redirected to ZarinPal to pay, and are brought back to Moodle where the result is verified. On successful payment, access is granted automatically according to the purchasing context (e.g., course enrolment).


## License
Released under the GNU GPL v3 or later: http://www.gnu.org/copyleft/gpl.html
