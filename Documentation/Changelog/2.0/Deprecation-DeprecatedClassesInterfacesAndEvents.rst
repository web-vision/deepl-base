..  _deprecation-deprecatedclassesinterfacesandevents-1777259934:

=========================================
Deprecation: Deprecated TYPO3 v13 support
=========================================

Description
===========

Following classes are deprecated and will be removed in `3.0.0`:

*   :php:`\WebVision\Deepl\Base\Core13\Controller\Backend\LocalizationController`
*   :php:`\WebVision\Deepl\Base\Core13\EventListener\DefaultTranslationDropdownEventListener`
*   :php:`\WebVision\Deepl\Base\Core13\EventListener\PrepareLocalizationProcessDataHandlerCommandMapForTypo3LocalizationModes`
*   :php:`\WebVision\Deepl\Base\Core13\EventListener\ProvideDefaultTypo3LocalizationModesEventListener`
*   :php:`\WebVision\Deepl\Base\Localization\LocalizationMode`
*   :php:`\WebVision\Deepl\Base\Localization\LocalizationModeCollection`

Following PSR-14 are deprecated and will be removed in `3.0.0`:

*   :php:`\WebVision\Deepl\Base\Event\GetLocalizationModesEvent` only dispatched
    in TYPO3 v13.
*   :php:`\WebVision\Deepl\Base\Event\LocalizationProcessPrepareDataHandlerCommandMapEvent`
    only dispatched in TYPO3 v13.

Impact
======

Instances with extension using these classes or events will either get PHP
Fatal errors or possible dependency injection issues.

`web-vision` extension requiring this extension will take care of this.

Affected installations
======================

All instances with extension relating to this extension.

Migration
=========

There is no migration for the deprecated classes, interfaces and events. They
are now longer required in TYPO3 v14 and are only kept to keep TYPO3 v13 support
up.
