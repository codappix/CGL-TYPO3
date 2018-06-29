<?php
namespace Codappix\CDXTYPO3\Sniffs\PHP;

use PHP_CodeSniffer\Files\File;
use PHP_CodeSniffer\Sniffs\Sniff;

class DisallowMultiplePHPTagsSniff implements Sniff
{
    public function register()
    {
        return [
            T_OPEN_TAG,
            T_CLOSE_TAG,
        ];
    }

    public function process(File $phpcsFile, $stackPtr)
    {
        $tokens = $phpcsFile->getTokens();
        $disallowTag = $phpcsFile->findNext($tokens[$stackPtr]['code'], ($stackPtr + 1));
        if (false !== $disallowTag) {
            $error = 'Exactly one "' . $tokens[$stackPtr]['content'] . '" tag is allowed';
            $phpcsFile->addError($error, $disallowTag);
        }

        return;
    }
}
