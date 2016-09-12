<?php

namespace SmsZilla\Adapter;

/**
 * This class is for easier testing FileAdapter.
 * Creates files with messages named like recipient's phone number.
 *
 * @author Jarosław Wasilewski <orajo@windowslive.com>
 */
class FileAdapterMock extends FileAdapter {
    protected function getFileName($recipient) {
        return $recipient . self::FILE_EXT;
    }
}
