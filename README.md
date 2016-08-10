# SmsZilla

[![Latest Stable Version](https://img.shields.io/packagist/v/orajo/sms-zilla.svg?style=flat-square)](https://packagist.org/packages/orajo/sms-zilla)
[![License](http://img.shields.io/badge/license-MIT-red.svg?style=flat-square)](https://packagist.org/packages/orajo/sms-zilla)

PHP library for sending SMS messages using various gateways. It's simple but flexible. Allows sending for more than one recipient at a time.

Currently supported gateways:
* SMSApi.pl,
* SmsCenter.pl,
* Cisco EHWIC and 880G for 3.7G (HSPA+)/3.5G (HSPA) device,
* text files generator (form gateways which monitor sahred folder,
* mock (dummy gateway for testing)

The library can be easily extended to support new gateways or integrated into your application, such as filtering of recipients based on consent to receiving SMS messages.

======

Installation
------------

use [composer](http://getcomposer.org/)

    {
        "require": {
            "orajo/sms-zilla": "1.*"
        }
    }

or

    php composer.phar require orajo/sms-zilla

Usage
------------

```php
$smsSender = new SmsZilla\SmsSender(new SmsZilla\Adapter\MockAdapter());

// default validator supports Polish numbers (PL) without country code
// others must be given with + and country code
// adding one recipient
$smsSender->setRecipient('605123456');
// adding more recipients (with and without country code)
$smsSender->setRecipient(['511654321', '48511654987', '+41751654987']);

// Add recipient from other then default country.
// If region is changed then country code (+41) can be ommited.
$smsSender->getValidator()->setDefaultRegion('CH);
$smsSender->setRecipient('987654321');

$smsSender->setText("Message text");
$result = $smsSender->send();
```

Author
------

Jaroslaw Wasilewski <orajo@windowslive.com>.

License
-------

[MIT](http://opensource.org/licenses/MIT)
