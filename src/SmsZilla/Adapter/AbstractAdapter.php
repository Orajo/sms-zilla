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
use SmsZilla\SendingErrorInterface;

/**
 * Abstract class of gateway adapter
 * @subpackage Adapter
 * @author Jarosław Wasilewski <orajo@windowslive.com>
 */
abstract class AbstractAdapter implements AdapterInterface {

    /**
     * Adapter configuration
     * @var array
     */
    protected $params = [];

    /**
     * List of errors while sendind messages to recipients
     * @var \ArrayObject
     */
    protected $errors = null;

    /**
     * Constructor
     * @param array $params Adapter parameters
     */
    public function __construct($params = []) {
        $this->clearErrors();

        if (!empty($params)) {
            $this->setParams($params);
        }
    }

    /**
     * Send message with given SMS gateway.
     * Remember to clean previews errors et the very begining of sending process!
     *
     * @param SmsMessageModel $message
     * @param bool $skipErrors If false error during sending message breaks sending others
     * @return bool true if success, false if error
     */
    abstract function send(MessageInterface $message, bool $skipErrors = true): bool;

    /**
     * Returns value gateway param
     *
     * @param string $name
     * @return mixed Value of the $name parameter
     */
    public function getParam(string $name) {
        if (empty($name) || !is_string($name)) {
            throw new \InvalidArgumentException('Parameter name must be not empty string');
        }

        if (isset($this->params[$name]) || array_key_exists($name, $this->params)) {
            return $this->params[$name];
        }
        throw new ConfigurationException(sprintf('Parameter %s doesn\'t exists', $name));
    }

    /**
     * Sets options of gateway
     *
     * @param array $params List of options as associative array name => value
     * @return AdapterInterface Value of the $name parameter
     * @throws ConfigurationException
     */
    public function setParams(array $params): AdapterInterface {
        foreach ($params as $name => $value) {
            if (isset($this->params[$name]) || array_key_exists($name, $this->params)) {
                if (is_array($this->params[$name])) {
                    if (is_array($value)) {
                        $this->params[$name] = array_merge($this->params[$name], $value);
                    } else {
                        throw new ConfigurationException(sprintf('Parameters type mismatch. Given type is "%s", expected type is "%s"', gettype($this->params[$name]) ,gettype($value)));
                    }
                } else {
                    $this->params[$name] = $value;
                }
            } else {
                throw new ConfigurationException(sprintf('Parameter "%s" doesn\'t exists', $name));
            }
        }
        return $this;
    }

    /**
     * Return all errors, that occurred during sending process.
     * Error code depends on selected Adapter
     * @return \ArrayObject
     */
    public function getErrors(): \ArrayObject {
        return $this->errors;
    }

    /**
     * Adds error to the list
     * @param SendingErrorInterface $error
     * @return AbstractAdapter
     */
    protected function addError(SendingErrorInterface $error): AbstractAdapter
    {
        $this->errors->append($error);
        return $this;
    }

    /**
     * Reset list of errors
     *
     * @return AbstractAdapter
     */
    protected function clearErrors(): AbstractAdapter
    {
        $this->errors = new \ArrayObject();
        return $this;
    }
}
