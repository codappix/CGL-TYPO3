<?php
namespace Codappix\CDXTYPO3\Sniffs\WhiteSpace;

use PHP_CodeSniffer\Files\File;
use PHP_CodeSniffer\Sniffs\Sniff;

class WhitespaceAfterCommentSignsSniff implements Sniff
{
    public function register()
    {
        return [T_COMMENT];
    }

    public function process(File $phpcsFile, $stackPtr)
    {
        $tokens      = $phpcsFile->getTokens();
        $commentLine = '';
        // We only need the single line comments which started with double slashes.
        if (substr_compare($tokens[$stackPtr]['content'], '//', 0, 2) === 0) {
            $commentLine = $tokens[$stackPtr]['content'];
            if (preg_match_all('/\/\/( ){1,}[\S]|(\/){3,}/', $commentLine, $matchesarray) === 0) {
                $phpcsFile->addError(
                    'Whitespace must be added after double slashes in single line comments; expected' .
                        ' "// This is a comment" but found "' . trim($tokens[$stackPtr]['content']) . '"',
                    $stackPtr,
                    'existing'
                );
            }
        }
    }
}
