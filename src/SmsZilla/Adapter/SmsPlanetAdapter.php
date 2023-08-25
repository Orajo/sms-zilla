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
use SmsZilla\SendingError;
use SmsZilla\SmsMessageModel;
use Laminas\Http\Client;
use Laminas\Http\Request;
use Laminas\Http\Response;

/**
 * Send message through smsplanet.pl provider.
 *
 * @link https://smsplanet.pl/ Service homepage
 * @link https://smsplanet.pl/doc/slate/index.html API documentation (includs PHP examples)
 *
 * @subpackage Adapter
 * @author Jarosław Wasilewski <orajo@windowslive.com>
 */
class SmsPlanetAdapter extends AbstractAdapter {

    protected $params = [
        'token' => '',
        'password' => '',
        'sender' => 'TEST',
        'to' => [],
        'subject' => '',
        'msg' => '',
        'test' => false
    ];

    private const ENDPOINT_URL = 'https://api2.smsplanet.pl/sms';

    /**
     * Send message through smsplanet.pl gateway
     *
     * @param SmsMessageModel $message
     * @param bool $skipErrors
     * @return bool
     * @throws ConfigurationException
     */
    public function send(MessageInterface $message, bool $skipErrors = true): bool
    {

        $sender = $this->getParam('sender');

        $request = $this->getRequest();
        $body = [
            'key' => $this->getParam('token'),
            'password' => $this->getParam('password'),
            'from' => $sender,
            'to' => $message->getRecipients(),
            'msg' => $message->getText(),
            'test' => (int)$this->getParam('test')
        ];

        foreach ($body as $kye => $value) {
            if (is_array($value)) {
                foreach ($value as $subValue) {
                    $request->getQuery()->set($kye, $subValue);
                }
            } else {
                $request->getQuery()->set($kye, $value);
            }
        }
        try {
            $client = new Client();
            $response = $client->send($request);
            $status = $response->getStatusCode();

            if ($status === 200) { // OK code
                $this->decodeResponse($response);
            } else {
                $this->addError(new SendingError(json_encode($body), $status, 'Response error' . $response->toString()));
            }
        }
        catch (\Exception $e) {
            $this->addError(new SendingError(json_encode($body), $e->getCode(), $e->getMessage()));
            if (!$skipErrors) {
                throw new \RuntimeException($e->getMessage(), $e->getCode(), true);
            }
        }
        return $this->getErrors()->count() === 0;
    }

    /**
     * Prepare request configuration
     *
     * @return Request
     * @throws ConfigurationException
     */
    private function getRequest(): Request
    {
        $password = $this->getParam('password');
        $token = $this->getParam('token');

        if (empty($token) && (empty($password))) {
            throw new ConfigurationException(__CLASS__ . ' is not configured properly. Please set "token" and "password" parameters properly.');
        }

        $request = new Request();
        $request->setUri(self::ENDPOINT_URL);
        $request->setMethod(Request::METHOD_GET);
        return $request;
    }

    /**
     * Error structure
     * {"errorMsg":"Niepoprawny klucz - sprawdź swój klucz API.","errorCode":101}
     */
    private function decodeError(Response $response): void
    {
        $data = json_decode($response->getBody(), false);

        if (is_object($data)) {
            $code = $data->errorCode;
            $msg = $data->errorMsg;
            $this->addError(new SendingError('', $code, $msg));
        }
        else {
            $this->addError(new SendingError('', $response->getStatusCode(), $response->getContent()));
        }
    }

    /**
     * Decodes response
     *
     * Response has JSON structure:
     * {"messageId":191919}
     *
     * @param Response $response
     * @return string
     */
    private function decodeResponse($response) {
        $result = $response->getBody();
        $data = json_decode($result, false);
        if (!isset($data->messageId)) {
            $this->decodeError($response);
        }
        return $result;
    }
}
