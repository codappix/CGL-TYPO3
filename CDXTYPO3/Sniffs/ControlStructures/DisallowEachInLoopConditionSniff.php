<?php
namespace Codappix\CDXTYPO3\Sniffs\ControlStructures;

use PHP_CodeSniffer\Files\File;
use PHP_CodeSniffer\Sniffs\Sniff;

class DisallowEachInLoopConditionSniff implements Sniff
{
    public function register()
    {
        return [T_WHILE];
    }

    public function process(File $phpcsFile, $stackPtr)
    {
        $tokens     = $phpcsFile->getTokens();
        $startToken = ($tokens[$stackPtr]['parenthesis_opener'] + 1);
        $endToken   = ($tokens[$stackPtr]['parenthesis_closer'] - 1);
        $result     = $phpcsFile->findNext(T_STRING, $startToken, $endToken, false, 'each');
        if ($result !== false) {
            $message = 'Usage of "each()" not allowed in loop condition. Use "foreach"-loop instead.';
            $phpcsFile->addError($message, $stackPtr, 'EachInWhileLoopNotAllowed');
        }
    }
}
