<?php
namespace Codappix\CDXTYPO3\Sniffs\Commenting;

use PHP_CodeSniffer\Files\File;
use PHP_CodeSniffer\Sniffs\Sniff;

class DoubleSlashCommentsInNewLineSniff implements Sniff
{
    public function register()
    {
        return [T_COMMENT];
    }

    public function process(File $phpcsFile, $stackPtr)
    {
        $tokens  = $phpcsFile->getTokens();
        $keyword = $tokens[$stackPtr]['content'];
        if (substr($keyword, 0, 2) === '//' && $this->existsOtherCodeBeforeThisComment($tokens, $stackPtr) === true) {
            $error = 'The double slash comments must be on a seperate line.';
            $phpcsFile->addError($error, $stackPtr, 'existing');
        }
    }

    /**
     * Checks if the found T_COMMENT is in a line which available source code.
     * Returns true, if there IS existing source code in the same line before
     * the comment.
     *
     * $a = $b; // This is the found comment
     * => Returns true
     *
     * // This is the found comment
     * => Returns false
     *
     * @param array $tokens   Token array with all tokens from the file which
     *                        is checked
     * @param int   $stackPtr Stackpointer where one of the registered token
     *                        was found
     *
     * @return bool
     */
    protected function existsOtherCodeBeforeThisComment(array $tokens, $stackPtr)
    {
        $result       = false;
        $originalLine = $tokens[$stackPtr]['line'];
        do {
            $stackPtr--;
            $line = $tokens[$stackPtr]['line'];
            if ($originalLine === $line && $tokens[$stackPtr]['type'] !== 'T_WHITESPACE') {
                $result = true;
            }
        } while ($result == false && $originalLine == $line);
        return $result;
    }
}
