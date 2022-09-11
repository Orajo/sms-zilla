<?php

namespace SmsZilla\Adapter;

use Laminas\Http\Client;
use Laminas\Http\Request;
use Laminas\Http\Response;
use Laminas\Uri\Http;
use SmsZilla\ConfigurationException;
use SmsZilla\MessageInterface;
use SmsZilla\SendingError;

class OrangeSmsOffnetNatAdapter extends AbstractAdapter
{
    public const SERVICE_URL = 'https://apib2b.orange.pl/Messaging/v1/SMSOffnetNat';

    protected $params = [
        'token' => '',
        'sender' => '',
    ];

    /**
     * @throws ConfigurationException
     */
    function send(MessageInterface $message, $skipErrors = true)
    {
        $client = $this->getClient();

        foreach ($message->getRecipient() as $recipient) {
            try {
                $client->getRequest()->getQuery()->set('msg', $message->getText())->set('to', $recipient);
                $response = $client->send();
                if ($response->getStatusCode() !== Response::STATUS_CODE_200) {
                    $responseArray = json_decode($response->getBody(), true);
                    $this->addError(new SendingError(
                        $recipient,
                        $responseArray['code'],
                        $responseArray['message'] . '. ' . $responseArray['description']
                    ));
                }
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

    private function getClient(): Client
    {
        //Sender of the SMS message. Max 11 character long alphanumerical string that describes the sender.
        //Whitespaces not allowed. In case if from is not defined the default customer number will be set.
        //The list of allowed strings is checked according to the contract.
        $sender = $this->getParam('sender');
        if (empty($sender)) {
            throw new ConfigurationException(__CLASS__ . ' is not configured properly. Please set "sender" parameter properly.');
        }

        $token = $this->getParam('token');
        if (empty($token)) {
            throw new ConfigurationException(__CLASS__ . ' is not configured properly. Please set "token" parameter properly.');
        }

        $client = new Client(self::SERVICE_URL);
        $client->setMethod(Request::METHOD_GET)->getRequest()->getQuery()
            ->set('from', $sender)
            ->set('apikey', $token);
        return $client;
    }
}
