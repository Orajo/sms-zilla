<?php
/**
 * SmsZilla
 * PHP library for sending SMS through various gateways.
 * @link https://github.com/Orajo/sms-zilla Homepage
 * @copyright Copyright (c) 2016 Jarosław Wasilewski <orajo@windowslive.com>
 * @license https://opensource.org/licenses/mit-license.php MIT License
 */

namespace SmsZilla\Adapter;

use SmsZilla\MessageInterface;

/**
 * Description of MockGateway
 *
 * @subpackage Adapter
 * @author Jarosław Wasilewski <orajo@windowslive.com>
 */
class MockAdapter extends AbstractAdapter {
    
    /**
     * @var array Sent messages
     */
    private $sentMessages = array();
    
    /**
     * Stores messages on stack
     * @param MessageInterface $message
     * @param bool $skipErrors
     * @return boolean Always true
     */
    public function send(MessageInterface $message, $skipErrors = true) {
        $this->sentMessages[] = $message;
        return true;
    }

    /**
     * @return array
     */
    public function getSentMessages() {
        return $this->sentMessages;
    }
}
