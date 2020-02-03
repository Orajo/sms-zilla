<?php
/**
 * SmsZilla
 * PHP library for sending SMS through various gateways.
 * @link https://github.com/Orajo/sms-zilla Homepage
 * @copyright Copyright (c) 2016 Jarosław Wasilewski <orajo@windowslive.com>
 * @license https://opensource.org/licenses/mit-license.php MIT License
 */

namespace SmsZilla\Adapter;

use \SmsZilla\ConfigurationException;
use \SmsZilla\MessageInterface;
use \Twilio\Rest\Client;

/**
 * Send message through Twilio provider.
 *
 * Require PHP API from SmsApi.pl service {@link https://github.com/smsapi/smsapi-php-client}
 *
 * @link https://www.twilio.com Service homepage
 * @link https://www.twilio.com/docs/ Twilio documentation homepage
 * @link https://github.com/twilio/twilio-php PHP API for Twilio.com service
 *
 * @subpackage Adapter
 * @author Jarosław Wasilewski <orajo@windowslive.com>
 */
class TwilioAdapter extends AbstractAdapter {

    /**
     * Twilio uses two typoes of authentication:
     * - per account credentials (Account SID and Auth Token)
     * - per Api Key credentials; authentication based on dedication for concreate application: Key SID, Api Secread and Account SID)
     * @var array
     */
    protected $params = [
        'api_key' => null,
        'api_secret' => null,
        'account_sid' => null,
        'number' => null,
    ];

    public function send(MessageInterface $message, $skipErrors = true) {
        $client = $this->getClient();
        $myNumber = $this->getParam('number');
        if (empty($myNumber)) {
            throw new ConfigurationException(__CLASS__ . ' is not configured properly. Please set "number" parameters properly. See ');
        }

        foreach ($message->getRecipients() as $recipient) {
            try {
                $response = $client->messages->create(
                    $recipient,
                        array(
                            'from' => $myNumber, // From a valid Twilio number
                            'body' => $message->getText()
                        )
                );
                var_dump($response);
                $prop = $response->properties;
                if ($prop->status !== 'queued') {
                    $this->addError(new \SmsZilla\SendingError($recipient, $prop->errorCode, $prop->errorMessage));
                    if (!$skipErrors) {
                        throw new \RuntimeException($prop->errorMessage, $prop->errorCode, true);
                    }
                }
            }
            catch(\Twilio\Exceptions\TwilioException $e) {
                $this->addError(new \SmsZilla\SendingError($recipient, $e->getCode(), $e->getMessage()));
                if (!$skipErrors) {
                    throw new \RuntimeException($e->getMessage(), $e->getCode(), true);
                }
            }
        }
        var_dump($this->getErrors());
        return $this->getErrors()->count() === 0;
    }

    private function getClient() {
        $apiKey = $this->getParam('api_key');
//        $apiSecret = $this->getParam('api_secret');
        $accountSid = $this->getParam('account_sid');

        if (empty($apiKey) || empty($accountSid)) {
            throw new ConfigurationException(__CLASS__ . ' is not configured properly. Please set "api_key" and "api_secret" and "account_sid" parameters properly.');
        }

        return new Client($accountSid, $apiKey);
    }
}
