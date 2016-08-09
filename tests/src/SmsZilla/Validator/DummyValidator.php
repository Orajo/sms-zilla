<?php

/**
 * Description of DummyValidator
 *
 * @author Jarek
 */
class DummyValidator implements SmsZilla\Validator\ValidatorInterface {
    //put your code here
    public function format($value) {
        return $value;
    }

    public function getMessages() {
        return [];
    }

    public function isValid($value) {
        return true;
    }

}
