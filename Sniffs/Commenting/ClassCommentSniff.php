<?php
namespace Codappix\CglTypo3\Sniffs\Commenting;

use PHP_CodeSniffer\Files\File;
use PHP_CodeSniffer\Sniffs\Sniff;
use PHP_CodeSniffer\Util\Tokens;

class ClassCommentSniff implements Sniff
{
    public function register()
    {
        return [
            T_CLASS,
            T_INTERFACE,
            T_TRAIT,
        ];
    }

    public function process(File $phpcsFile, $stackPtr)
    {
        $tokens    = $phpcsFile->getTokens();
        $type      = strtolower($tokens[$stackPtr]['content']);
        $errorData = array($type);

        $find   = Tokens::$methodPrefixes;
        $find[] = T_WHITESPACE;

        $commentEnd = $phpcsFile->findPrevious($find, ($stackPtr - 1), null, true);
        if ($tokens[$commentEnd]['code'] !== T_DOC_COMMENT_CLOSE_TAG
            && $tokens[$commentEnd]['code'] !== T_COMMENT
        ) {
            return;
        } else {
            $phpcsFile->recordMetric(
                $stackPtr,
                '%s has doc comment',
                'yes'
            );
        }

        // Try and determine if this is a file comment instead of a class comment.
        // We assume that if this is the first comment after the open PHP tag, then
        // it is most likely a file comment instead of a class comment.
        if ($tokens[$commentEnd]['code'] === T_DOC_COMMENT_CLOSE_TAG) {
            $start = ($tokens[$commentEnd]['comment_opener'] - 1);
        } else {
            $start = $phpcsFile->findPrevious(
                T_COMMENT,
                ($commentEnd - 1),
                null,
                true
            );
        }

        $prev = $phpcsFile->findPrevious(T_WHITESPACE, $start, null, true);
        if ($tokens[$prev]['code'] === T_OPEN_TAG) {
            $prevOpen = $phpcsFile->findPrevious(T_OPEN_TAG, ($prev - 1));
            if ($prevOpen === false) {
                // This is a comment directly after the first open tag,
                // so probably a file comment.
                $phpcsFile->addError(
                    'Missing %s doc comment',
                    $stackPtr,
                    'Missing',
                    $errorData
                );
                return;
            }
        }

        if ($tokens[$commentEnd]['code'] === T_COMMENT) {
            $phpcsFile->addError(
                'You must use "/**" style comments for a %s comment',
                $stackPtr,
                'WrongStyle',
                $errorData
            );
            return;
        }

        if ($tokens[$commentEnd]['line'] !== ($tokens[$stackPtr]['line'] - 1)) {
            $error = 'There must be no blank lines after the %s comment';
            $phpcsFile->addError($error, $commentEnd, 'SpacingAfter', $errorData);
        }

        $commentStart = $tokens[$commentEnd]['comment_opener'];
        if ($tokens[$prev]['line'] !== ($tokens[$commentStart]['line'] - 2)) {
            $error = 'There must be exactly one blank line before the %s comment';
            $phpcsFile->addError($error, $commentStart, 'SpacingBefore', $errorData);
        }
    }
}
