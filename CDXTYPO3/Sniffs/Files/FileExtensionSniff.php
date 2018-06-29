<?php
namespace Codappix\CDXTYPO3\Sniffs\Files;

use PHP_CodeSniffer\Files\File;
use PHP_CodeSniffer\Sniffs\Sniff;

class FileExtensionSniff implements Sniff
{
    public function register()
    {
        return [T_OPEN_TAG];
    }

    public function process(File $phpcsFile, $stackPtr)
    {
        // Make sure this is the first PHP open tag so we don't process
        // the same file twice.
        $prevOpenTag = $phpcsFile->findPrevious(T_OPEN_TAG, ($stackPtr - 1));
        if ($prevOpenTag !== false) {
            return;
        }

        $fileName  = $phpcsFile->getFileName();
        $extension = substr($fileName, strrpos($fileName, '.'));

        if ($extension !== '.php') {
            $error = 'Extension for PHP files is always ".php". Found "' .
                $extension . '" file; use ".php" extension instead';
            $phpcsFile->addError($error, $stackPtr, 'WrongFileExtension');
        }
    }
}
