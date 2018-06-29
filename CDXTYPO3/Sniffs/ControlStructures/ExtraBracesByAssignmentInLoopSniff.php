<?php
namespace Codappix\CDXTYPO3\Sniffs\ControlStructures;

use PHP_CodeSniffer\Files\File;
use PHP_CodeSniffer\Sniffs\Sniff;

class ExtraBracesByAssignmentInLoopSniff implements Sniff
{
    public function register()
    {
        return [
            T_WHILE,
            T_IF,
        ];
    }

    public function process(File $phpcsFile, $stackPtr)
    {
        $tokens           = $phpcsFile->getTokens();
        $parenthesisStart = $phpcsFile->findNext(T_OPEN_PARENTHESIS, $stackPtr);
        $parenthesisStart = $tokens[$parenthesisStart]['parenthesis_opener'];
        $parenthesisEnd   = $tokens[$parenthesisStart]['parenthesis_closer'];

        $equalOperator = $phpcsFile->findNext(T_EQUAL, $parenthesisStart, $parenthesisEnd);
        if ($equalOperator === false) {
            return;
        }

        $braceBefore = $phpcsFile->findPrevious(T_OPEN_PARENTHESIS, $equalOperator, $parenthesisStart);
        if ($braceBefore === $parenthesisStart) {
            $message = 'Assignments in condition should be surrounded by the extra pair of brackets';
            $phpcsFile->addError($message, $stackPtr, 'AssignmentsInCondition');
        }
    }
}
