<?php
namespace Codappix\CglTypo3\Sniffs\Classes;

use PHP_CodeSniffer\Standards\Squiz\Sniffs\Classes\LowercaseClassKeywordsSniff as PhpCsLowercaseClassKeywordsSniff;

/**
 * Ensures all class keywords are lowercase.
 */
class LowercaseClassKeywordsSniff extends PhpCsLowercaseClassKeywordsSniff
{
    public function register()
    {
        return [
            T_CLASS,
            T_INTERFACE,
            T_EXTENDS,
            T_IMPLEMENTS,
            T_ABSTRACT,
            T_FINAL,
            T_TRAIT,
            T_VAR,
            T_CONST,
            T_PRIVATE,
            T_PUBLIC,
            T_PROTECTED,
        ];
    }
}
