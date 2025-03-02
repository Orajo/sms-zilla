<?php

declare(strict_types=1);

namespace SmsZilla\Adapter;

use HostedSms\SimpleApi\HostedSmsSimpleApi;
use SmsZilla\ConfigurationException;
use SmsZilla\MessageInterface;
use SmsZilla\SendingError;

class HostedSmsAdapter extends AbstractAdapter
{
    protected $params = [
        'login' => null,
        'password' => null,
        'sender' => null,
    ];

    /**
     * @inheritDoc
     */
    function send(MessageInterface $message, bool $skipErrors = true): bool
    {

        $client = $this->getClient();
        $sender = $this->getParam('sender');

        if (empty($sender)) {
            throw new ConfigurationException(__CLASS__ . ' is not configured properly. Please set "sender" parameter properly.');
        }
        foreach ($message->getRecipient() as $recipient) {
            try {
                $client->sendSms($sender, $recipient, $message->getText());
            } catch (\Exception $e) {
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
     * @return HostedSmsSimpleApi
     * @throws ConfigurationException
     */
    private function getClient(): HostedSmsSimpleApi
    {
        $login = $this->getParam('login');
        $password = $this->getParam('password');
        if ((empty($login) || empty($password))) {
            throw new ConfigurationException(__CLASS__ . ' is not configured properly. Please set "login" and "password" parameters properly.');
        }

        return new HostedSmsSimpleApi($login, $password);
    }
}