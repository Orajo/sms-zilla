<?php
return [
    'default' => 'mock',
    'profiles' => [
        'file' => [
            'adapter' => 'SmsZilla\Adapter\FileAdapter',
            'store_path' => __DIR__ . '/../../data/sms'
        ],
        'mock' => [
            'adapter' => 'SmsZilla\Adapter\MockAdapter',
        ],
        'smsapi' => [
            'adapter' => 'SmsZilla\Adapter\SmsApiAdapter',
            'token' => '',
        ],
        'cisco' => [
            'adapter' => 'SmsZilla\Adapter\CiscoAdapter',
            'use_ssh' => true,
            'ssh_host' => '127.0.0.1',
            'ssh_login' => 'dummy'
        ],
        'smscenter' => [
            'adapter' => 'SmsZilla\Adapter\SmsCenterAdapter',
            'login' => '',
            'password' => '',
            'sender' => ''
        ],
        'infobip' => [
            'adapter' => 'SmsZilla\Adapter\InfobipAdapter',
            'sender' => 'InfoSMS',
            'token' => '',
        ],
        'serwersms' => [
            'adapter' => 'SmsZilla\Adapter\SerwerSmsAdapter',
            'login' => '',
            'password' => '',
            'sender' => null // ECO
        ],
        'orange' => [
            'adapter' => \SmsZilla\Adapter\OrangeSmsOffnetNatAdapter::class,
            'token' => '',
            'sender' => ''
        ],
        'smsplanet' => [
            'adapter' => \SmsZilla\Adapter\SmsPlanetAdapter::class,
            'token' => '',
			'password' => '',
            'sender' => ''
        ]
    ],
];
