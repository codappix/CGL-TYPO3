<?php
namespace Codappix\CDXTYPO3\Sniffs\Strings;

use PHP_CodeSniffer\Files\File;
use PHP_CodeSniffer\Sniffs\Sniff;
use PHP_CodeSniffer\Util\Tokens;

class UnnecessaryStringConcatSniff implements Sniff
{
    public $supportedTokenizers = [
        'PHP',
        'JS',
    ];

    /**
     * If true, an error will be thrown; otherwise a warning.
     *
     * @var bool
     */
    public $error = true;

    public function register()
    {
        return [
            T_STRING_CONCAT,
            T_PLUS,
        ];
    }

    public function process(File $phpcsFile, $stackPtr)
    {
        // Work out which type of file this is for.
        $tokens = $phpcsFile->getTokens();
        if ($tokens[$stackPtr]['code'] === T_STRING_CONCAT) {
            if ($phpcsFile->tokenizerType === 'JS') {
                return;
            }
        } else {
            if ($phpcsFile->tokenizerType === 'PHP') {
                return;
            }
        }

        $prev            = $phpcsFile->findPrevious(T_WHITESPACE, ($stackPtr - 1), null, true);
        $next            = $phpcsFile->findNext(T_WHITESPACE, ($stackPtr + 1), null, true);
        $columnPrevToken = $tokens[$prev]['column'];
        $columnCurrentToken = $tokens[$stackPtr]['column'];
        $lineCurrentToken   = $tokens[$stackPtr]['line'];
        $lineNextToken      = $tokens[$next]['line'];
        // Concat string over multiple lines are allowed, so we leave the sniff here.
        if ($lineCurrentToken !== $lineNextToken) {
            return;
        }

        if ($prev === false || $next === false) {
            return;
        }

        // Check if the concatenation operator its on the end of the line,
        // otherwise we trow an error.
        if ($columnPrevToken > $columnCurrentToken) {
            $error = 'Line concatenation operator must be at the end of the line';
            $phpcsFile->addError($error, $stackPtr, 'Found');
            return;
        }

        $stringTokens = Tokens::$stringTokens;
        if (in_array($tokens[$prev]['code'], $stringTokens) === true
            && in_array($tokens[$next]['code'], $stringTokens) === true
        ) {
            if ($tokens[$prev]['content'][0] === $tokens[$next]['content'][0]) {
                $error = 'String concat is not required here; use a single string instead';
                if ($this->error === true) {
                    $phpcsFile->addError($error, $stackPtr, 'Found');
                } else {
                    $phpcsFile->addWarning($error, $stackPtr, 'Found');
                }
            }
        }
    }
}
