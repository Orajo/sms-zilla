<?php
/**
 * Please override this settings in config.local.php, and set at least your
 * phone number and SmsApi.pl security token.
 */

$config = array(
    'message' => 'Message content with ĄŻŹĆŚĘŁÓŃążźćśęłóń',
    'smsapi_token' => '',
    'phones' => ['48123456789', '48121212121'],
    'wrong_phone' => '48251365', // wrong phone number
    'my_phone' => '',
    'smscenter_login' => '',
    'smscenter_password' => '',
    'smscenter_sender' => '',
    'infobip_token' => '',
    'clickatell_token' => '',
    'serwersms_login' => 'demo',
    'serwersms_password' => 'demo',
    'serwersms_sender' => null,
    'serwersms_test' => false,
    'orange_token' => '',
    'orange_sender' => '',
);

$local_config = [];
$distFilePath = __DIR__ . '/config.local.php';
if (is_file($distFilePath)) {
    $local_config = include $distFilePath;
}
return array_merge($config, $local_config);
