<?php
/**
 * SmsZilla
 * PHP library for sending SMS through various gateways.
 * @link https://github.com/Orajo/sms-zilla Homepage
 * @copyright Copyright (c) 2016 Jarosław Wasilewski <orajo@windowslive.com>
 * @license https://opensource.org/licenses/mit-license.php MIT License
 */

namespace SmsZilla\Adapter;

use Mobitex\Exception;
use Mobitex\Sender;
use SMSApi\Client;
use SmsZilla\ConfigurationException;
use SmsZilla\MessageInterface;
use SmsZilla\SmsMessageModel;
use SmsZilla\SendingError;

/**
 * Send message through SmsApi.pl provider.
 * 
 * Require PHP API from SmsApi.pl service {@see https://github.com/smsapi/smsapi-php-client}
 * 
 * @link https://www.smsapi.pl Service homepage
 * @link https://www.smsapi.pl/assets/files/api/SMSAPI_http.pdf SmsApi.pl API documentation
 * @link https://github.com/smsapi/smsapi-php-client PHP API form SmsApi.pl service
 * 
 * @subpackage Adapter
 * @author Jarosław Wasilewski <orajo@windowslive.com>
 */
class SmsCenterAdapter extends AbstractAdapter {

    protected $params = [
        'login' => null,
        'password' => null,
        'sender' => null,
    ];
    
    /**
     * Send message through SmsCenter.pl gateway
     * @param SmsMessageModel $message
     */
    public function send(MessageInterface $message, $skipErrors = true) {

        $mobitex = $this->getClient();
        
        foreach ($message->getRecipient() as $recipient) {
            try {
                $mobitex->sendMessage($recipient, $message->getText());
            }
            catch (Exception $e) {
                $this->addError(new SendingError($recipient, $e->getCode(), $e->getMessage()));
                if (!$skipErrors) {
                    throw new \RuntimeException($e->getMessage(), $e->getCode());
                }
            }
        }
        return $this->getErrors()->count() === 0;
    }

    /**
     * Prepare client configuration
     * @return Client
     * @throws ConfigurationException
     */
    private function getClient() {
        $login = $this->getParam('login');
        $password = $this->getParam('password');
        $sender = $this->getParam('sender');

        if (empty($sender) || (empty($login) || empty($password))) {
            throw new ConfigurationException(__CLASS__ . ' is not configured properly. Please set "sender" and "login" and "password" parameters properly.');
        }

        return Sender::create($login, md5($password), $sender);
    }

}
