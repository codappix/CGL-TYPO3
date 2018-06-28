<?php
namespace Codappix\CglTypo3\Sniffs\Strings;

use PHP_CodeSniffer\Files\File;
use PHP_CodeSniffer\Sniffs\Sniff;

class ConcatenationSpacingSniff implements Sniff
{
    public function register()
    {
        return [T_STRING_CONCAT];
    }

    public function process(File $phpcsFile, $stackPtr)
    {
        $tokens    = $phpcsFile->getTokens();
        $prevToken = $tokens[($stackPtr - 1)];
        $nextToken = $tokens[($stackPtr + 1)];

        if ($prevToken['code'] !== T_WHITESPACE
            || $nextToken['code'] !== T_WHITESPACE
        ) {
            $error = 'Concat operator must be surrounded by spaces. ';
            $phpcsFile->addError($error, $stackPtr, 'NoSpaceAroundConcat');
        }

        if (($prevToken['code'] === T_WHITESPACE && stristr($prevToken['content'], '  ') !== false)
            || ($nextToken['code'] === T_WHITESPACE && stristr($nextToken['content'], '  ') !== false)
        ) {
            $error = 'Concat operator should be surrounded by just one space';
            $phpcsFile->addWarning($error, $stackPtr, 'OnlyOneSpaceAroundConcat');
        }
    }
}
