<?php
/**
 * SmsZilla
 * PHP library for sending SMS through various gateways.
 * @link https://github.com/Orajo/sms-zilla Homepage
 * @copyright Copyright (c) 2016 Jarosław Wasilewski <orajo@windowslive.com>
 * @license https://opensource.org/licenses/mit-license.php MIT License
 */

namespace SmsZilla\Validator;

use \SmsZilla\Validator\ValidatorInterface;

/**
 * LibphonenumberValidator is a phone number validator and formatter.
 * 
 * This class depends on giggsey/libphonenumber-for-php - PHP port 
 * of Google library for parsing, formatting, and validating international 
 * phone numbers.
 *
 * @link https://github.com/googlei18n/libphonenumber Original Google library 
 * for parsing, formatting, and validating international phone numbers.
 * @link https://github.com/giggsey/libphonenumber-for-php PHP port of Google lib
 * @author Jarek
 */
class LibphonenumberValidator implements ValidatorInterface {
    private $instance = null;
 
    /**
     * 2 letters region name.
     * @var string
     */
    private $defaultRegion = 'PL';

    /**
     *
     * @var type 
     */
    private $numberParsed = null;
    
    /**
     * List of validation errors
     * @var array
     */
    private $messages = [];

    public function __construct($region = '') {
        if (!class_exists('\libphonenumber\PhoneNumberUtil')) {
            throw new \RuntimeException('giggsey/libphonenumber-for-php library not installed');
        }
        
        if (!empty($region)) {
            $this->setDefaultRegion($region);
        }
        
        $this->instance = \libphonenumber\PhoneNumberUtil::getInstance();
    }
    
    public function isValid($number) {
        $this->parseNumber($number);
        if ($this->numberParsed === null) {
            // $number cannot be parsed whitch means that is wrong
            $this->messages[] = 'Phone number has wrong format and cannot be parsed';
        }
        elseif ($this->instance->isValidNumber($this->numberParsed)) {
            if ($this->instance->getNumberType($this->numberParsed) == \libphonenumber\PhoneNumberType::MOBILE) {
                return true;
            }
            else {
                $this->messages[] = 'Phone number is not mobile';
            }
        }
        else {
            $this->messages[] = 'Phone number is not valid';
        }
        return false;
    }
    
    /**
     * Format phone number according to E164 standard but without leading
     * + (plus) sign.
     * 
     * @param string $number Can be ''|null if isValid was called first
     * @return string
     * @throws \RuntimeException
     */
    public function format($number) {
        if (!($this->numberParsed instanceof \libphonenumber\PhoneNumber)) {
            throw new \RuntimeException(__CLASS__ . '::format() method must be called after '.__CLASS__.'::isValid()');
        }

        $retVal = $this->instance->format($this->numberParsed, \libphonenumber\PhoneNumberFormat::E164);
        
        // remove + sign
        return substr($retVal, 1);
    }

    /**
     * Return list of error messages
     * @return array
     */
    public function getMessages() {
        return $this->messages;
    }

    /**
     * Return default region code.
     * @return string
     */
    public function getDefaultRegion() {
        return $this->defaultRegion;
    }

    /**
     * Sets default region code.
     * Region code is used only if it cennot be determined from phone number.
     * 
     * @param string $region
     * @return $this
     */
    public function setDefaultRegion($region) {
        $this->defaultRegion = strtoupper($region);
        return $this;
    }
    
    /**
     * Parses the phone number
     * @param string $number
     * @uses self::$numberParsed
     */
    private function parseNumber($number) {

        try {
            $this->numberParsed = $this->instance->parse($number, $this->defaultRegion);
        } catch (\libphonenumber\NumberParseException $e) {
            $this->numberParsed = null;
        }
    }
}
