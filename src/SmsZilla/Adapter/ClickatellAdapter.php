<?php
/**
 * SmsZilla
 * PHP library for sending SMS through various gateways.
 * @link https://github.com/Orajo/sms-zilla Homepage
 * @copyright Copyright (c) 2016 Jarosław Wasilewski <orajo@windowslive.com>
 * @license https://opensource.org/licenses/mit-license.php MIT License
 */

namespace SmsZilla\Adapter;

use SmsZilla\ConfigurationException;
use SmsZilla\MessageInterface;
use SmsZilla\SmsMessageModel;
use SmsZilla\SendingError;

/**
 * Send message through Clickatell provider.
 *
 * Require Clickatell PHP Api
 *
 * @link https://www.clickatell.com Service homepage
 * @link https://www.clickatell.com/developers/ Clickatell API documentation
 * @link https://github.com/clickatell/clickatell-php Clickatell PHP Api
 * @link https://www.clickatell.com/developers/scripts/php-library/ PHP library documentation
 *
 * @subpackage Adapter
 * @author Jarosław Wasilewski <orajo@windowslive.com>
 */
class ClickatellAdapter extends AbstractAdapter {

    protected $params = [
        'token' => null,
    ];

    /**
     * Send message through Clickatell.com gateway
     * @param SmsMessageModel $message
     * @return bool
     */
    public function send(MessageInterface $message, $skipErrors = true) {

        // check clickatell limit
        if (count($message->getRecipients()) > 600) {
            $this->addError(new SendingError($message->getRecipients(),
                    'You have excedded provider limit of messages to send in one time.'
                    , $message->error));
        }

        $gateway = $this->getClient();

        try {
            $response = $gateway->sendMessage($message->getRecipients(), $message->getText());
            foreach ($response as $message) {
                if ($message->errorCode) {
                    $this->addError(new SendingError($message->destination, $message->errorCode, $message->error));
                }
            }
        }
        catch (Exception $e) {
            $this->addError(new SendingError('', $e->getCode(), $e->getMessage()));
            if (!$skipErrors) {
                throw new \RuntimeException($e->getMessage(), $e->getCode());
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
        $token = $this->getParam('token');

        if (empty($token)) {
            throw new ConfigurationException(__CLASS__ . ' is not configured properly. Please set "token" parameter properly.');
        }

        return new \Clickatell\Api\ClickatellRest($token);
    }

}
