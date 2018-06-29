<?php
namespace Codappix\CDXTYPO3\Sniffs\Commenting;

use PHP_CodeSniffer\Files\File;
use PHP_CodeSniffer\Sniffs\Sniff;

class ValidCommentLineLengthSniff implements Sniff
{
    /**
     * Max character length of comments
     *
     * @var int
     */
    public $maxCommentLength = 80;

    public function register()
    {
        return [
            T_DOC_COMMENT_STAR,
            T_COMMENT,
        ];
    }

    public function process(File $phpcsFile, $stackPtr)
    {
        $tokens = $phpcsFile->getTokens();

        $commentLineLength = $tokens[$stackPtr]['length'];
        $lastTokenOnLine = $this->getLastTokenOnLine($tokens, $stackPtr);

        for ($i = ($stackPtr + 1); $i <= $lastTokenOnLine; $i++) {
            $commentLineLength += $tokens[$i]['length'];
        }

        if ($commentLineLength > $this->maxCommentLength) {
            $phpcsFile->addWarning(
                'Comment lines should be kept within a limit of about ' .
                    $this->maxCommentLength . ' characters but this comment has ' .
                    $commentLineLength . ' character!',
                $stackPtr,
                'CommentLineLength'
            );
        }
    }

    protected function getLastTokenOnLine(array $tokens, $stackPtr)
    {
        $line = $tokens[$stackPtr]['line'];
        $lastToken = $stackPtr;

        $stackPtr++;
        while (isset($tokens[$stackPtr]) && $tokens[$stackPtr]['line'] === $line) {
            $lastToken = $stackPtr;
            $stackPtr++;
        }

        return $lastToken;
    }
}
