Coding Guideline for TYPO3
==========================

This standard is named `CDXTYPO3` within PHP Code Sniffer.

Installation
------------

You have to require the package via composer `composer require codappix/cgl-typo3`.

Afterwards, there are multiple ways to install the standard. We recommend to require
`dealerdirect/phpcodesniffer-composer-installer` which handles the installation.

What does it do?
----------------

The goal of this package is to provide our TYPO3 coding guideline rules, which are
based on `CDXPHP`_, via composer. The package uses PHP Code Sniffer to sniff the
configured files and show errors.

How to use
----------

After installation, the standard is available as `CDXTYPO3`.

Current state
-------------

This is the initial version of this ruleset. We are currently using our PHP rules.
Custom rules and sniffs will follow when needed.

This is a early version. More information can be taken from Github at
`current issues`_.

Credits
-------

The sniffs were originally copied from TYPO3SniffPool which is not managed anymore.

.. _CDXPHP: https://packagist.org/packages/codappix/cgl-php
.. _current issues: https://github.com/Codappix/CGL-TYPO3/issues
