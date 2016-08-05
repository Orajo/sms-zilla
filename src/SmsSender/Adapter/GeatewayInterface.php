<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

namespace SmsSender\Adapter;

use SmsSender\MessageInterface;

/**
 *
 * @author Jarek
 */
interface GeatewayInterface{
    
    /**
     * Sets options of gateway
     * @param array $params List of options as associative array name => value
     * @return GeatewayInterface
     */
    public function setParams($params);

    /**
     * Returns value gateway param
     * @param string $name
     * @return mixed Value of the $name parameter
     */
    public function getParam($name);

    /**
     * Send message with given SMS gateway
     * @param MessageModel $message
     * @param bool $skipErrors If false error during sending message breaks sending others
     * @return bool true if success, false if error
     */
    public function send(MessageInterface $message, $skipErrors = true);
    
    /**
     * Return error messages.
     * Error code depends of selected Adapter
     * @return \ArrayObject
     */
    public function getErrors();
}
