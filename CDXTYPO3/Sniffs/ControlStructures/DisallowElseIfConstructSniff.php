<?php
namespace Codappix\CDXTYPO3\Sniffs\ControlStructures;

use PHP_CodeSniffer\Files\File;
use PHP_CodeSniffer\Sniffs\Sniff;

class DisallowElseIfConstructSniff implements Sniff
{
    public function register()
    {
        return [T_ELSE];
    }

    public function process(File $phpcsFile, $stackPtr)
    {
        $result = $phpcsFile->findNext(T_IF, ($stackPtr + 1), ($stackPtr + 3));
        if ($result !== false) {
            $phpcsFile->addError('Usage of "ELSE IF" not allowed. Use "ELSEIF" instead.', $stackPtr);
        }
    }
}
