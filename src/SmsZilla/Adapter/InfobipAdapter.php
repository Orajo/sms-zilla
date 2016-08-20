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
use Zend\Http\Request;

/**
 * Send message through infobip.com provider.
 * 
 * @link http://www.infobip.com Service homepage
 * @link http://www.infobip.com.pl Service homepage in Poland
 * @link https://dev.infobip.com/docs/send-single-sms API documentation (including PHP examples)
 * 
 * @subpackage Adapter
 * @author Jarosław Wasilewski <orajo@windowslive.com>
 */
class InfobipAdapter extends AbstractAdapter {

    protected $params = [
        'sender' => 'InfoSMS',
        'token' => null,
    ];
    
    private $url = 'https://api.infobip.com/sms/1/text/single';
    
    private $errorCodes = [
        'BAD_REQUEST' => 400,
        'UNAUTHORIZED' => 401,
        'NOT_FOUND' => 404,
        'UNSUPPORTED_CONTENT_TYPE' => 415,
        'TOO_MANY_REQUESTS' => 429,
        'INTERNAL_SERVER_ERROR' => 500,
        'SERVICE_UNAVAILABLE' => 503,
    ];
    
    const STATUS_GROUPS_ACCEPTED = 0;	// Message is accepted.
    const STATUS_GROUPS_PENDING = 1; 	// Message is in pending status.
    const STATUS_GROUPS_UNDELIVERABLE = 2;// Message is undeliverable.
    const STATUS_GROUPS_DELIVERED = 3; 	// Message is delivered.
    const STATUS_GROUPS_EXPIRED = 4; 	// Message is expired.
    const STATUS_GROUPS_REJECTED = 5; 	// Message is rejected.
    
    
    /**
     * Send message through infobip.com gateway
     * @param SmsMessageModel $error
     * @return bool
     */
    public function send(MessageInterface $error, $skipErrors = true) {

        $sender = $this->getParam('sender');
        
        $request = $this->getRequest();
        $body = [
            'from' => $sender,
            'to' => $error->getRecipients(),
            'text' => $error->getText()
        ];
        
        $request->setContent(json_encode($body));
        try {
            $client = new \Zend\Http\Client();
            $response = $client->send($request);
            $status = $response->getStatusCode();

            if ($status !== 200) { // OK code
                $error = $this->decodeError($response);
            }
            else {
                $this->decodeResponse($response);
            }
        }
        catch (Exception $e) {
            $this->addError(new SendingError(json_encode($body), $e->getCode(), $e->getMessage()));
            if (!$skipErrors) {
                throw new \RuntimeException($e->getMessage());
            }
        }
        return $this->getErrors()->count() === 0;
    }

    /**
     * Prepare request configuration
     * 
     * @return SMSApi\Client
     * @throws ConfigurationException
     */
    private function getRequest() {
        
        $token = $this->getParam('token');

        if (empty($token)) {
            throw new ConfigurationException(__CLASS__ . ' is not configured properly. Please set "token" parameter properly.');
        }

        $request = new Request();
        $request->setUri($this->url);
        $request->setMethod(Request::METHOD_POST);

        $request->getHeaders()->addHeaders(array(
          'accept' => 'application/json',
          'content-type' => 'application/json',
          'authorization' => 'Basic ' . $token
        ));

        return $request;
    }

    /**
     * Error structure
     * {
     *  "requestError": {
     *      "serviceException": {
     *          "messageId": "RESOURCE_NOT_FOUND",
     *          "text": "Application or message with given ID cannot be found."
     *      }
     *  }
     * }
     *
     * @link https://dev.infobip.com/v1/docs/2fa-status-codes-and-error-details Status codes and error details
     * @param \Zend\Http\Response $response
     */
    private function decodeError(\Zend\Http\Response $response) {
        $data = json_decode($response->getContent());

        if (is_object($data)) {
            $code = $data->requestError->serviceException->messageId;
            $msg = $data->requestError->serviceException->text;
            $this->addError(new \SmsZilla\SendingError('', $this->errorCodes[$code], $msg));
        }
        else {
            $this->addError(new \SmsZilla\SendingError('', $response->getStatusCode(), $response->getContent()));
        }
    }
    
    /**
     * Decodes response
     * 
     * Response is has JSON structure:
     * {"messages":[
     *    {"to":"null",
     *        "status":{
     *            "groupId":5,
     *            "groupName":"REJECTED",
     *            "id":51,
     *            "name":"MISSING_TO",
     *            "description":"Missing destination.",
     *            "action":"Check to parameter."
     *        }
     *    }
     * ]}
     * 
     * @param \Zend\Http\Response $response
     * @return void
     */
    private function decodeResponse($response) {
        
        $data = json_decode($response->getContent());

        if (count($data->messages)> 0) {
            foreach ($data->messages as $message) {
                $this->parseResponseMessage($message);
            }
        }
    }
    
    /**
     * Parses response message. Optionally register errors.
     * 
     * @link https://dev.infobip.com/docs/send-sms-response Description of the response message
     * @param Object $msg Message object according to {@link https://dev.infobip.com/docs/send-sms-response}
     * @return boolean True if there are no errors
     */
    private function parseResponseMessage($msg) {
        if (in_array($msg->status->groupId, [
            self::STATUS_GROUPS_UNDELIVERABLE, self::STATUS_GROUPS_EXPIRED,
            self::STATUS_GROUPS_REJECTED])) {
            $code = $msg->status->id;
            $text = sprintf("Error id=%d (groupId=%d). Message: %s %s", 
                    $msg->status->id,
                    $msg->status->groupId,
                    $msg->status->description,
                    $msg->status->action);
            $this->addError(new SendingError($msg->to, $code, $text));
            return false;
        }
        return true;
    }
}
