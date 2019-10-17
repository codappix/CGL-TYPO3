<?php
namespace Codappix\CDXTYPO3\Sniffs\Files;

use PHP_CodeSniffer\Files\File;
use PHP_CodeSniffer\Sniffs\Sniff;

class FilenameSniff implements Sniff
{
    public function register()
    {
        return [T_OPEN_TAG];
    }

    public function process(File $phpcsFile, $stackPtr)
    {
        $tokens     = $phpcsFile->getTokens();
        $findTokens = [
            T_CLASS,
            T_INTERFACE,
            T_TRAIT
        ];

        $stackPtr = $phpcsFile->findNext($findTokens, ($stackPtr + 1));

        // Check if we hit a file without a class. Raise a warning and return.
        if ($stackPtr === false) {
            $warning = 'Its recommended to use only PHP classes and avoid non-class files.';
            $phpcsFile->addWarning($warning, 1, 'Non-ClassFileFound');
            return;
        }

        $classNameToken = $phpcsFile->findNext(T_STRING, $stackPtr);
        $className      = $tokens[$classNameToken]['content'];
        $fullPath       = basename($phpcsFile->getFileName());
        $fileName       = substr($fullPath, 0, strrpos($fullPath, '.'));
        if ($fileName === '') {
            // No filename probably means STDIN, so we can't do this check.
            return;
        }

        if (strcmp($fileName, $className) !== 0) {
            $error = 'The classname is not equal to the filename; found "%s" as classname and "%s" for filename.';
            $data  = array(
                      $className,
                      $fileName . '.php',
                     );
            $phpcsFile->addError($error, $stackPtr, 'ClassnameNotEqualToFilename', $data);
        }

        if (strtolower($fileName) === $fileName) {
            $error = 'The filename has to be in UpperCamelCase; found "%s".';
            $data  = array($fileName . '.php');
            $phpcsFile->addError($error, $stackPtr, 'LowercaseFilename', $data);
        }

        if ($tokens[$stackPtr]['code'] === T_INTERFACE) {
            if (stristr($fileName, 'Interface') === false) {
                $error = 'The file contains an interface but the string "Interface"' .
                   ' is not part of the filename; found "%s", but expected "%s".';
                $data  = [
                    $fileName . '.php',
                    $className . '.php',
                ];
                $phpcsFile->addError($error, $stackPtr, 'InterfaceNotInFilename', $data);
            }
        }
    }
}
