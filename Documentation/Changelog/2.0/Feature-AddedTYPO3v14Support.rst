..  _feature-addedtypo3v14support-1777258656:

================================
Feature: Added TYPO3 v14 Support
================================

Description
===========

:guilabel:`TYPO3 v14.3.*` has been added coming with following changes:

Replacing TYPO3 localization wizard is restricted to TYPO3 v13 and deprecated
-----------------------------------------------------------------------------

For `TYPO3 v14` the streamlined and revamped overall localization handling is
adopted and making use of the new `Localization Handler` feature. That means,
that the look and feel is different based on the used TYPO3 version even with
the same extension version.

To respect this replacing the `localization wizard` JavaScript together with
the related `PSR-14` events are restricted to TYPO v13 only and is also now
deprecated:

*   TYPO3 v13 localization mode selection in `PageLayout module`:

    ..  figure:: /Images/deepl-localization-mode.png
        :alt: Select `Translate with DeepL` localization mode in TYPo3 v13

Impact
======

:guilabel:`web-vision/deepltranslate-core` can now be installed and used in
:guilabel:`TYPO3 v14.3` instances.

Supported features are completely available for TYPO3 v13 and v14, except
that generic TYPO3 handling is used provided by these versions.

