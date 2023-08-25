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
use SmsZilla\ConfigurationException;
use SmsZilla\MessageInterface;
use SmsZilla\SmsMessageModel;
use SmsZilla\SendingError;

/**
 * Send message through SmsCenter.pl provider.
 * 
 * Require Mobitex SMS Api
 * 
 * @link https://smscenter.pl Service homepage
 * @link http://smscenter.pl/specyfikacja_mt.pdf SmsCenter.pl API documentation
 * @link https://github.com/mlebkowski/mobitex Mobitex SMS Api
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
     *
     * @param SmsMessageModel $message
     * @return bool
     */
    public function send(MessageInterface $message, bool $skipErrors = true): bool
    {

        $client = $this->getClient();
        
        foreach ($message->getRecipient() as $recipient) {
            try {
                $client->sendMessage($recipient, $message->getText());
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
     * @throws ConfigurationException
     */
    private function getClient(): Sender
    {
        $login = $this->getParam('login');
        $password = $this->getParam('password');
        $sender = $this->getParam('sender');

        if (empty($sender) || (empty($login) || empty($password))) {
            throw new ConfigurationException(__CLASS__ . ' is not configured properly. Please set "sender" and "login" and "password" parameters properly.');
        }

        return Sender::create($login, md5($password), $sender);
    }

}
