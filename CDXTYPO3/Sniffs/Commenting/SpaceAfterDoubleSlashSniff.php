<?php
namespace Codappix\CDXTYPO3\Sniffs\Commenting;

use PHP_CodeSniffer\Files\File;
use PHP_CodeSniffer\Sniffs\Sniff;

class SpaceAfterDoubleSlashSniff implements Sniff
{
    public function register()
    {
        return [T_COMMENT];
    }

    public function process(File $phpcsFile, $stackPtr)
    {
        $tokens  = $phpcsFile->getTokens();
        $keyword = $tokens[$stackPtr]['content'];
        if (substr($keyword, 0, 2) === '//' && (substr($keyword, 2, 1) === ' ') === false) {
            $error = 'Space must be added in single line comments after the comment sign (double slash).';
            $fix   = $phpcsFile->addFixableError($error, $stackPtr, 'SpaceAfterDoubleSlash');
            if ($fix === true) {
                $phpcsFile->fixer->replaceToken($stackPtr, preg_replace('#^//#', '// ', $keyword));
            }
        }
    }
}
