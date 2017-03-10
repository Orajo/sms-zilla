<?php
/**
 * SmsZilla
 * PHP library for sending SMS through various gateways.
 * @link https://github.com/Orajo/sms-zilla Homepage
 * @copyright Copyright (c) 2016 Jarosław Wasilewski <orajo@windowslive.com>
 * @license https://opensource.org/licenses/mit-license.php MIT License
 */

namespace SmsZilla\Adapter;

use SerwerSMS\SerwerSMS;
use SmsZilla\ConfigurationException;
use SmsZilla\MessageInterface;
use SmsZilla\SmsMessageModel;
use SmsZilla\SendingError;

/**
 * Send message through SerwerSms.pl provider.
 *
 * Require PHP API from SerwerSms service {@link https://github.com/SerwerSMSpl/serwersms-php-api-v2}
 *
 * @link https://www.serwersms.pl Service homepage
 * @link http://dev.serwersms.pl/ SerwerSms API documentation
 * @link https://github.com/SerwerSMSpl/serwersms-php-api-v2 PHP API for SerwerSms service
 *
 * @subpackage Adapter
 * @author Jarosław Wasilewski <orajo@windowslive.com>
 */
class SerwerSmsAdapter extends AbstractAdapter {

    const MAX_RECIPIENS = 100000;

    protected $params = [
        'login' => '',
        'passwd' => '',
        'sender' => null, // ECO
        'extra' => array(
            'test' => false,
            'details' => true,
            'utf' => true
        ),
    ];

    /**
     * Send message through SerwerSms gateway
     * @param SmsMessageModel $message
     * @return bool
     */
    public function send(MessageInterface $message, $skipErrors = true) {
        if (count($message->getRecipient()) > self::MAX_RECIPIENS) {
            throw new ConfigurationException('Too many recipiens. For SerwerSms gateway limit is ' . self::MAX_RECIPIENS);
        }

        $client = $this->getClient();

        $extraParams = $this->getParam('extra');
        try {
            $response = $client->messages->sendSms(
                $message->getRecipients(),
                $message->getText(),
                $this->getParam('sender'),
                $this->getParam('extra')
            );

            if ((int)$response->unsent > 0) {
                foreach ($response->items as $item) {
                    // @see http://dev.serwersms.pl/https-api-v2/wysylanie-wiadomosci-sms-o-jednakowej-tresci
                    if (in_array($item->status === 'unsent')) {
                        $this->addError(new SendingError($item->phone, $item->error_code, $item->error_message . 'id:' . $item->id));
                        if (!$skipErrors) {
                            throw new \RuntimeException($item->error_message, $item->error_code);
                        }
                    }
                }
            }
        }
        catch (Exception $e) {
            $this->addError(new SendingError('', $e->getCode(), $e->getMessage()));
            if (!$skipErrors) {
                throw new \RuntimeException($e->getMessage());
            }
        }
        return $this->getErrors()->count() === 0;
    }

    /**
     * Prepare client configuration
     * @return \SerwerSMS\SerwerSMS
     * @throws ConfigurationException
     */
    private function getClient() {
        $login = $this->getParam('login');
        $password = $this->getParam('passwd');

        if (empty($login) || empty($password)) {
            throw new ConfigurationException(__CLASS__ . ' is not configured properly. Please set "login" and "passwd" parameters properly.');
        }

        return new SerwerSMS($login, $password);
    }

}
