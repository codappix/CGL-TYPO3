Coding Guideline for TYPO3
==========================

Installation
------------

You have to add the following to the `composer.json`:

    "scripts": {
        "post-autoload-dump": [
            "[ -d vendor/squizlabs/php_codesniffer/CodeSniffer/Standards/CGL-PHP ] || cp -r vendor/codappix/cgl-php vendor/squizlabs/php_codesniffer/CodeSniffer/Standards/CGL-PHP",
            "[ -d vendor/squizlabs/php_codesniffer/CodeSniffer/Standards/CGL-TYPO3 ] || cp -r vendor/codappix/cgl-typo3 vendor/squizlabs/php_codesniffer/CodeSniffer/Standards/CGL-TYPO3",
            "[ -d vendor/squizlabs/php_codesniffer/CodeSniffer/Standards/TYPO3SniffPool ] || cp -r vendor/typo3-ci/typo3sniffpool vendor/squizlabs/php_codesniffer/CodeSniffer/Standards/TYPO3SniffPool"
        ]
    }

After that you have to require the package via composer `composer require codappix/cgl-typo3`.

What does it do?
----------------

The goal of this package is to provide our TYPO3 cgl rules, which are based on `cgl-php`_, via composer.
The package uses PHP_CodeSniffer to sniff the configured files and show errors.

Current state
-------------

This is the initial version of this ruleset. We are currently using our PHP rules.
Custom rules and sniffs will follow when needed.

This is a early version. More information can be taken from Github at
`current issues`_.

.. _cgl-php: https://packagist.org/packages/codappix/cgl-php
.. _current issues: https://github.com/Codappix/CGL-TYPO3/issues
