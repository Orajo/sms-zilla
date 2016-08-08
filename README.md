# SmsZilla
======
[![Latest Stable Version](https://img.shields.io/packagist/v/orajo/sms-zilla.svg?style=flat-square)](https://packagist.org/packages/orajo/sms-zilla)
[![License](http://img.shields.io/badge/license-MIT-red.svg?style=flat-square)](https://packagist.org/packages/orajo/sms-zilla)
======


PHP library for sending SMS messages using various gateways. It's simple but flexible. Allows sending for more than one recipient at a time.

Currently supported gateways:
* SMSApi.pl,
* SmsCenter.pl,
* Cisco EHWIC and 880G for 3.7G (HSPA+)/3.5G (HSPA) device,
* text files generator (form gateways which monitor sahred folder,
* mock (dummy gateway for testing)

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
$smsSender->setCountryCode(56);

// adding one recipient
$smsSender->setRecipient('987654321');
// adding more recipients (with and without country code)
$smsSender->setRecipient(['987654321', '48321654987']);

$smsSender->setText("Message text");
$result = $smsSender->send();
```

Author
------

Jaroslaw Wasilewski <orajo@windowslive.com>.

License
-------

[MIT](http://opensource.org/licenses/MIT)
