..  _breaking-removed-typo3v12-support-1777257250:

===================================
Breaking: Removed TYPO3 v12 support
===================================

Description
===========

Support for TYPO3 v12 has been removed for `2.x` based on our dual
TYPO3 core version support per major version as casual support matrix.

This includes removing code paths and configurations only required for
TYPO3 v12.

..  note::

    This extension is usually used as transient dependency for other
    extension and should be updated or forced to suitable version by
    them.

    Refer to corresponding extension to update :guilabel:``EXT:deepl_base`
    along the way.

Impact
======

TYPO3 v12 or older instances cannot update to the `2.x` version and are
required to upgrade TYPO3 to be able to install the next version of the
`EXT:deepltranslate_core` and related extensions/addons based on this
version.

Extension cannot be installed in that version but does not break otherwise.

Affected installations
======================

TYPO3 v12 or older instances with :guilabel:``EXT:deepl_base` version `1.x`.


Migration
=========

Upgrade TYPO3 to supported version for `2.x` beforehand or in the same step.
