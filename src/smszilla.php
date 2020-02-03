#!/usr/bin/env php

<?php
/**
 * SmsZilla
 * PHP library for sending SMS through various gateways.
 * @link https://github.com/Orajo/sms-zilla Homepage
 * @copyright Copyright (c) 2016 Jarosław Wasilewski <orajo@windowslive.com>
 * @license https://opensource.org/licenses/mit-license.php MIT License
 */

/**
 * This file is a console interface for SmsZilla library.
 * Allows to send SMS messages using standard CLI console.
 */

require __DIR__.'/../vendor/autoload.php';

$options = parseMessageOptions();

if ($options['h']) {
    echo getConsoleUsage(true);
    exit(0);
}

send($options);


/**
 * Prints help
 *
 * @return string
 */
function getConsoleUsage($withBanner = false) {
    $help = '';
    if ($withBanner) {
        $help = "smszilla.php allows sending SMS messages through simple console command\n\n";
    }

    $help .= "Usage: php smszilla.php [-pProfileName] -nnumber -mmessage [-cpath_to_config_file] [-h] [-v]\n".
        "-p profilename\tName of the profile\n".
        "-n number\tRecipients phone numbers\n".
        "-m message\tmesage to send\n".
        "-c path_to_config_file\tconfig file\n".
        "-v\tVerbose mode\n";
        "-h\t\tThis help\n";

    return $help;
}

/**
 * Creates adapter
 * @global array $config
 * @param array $options
 * @return \SmsZilla\Adapter\AbstractAdapter
 */
function createAdapter($options) {
    $config = getConfig($options);

    if (empty($options['p'])) {
        $profile = $config['default'];
    }
    else {
        $profile = strtolower($options['p']);
    }

    if (isset($config['profiles'][$profile])) {
        $adapterClass = $config['profiles'][$profile]['adapter'];
        try {
            unset($config['profiles'][$profile]['adapter']);
            return new $adapterClass($config['profiles'][$profile]);
        }
        catch (\Exception $exp) {
            printError('Initiating adapter faild', $exp);
        }
    }
    else {
        printError('Profile ' . $profile . ' is udenfined');
    }
}

/**
 * Send message using chosen profile
 *
 * @param array $options
 */
function send($options) {

    $sender = new SmsZilla\SmsSender(createAdapter($options));
    $sender->setText($options['m']);
    $sender->setRecipient($options['n']);
    $sender->send();
    if ($options['v']) {
        if ($sender->getAdapter()->getErrors()->count()) {
            foreach ($sender->getAdapter()->getErrors() as $error) {
                print $error->getMessage();

            }
        }
        else {
            printf ("Message(s) to %s sended.\n", join(', ', $options['n']));
        }
    }
}

/**
 * Parses profile configuration.
 * Handles global (package) and local (given using "c" option) profile files.
 *
 * @param array $options
 * @return array
 */
function getConfig($options) {
    $config = include __DIR__.'/SmsZilla/Console/profiles.config.php';
    if (isset($options['c'])) {
        $configFilePath = trim($options['c']);
        if (file_exists($configFilePath)) {
            try {
                $localConfig = include $configFilePath;
                $config = array_merge($config, $localConfig);
            }
            catch(\Exception $exp) {
                printError("Error in config file " . $localConfig, $exp);
            }
        }
    }
    return $config;
}

/**
 * Print error message
 *
 * @param string $message
 * @param \Exception $exp
 */
function printError($message, \Exception $exp = null) {
    print 'Error: ' . $message;
    if ($exp) {
        print "\n";
        print $exp->getMessage();
    }
    print "\n\n";
    print getConsoleUsage();
    exit(1);
}

/**
 * Parses and validates console parameters
 *
 * @return array
 */
function parseMessageOptions() {
    $options = getopt("p::n:m:c::hv");

    $opt = [
        'p' => '',
        'n' => [],
        'm' => '',
        'c' => '',
        'h' => false,
        'v' => false,
    ];


    if (isset($options['h']) || count($options) === 0) {
        $opt['h'] = true;
        return $opt;
    }

    if (isset($options['n']) && !empty($options['n'])) {
        $opt['n'] = explode(',', $options['n']);
    }
    if (count($opt['n']) < 1) {
        printError('At least one recipient mus be provided');
    }

    if (isset($options['m']) && !empty($options['m'])) {
        $opt['m'] = $options['m'];
    }
    else {
        printError('Message cannot be empty');
    }

    if (isset($options['c']) && !empty($options['c'])) {
        $opt['c'] = $options['c'];
    }

    if (isset($options['p']) && !empty($options['p'])) {
        $opt['p'] = $options['p'];
    }

    if (isset($options['v'])) {
        $opt['v'] = true;
    }

    return $opt;
}
