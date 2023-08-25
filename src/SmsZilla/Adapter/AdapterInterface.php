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
 * Interface of the adapter to handle specified SMS gateway
 * @subpackage Adapter
 * @author Jarosław Wasilewski <orajo@windowslive.com>
 */
interface AdapterInterface{
    
    /**
     * Sets options of gateway
     *
     * @param array $params List of options as associative array name => value
     * @return AdapterInterface
     */
    public function setParams(array $params): AdapterInterface;

    /**
     * Returns value gateway param
     *
     * @param string $name
     * @return mixed Value of the $name parameter
     */
    public function getParam(string $name);

    /**
     * Send message with given SMS gateway
     *
     * @param MessageModel $message
     * @param bool $skipErrors If false error during sending message breaks sending others
     * @return bool true if success, false if error
     */
    public function send(MessageInterface $message, bool $skipErrors = true): bool;
    
    /**
     * Return error messages.
     * Error code depends of selected Adapter
     * @return \ArrayObject
     */
    public function getErrors(): \ArrayObject;
}
