<?php
namespace Codappix\CglTypo3\Sniffs\Files;

use PHP_CodeSniffer\Files\File;
use PHP_CodeSniffer\Sniffs\Sniff;

class IncludingFileSniff implements Sniff
{
    public function register()
    {
        return [
            T_INCLUDE_ONCE,
            T_REQUIRE,
            T_INCLUDE,
        ];
    }

    public function process(File $phpcsFile, $stackPtr)
    {
        $tokens    = $phpcsFile->getTokens();
        $keyword   = $tokens[$stackPtr]['content'];
        $tokenCode = $tokens[$stackPtr]['type'];
        switch ($tokenCode) {
            case 'T_INCLUDE_ONCE':
                // Here we are looking if the found include_once keyword is
                // part of an XClass declaration where this is allowed.
                if ($tokens[($stackPtr + 7)]['content'] === "'XCLASS'") {
                    return;
                }

                $error  = 'Including files with "' . $keyword . '" is not allowed; ';
                $error .= 'use "require_once" instead';
                $phpcsFile->addError($error, $stackPtr);
                break;
            case 'T_REQUIRE':
                $error  = 'Including files with "' . $keyword . '" is not allowed; ';
                $error .= 'use "require_once" instead';
                $phpcsFile->addError($error, $stackPtr);
                break;
            case 'T_INCLUDE':
                $error  = 'Including files with "' . $keyword . '" is not allowed; ';
                $error .= 'use "require_once" instead';
                $phpcsFile->addError($error, $stackPtr);
                break;
            default:
        }
    }
}
