<?php
namespace Codappix\CglTypo3\Sniffs\ControlStructures;

use PHP_CodeSniffer\Files\File;
use PHP_CodeSniffer\Sniffs\Sniff;

class TernaryConditionalOperatorSniff implements Sniff
{
    public function register()
    {
        return [T_INLINE_THEN];
    }

    public function process(File $phpcsFile, $stackPtr)
    {
        $isNested = $phpcsFile->findNext(
            T_INLINE_THEN,
            ($stackPtr + 1),
            null,
            false,
            null,
            true
        );

        if ($isNested !== false) {
            $error = 'Nested ternary conditional operators are not allowed;';
            $phpcsFile->addError($error, $stackPtr);
        }
    }
}
