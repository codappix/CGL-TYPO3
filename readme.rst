Coding Guideline for TYPO3
==========================

This standard is named `CglTypo3` within PHP Code Sniffer.

Installation
------------

There are different ways, we use the following.

Add the following to the `composer.json`:

    "scripts": {
        "post-autoload-dump": [
            "[ -e vendor/codappix/CGL-TYPO3 ] || ln -s CglTypo3 vendor/codappix/CGL-TYPO3",
            "./vendor/bin/phpcs --config-set installed_paths $PWD/vendor/codappix"
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
