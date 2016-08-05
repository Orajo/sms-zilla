<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

namespace SmsSender\Adapter;

use SmsSender\ConfigurationException;
use SmsSender\MessageInterface;
use SmsSender\MessageModel;
use SmsSender\SendingErrorInterface;

/**
 * Description of MockGateway
 *
 * @author Jarek
 */
abstract class AbstractGateway implements GeatewayInterface {
    
    protected $params = [];
    
    protected $errors;
    
    /**
     * Constructor
     * @param array $params Adapter parameters
     */
    public function __construct($params = null) {
        $this->clearErrors();
        
        if (is_array($params) && !empty($params)) {
            $this->setParams($params);
        }
    }
    
    /**
     * Send message with given SMS gateway.
     * Remember to clean previews errors et the very begining of sending process!
     * 
     * @param MessageModel $message
     * @param bool $skipErrors If false error during sending message breaks sending others
     * @return bool true if success, false if error
     */
    abstract function send(MessageInterface $message, $skipErrors = true);
    
    /**
     * Returns value gateway param
     * @param string $name
     * @return mixed Value of the $name parameter
     */
    public function getParam($name) {
        if (empty($name) || !is_string($name)) {
            throw new \InvalidArgumentException('Paramater name must be not empty string');
        }
        
        if (isset($this->params[$name]) || array_key_exists($name, $this->params)) {
            return $this->params[$name];
        }
        throw new ConfigurationException(sprintf('Parameter %s doeasn\'t exists', $name));
    }

    /**
     * Sets options of gateway
     * @param array $params List of options as associative array name => value
     * @return mixed Value of the $name parameter
     */
    public function setParams($params) {
        if (!is_array($params)) {
            throw new \InvalidArgumentException('Paramater $params must be an array.');
        }
        
        foreach ($params as $name => $value) {            
            if (isset($this->params[$name]) || array_key_exists($name, $this->params)) {
                $this->params[$name] = $value;
            }
            else {
                throw new ConfigurationException(sprintf('Parameter "%s" doesn\'t exists', $name));
            }
        }
    }
    
    /**
     * Return all errors, that occired during sending process.
     * Error code depends of selected Adapter
     * @return ArrayObject
     */
    public function getErrors() {
        return $this->errors;
    }
    
    /**
     * Adds error to the list
     * @param SendingErrorInterface $error
     * @return AbstractGateway
     */
    protected function addError(SendingErrorInterface $error) {
        $this->errors->append($error);
        return $this;
    }
    
    /**
     * Reset errors list
     * @return AbstractGateway
     */
    protected function clearErrors() {
        $this->errors = new \ArrayObject();
        return $this;
    }
}
