<?php
return [
    'default' => 'mock',
    'profiles' => [
        'file' => [
            'adapter' => 'SmsZilla\Adapter\FileAdapter',
            'store_path' => __DIR__ . '/../data/sms'
        ],
        'mock' => [
            'adapter' => 'SmsZilla\Adapter\MockAdapter',
        ],
        'smsapi' => [
            'adapter' => 'SmsZilla\Adapter\SmsApiAdapter',
            'token' => 'CNgOwKgfihwxnS58QyYIpXRGQ5ekefhYy9YKCavl',
        ],
        'cisco' => [
            'adapter' => 'SmsZilla\Adapter\CiscoAdapter',
            'use_ssh' => true,
            'ssh_host' => '127.0.0.1',
            'ssh_login' => 'dummy'
        ],
        'smscenter' => [
            'adapter' => 'SmsZilla\Adapter\SmsCenterAdapter',
            'login' => 'orajo2',
            'password' => 'pE4.wU2$kB',
            'sender' => '48605171108'
        ],
        'infobip' => [
            'adapter' => 'SmsZilla\Adapter\InfobipAdapter',
            'sender' => 'InfoSMS',
            'token' => 'Yml0c2E6ZChFS2w4dy0=',
        ],
        'serwersms' => [
            'adapter' => 'SmsZilla\Adapter\SerwerSmsAdapter',
            'login' => 'orajo',
            'password' => 'Dodekorajo73',
            'sender' => null // ECO
        ]
    ],
];