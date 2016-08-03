<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

namespace SmsSender;

/**
 *
 * @author Jarek
 */
interface SendingErrorInterface {
    
    public function __construct($recipient, $code, $message = '');
            
    public function getCode();
    
    public function getCode();
    
    public function getMessage();
}
