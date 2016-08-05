<?php

namespace SmsZilla\Adapter;

use SmsZilla\Adapter\AbstractAdapter;
use SmsZilla\ConfigurationException;
use SmsZilla\MessageInterface;
use SmsZilla\MessageModel;
use SmsZilla\SendingError;

/**
 * Adatpter for Cisco EHWIC and 880G for 3.7G (HSPA+)/3.5G (HSPA)
 * @link http://www.cisco.com/c/en/us/td/docs/routers/access/1800/1861/software/feature/guide/mrwls_hspa.html#wp1600033 Manual how to send SMS
 * Gateway suport connecting via SSH. In this case server must be authenticated by a public key.
 *
 * @author Jarek
 */
class CiscoAdapter extends AbstractAdapter {

    const ERROR_NOT_SAVED = 1;
    
    protected $params = [
        'router_slot' => 0,
        'wic_slot' => 1,
        'port' => 0,
        'use_ssh' => false,
        'ssh_host' => '',
        'ssh_login' => '',
    ];
    protected $command = 'cellular #ROUTER_SLOT#/#WIC_SLOT#/#PORT# gsm sms send #RECIPIENT# #MESSAGE#';
    protected $sshCommand = 'ssh #SSH_LOGIN#@#SSH_HOST# #COMMAND#';

    /**
     * Save message in file
     * @param MessageModel $message
     */
    public function send(MessageInterface $message, $skipErrors = true) {
        $this->clearErrors();

        $command = $this->prepareCommand($message->getText());

        foreach ($message->getRecipient() as $recipient) {
            // execute command
            $command = str_replace('#RECIPIENT#', $recipient, $command);

            $return = 0;
            $output = [];
            exec($command, $output, $return);

            if ($return !== 0) {
                $errorMsg = sprintf("Error while sending message to \"%s\".", $recipient);
                $this->addError(new SendingError($recipient, self::ERROR_NOT_SAVED, $errorMsg));
                if (!$skipErrors) {
                    throw new RuntimeException($errorMsg, self::ERROR_NOT_SAVED);
                }
            }
        }
        return $this->getErrors()->count() === 0;
    }

    /**
     * 
     * @param string $message SMS message
     * @return string Parsed command
     * @throws ConfigurationException
     */
    private function prepareCommand($message) {

        $tokens = [];
        $params = array_merge($this->params, ['message' => escapeshellarg($message)]);
        foreach ($params as $name => $value) {
            $token = '#' . strtoupper($name) . '#';
            if (is_scalar($value)) {
                $tokens[$token] = $value;
            }
        }

        $command = str_replace(array_keys($tokens), array_values($tokens), $this->command);

        try {
            $useSsh = (bool) $this->getParam('use_ssh');
        }
        catch (ConfigurationException $exp) {
            $useSsh = false;
        }

        if ($useSsh) {
            $sshHost = (string) $this->getParam('ssh_host');
            $sshLogin = (string) $this->getParam('ssh_login');
            if (empty($sshHost) || empty($sshLogin)) {
                throw new ConfigurationException(__CLASS__ . ' is not configured properly. If SSH is enabled then parameters "ssh_host" and "ssh_login" must be set.');
            }

            $tokens['#COMMAND#'] = $command;

            $command = str_replace(array_keys($tokens), array_values($tokens), $this->sshCommand);
        }

        return $command;
    }

}
