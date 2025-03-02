<?php
/**
 * SmsZilla
 * PHP library for sending SMS through various gateways.
 * @link https://github.com/Orajo/sms-zilla Homepage
 * @copyright Copyright (c) 2016 Jarosław Wasilewski <orajo@windowslive.com>
 * @license https://opensource.org/licenses/mit-license.php MIT License
 */

namespace SmsZilla\Adapter;

use Gsmservice\Gateway\Client;
use Gsmservice\Gateway\Models\Components\SmsMessage;
use Gsmservice\Gateway\Models\Components\SmsType;
use Gsmservice\Gateway\Models\Errors\ErrorResponseThrowable;
use Gsmservice\Gateway\Models\Errors\SDKException;
use SmsZilla\ConfigurationException;
use SmsZilla\MessageInterface;
use SmsZilla\SendingError;
use SmsZilla\SmsMessageModel;

/**
 * Send message through SzybkiSms.pl provider.
 *
 * Require PHP API from SzybkiSms.pl service {@link https://github.com/gsmservice-pl/messaging-sdk-php}
 * @link https://www.szybkisms.pl Service homepage
 * @link https://szybkisms.pl/dokumentacja-api/ API documentation
 *
 * @subpackage Adapter
 * @author Jarosław Wasilewski <orajo@windowslive.com>
 */
class SzybkiSmsAdapter extends AbstractAdapter {

    protected $params = [
        'token' => null,
        'sender' => null,
    ];

    /**
     * Send message through SzybkiSms.pl gateway
     *
     * @param SmsMessageModel $message
     * @param bool $skipErrors
     * @return bool
     * @throws ConfigurationException
     */
    public function send(MessageInterface $message, bool $skipErrors = true): bool
    {

        $client = $this->getClient();
        $sender = $this->getParam('sender');
        
        foreach ($message->getRecipient() as $recipient) {
            try {
                $response = $client->outgoing->sms->send(
                    [
                        new SmsMessage(
                            recipients: [$recipient],
                            message: $message->getText(),
                            sender: $sender,
                            type: SmsType::SmsPro,
                            unicode: true,
                            flash: false,
                            date: null,
                        )
                    ]
                );

                if ($response->messages === null) {
                    $this->addError(new SendingError($recipient, $response->statusCode, $response->rawResponse));
                    if (!$skipErrors) {
                        throw new \RuntimeException('Unknown error', $response->statusCode);
                    }
                }
            } catch (ErrorResponseThrowable | SDKException | \Exception $e) {
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
     *
     * @return Client
     * @throws ConfigurationException
     */
    private function getClient(): Client
    {
        $token = $this->getParam('token');
        if (empty($token)) {
            throw new ConfigurationException(__CLASS__ . ' is not configured properly. Please set "token" parameter properly.');
        }
        return Client::builder()->setSecurity($token)->build();
    }

}
