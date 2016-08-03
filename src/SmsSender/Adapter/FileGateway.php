<?php
namespace SmsSender\Adapter;

use SmsSender\Adapter\AbstractGateway;
use SmsSender\ConfigurationException;
use SmsSender\MessageInterface;
use SmsSender\MessageModel;
use SmsSender\SendingError;

/**
 * Save message as file.
 * Create separate file for every recepient.
 *
 * @author Jarek
 */
class FileGateway extends AbstractGateway {
    const FILE_EXT = '.call';
    
    const ERROR_NOT_SAVED = 1;
    
    protected $params  = [
        'store_path' => null,
        'path_chmod' => 660,
        'format' => "[%s]" . PHP_EOL . "%s" . PHP_EOL,
    ];
    
    /**
     * Save message in file
     * @param MessageModel $message
     */
    public function send(MessageInterface $message, $skipErrors = true) {
        $this->clearErrors();
        
        $storePath = $this->getParam('store_path');
        if (empty($storePath)) {
            throw new ConfigurationException(__CLASS__ . ' is not configured properly. Please set "store_path" parameter.');
        }
        
        $pathChmod = $this->getParam('path_chmod');
        $dir = realpath($storePath);
        if (!is_dir($dir)) {
            if (!mkdir($dir, $pathChmod, true)) {
                return false;
            }
        }

        $format = $this->getParam('format');
        foreach ($message->getRecipient() as $recipient) {
            $save_path = $storePath . DIRECTORY_SEPARATOR . $recipient . self::FILE_EXT;
            $content = sprintf($format, $recipient, $message->getText());

            $return = file_put_contents($save_path, $content);
            if($return === false) {
                $errorMsg = sprintf("Wystąpił błąd podczas dodawania pliku \"%s\" z wiadomością SMS.", $save_path);
                $this->addError(new SendingError($recipient, self::ERROR_NOT_SAVED, $errorMsg));
                if (!$skipErrors){
                    throw new RuntimeException($errorMsg, self::ERROR_NOT_SAVED);
                }
            }
        }
        return true;
    }
}
