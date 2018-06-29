<?php
namespace Codappix\CDXTYPO3\Sniffs\Debug;

use PHP_CodeSniffer\Files\File;
use PHP_CodeSniffer\Sniffs\Sniff;

class DebugCodeSniff implements Sniff
{
    public function register()
    {
        return [
            T_STRING,
            T_COMMENT,
        ];
    }

    public function process(File $phpcsFile, $stackPtr)
    {
        $tokens       = $phpcsFile->getTokens();
        $tokenType    = $tokens[$stackPtr]['type'];
        $currentToken = $tokens[$stackPtr]['content'];
        switch ($tokenType) {
            case 'T_STRING':
                if ($currentToken === 'debug') {
                    $previousToken = $phpcsFile->findPrevious(T_WHITESPACE, ($stackPtr - 1), null, true);
                    if ($tokens[$previousToken]['content'] === '::') {
                        $errorData = array($tokens[($previousToken - 1)]['content']);
                        $error     = 'Call to debug function %s::debug() must be removed';
                        $phpcsFile->addError($error, $stackPtr, 'StaticDebugCall', $errorData);
                    } elseif (($tokens[$previousToken]['content'] === '->')
                        || ($tokens[$previousToken]['content'] === 'class')
                        || ($tokens[$previousToken]['content'] === 'function')
                    ) {
                        // phpcs:disable CglTypo3.Debug.DebugCode.CommentOutDebugCall
                        // We don't care about code like:
                        // if ($this->debug) {...}
                        // class debug {...}
                        // function debug () {...}.
                        // phpcs:enable
                        return;
                    } else {
                        $errorData = array($currentToken);
                        $error     = 'Call to debug function %s() must be removed';
                        $phpcsFile->addError($error, $stackPtr, 'DebugFunctionCall', $errorData);
                    }
                } elseif ($currentToken === 'print_r' || $currentToken === 'var_dump' || $currentToken === 'xdebug') {
                    $errorData = array($currentToken);
                    $error     = 'Call to debug function %s() must be removed';
                    $phpcsFile->addError($error, $stackPtr, 'NativDebugFunction', $errorData);
                }
                break;
            case 'T_COMMENT':
                $comment          = $tokens[$stackPtr]['content'];
                $ifDebugInComment = preg_match_all(
                    '/\b((DebugUtility::)?([x]?debug)|(print_r)|(var_dump))([\s]+)?\(/',
                    $comment,
                    $matchesArray
                );
                if ($ifDebugInComment === 1) {
                    $error  = 'Its not enough to comment out debug functions calls; ';
                    $error .= 'they must be removed from code.';
                    $phpcsFile->addError($error, $stackPtr, 'CommentOutDebugCall');
                }
                break;
            default:
        }
    }
}
