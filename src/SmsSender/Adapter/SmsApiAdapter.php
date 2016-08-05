<?php
/*
 * https://www.smsapi.pl/assets/files/api/SMSAPI_http.pdf
 */

namespace SmsSender\Adapter;

use SMSApi\Api\SmsFactory;
use SMSApi\Client;
use SMSApi\Exception\SmsapiException;
use SmsSender\ConfigurationException;
use SmsSender\MessageInterface;
use SmsSender\MessageModel;
use SmsSender\SendingError;

/**
 * Send message through SmsApi.pl provider.
 *
 * @author Jarek
 */
class SmsApiAdapter extends AbstractAdapter {

    protected $params = [
        'login' => null,
        'passwd_hash' => null,
        'sender' => 'ECO',
        'token' => null,
    ];

    /**
     * Save message in file
     * @param MessageModel $message
     */
    public function send(MessageInterface $message, $skipErrors = true) {
        $smsapi = new SmsFactory();
        $smsapi->setClient($this->getClient());

        $actionSend = $smsapi->actionSend();

        // Name of the sender must be defined in SMSApi admin panel first.
        // If $sender is set to "ECO", then the ECO SMS will be send
        $sender = $this->getParam('sender');
        if (empty($sender)) {
            throw new ConfigurationException(__CLASS__ . ' is not configured properly. Please set "sender" parameter properly.');
        }
        $actionSend->setSender($sender);

        $actionSend->setText($message->getText());
        foreach ($message->getRecipient() as $recipient) {
            try {
                $actionSend->setTo($recipient); // Numer odbiorcy w postaci 48xxxxxxxxx lub xxxxxxxxx

                $response = $actionSend->execute();

                var_dump($response);
                foreach ($response->getList() as $status) {
                    // @see https://www.smsapi.pl/statusy-wiadomosci
                    if (in_array($status->getStatus(), [407, 406, 405, 401, 402])) {
                        $this->addError(new SendingError($status->getNumber(), $status->getStatus(), $status->getError()));
                        if (!$skipErrors) {
                            throw new \RuntimeException($e->getMessage());
                        }
                    }
                }
            }
            catch (SmsapiException $e) {
                $this->addError(new SendingError($recipient, $e->getCode(), $e->getMessage()));
                if (!$skipErrors) {
                    throw new \RuntimeException($e->getMessage());
                }
            }
        }
        return $this->getErrors()->count() === 0;
    }

    /**
     * 
     * @return SMSApi\Client
     * @throws ConfigurationException
     */
    private function getClient() {
        $login = $this->getParam('login');
        $passwordHash = $this->getParam('passwd_hash');
        $token = $this->getParam('token');

        if (empty($token) && (empty($login) || empty($passwordHash))) {
            throw new ConfigurationException(__CLASS__ . ' is not configured properly. Please set "token" or "login" and "passwd_pash" parameters properly.');
        }

        $client = null;
        if (!empty($token)) {
            $client = Client::createFromToken($token);
        }
        else {
            $client = new Client($login);
            $client->setPasswordHash($passwordHash);
        }
        return $client;
    }

}
