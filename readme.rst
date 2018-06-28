Coding Guideline for TYPO3
==========================

This standard is named `CglTypo3` within PHP Code Sniffer.

Installation
------------

You have to add the following to the `composer.json`:

    "scripts": {
        "post-autoload-dump": [
            "[ -d vendor/squizlabs/php_codesniffer/CodeSniffer/Standards/CGL-PHP ] || cp -r vendor/codappix/cgl-php vendor/squizlabs/php_codesniffer/CodeSniffer/Standards/CGL-PHP",
            "[ -d vendor/squizlabs/php_codesniffer/CodeSniffer/Standards/CGL-TYPO3 ] || cp -r vendor/codappix/cgl-typo3 vendor/squizlabs/php_codesniffer/CodeSniffer/Standards/CglTypo3",
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

Credits
-------

The sniffs were originally copied from TYPO3SniffPool which is not managed anymore.

.. _cgl-php: https://packagist.org/packages/codappix/cgl-php
.. _current issues: https://github.com/Codappix/CGL-TYPO3/issues
