<?php
/**
 * Please override this settings in config.local.php, and set at least your
 * phone number and SmsApi.pl security token.
 */

$distFilePath = __DIR__ . '/config.local.php';
if (is_file($distFilePath)) {
    return include $distFilePath;
} else {
    return array(
        'message' => 'Message content with ĄŻŹĆŚĘŁÓŃążźćśęłóń',
        'smsapi_token' => '',
        'phones' => ['48123456789', '48121212121'],
        'my_phone' => '',
        'smscenter_login' => '',
        'smscenter_password' => '',
        'smscenter_sender' => '',
        'infobip_token' => '',
        'clickatell_token' => '',
    );
}
