BREAKING - Separated provided features based on TYPO3 version
=============================================================

See: DPL-158

## Description

Following class(s) has been moved to a new namespace:

* `\WebVision\Deepl\Base\Controller\Backend\LocalizationController` to
  `\WebVision\Deepl\Base\Core13\Controller\Backend\LocalizationController`

Following classes has been marked as deprecated and are not used in TYPO3 v14
albeit being available and kept to minimize fatal breaking errors:

* `\WebVision\Deepl\Base\Event\GetLocalizationModesEvent`
* `\WebVision\Deepl\Base\Event\LocalizationProcessPrepareDataHandlerCommandMapEvent`
* `\WebVision\Deepl\Base\EventListener\DefaultTranslationDropdownEventListener`
* `\WebVision\Deepl\Base\EventListener\PrepareLocalizationProcessDataHandlerCommandMapForTypo3LocalizationModes`
* `\WebVision\Deepl\Base\EventListener\ProvideDefaultTypo3LocalizationModesEventListener`

Further, backend template overrides has been moved into a dedicated folder
and related PageTsConfig option has been adjusted and nested into a condition
to override them only for TYPO3 v13 to provide the overrides to inject values.

> [!NOTE]
> This needs to be rechecked if we still need the variable injection ViewHelper
> at the same places for TYPO3 v14 for `EXT:deepl_write` but intentionally
> separated now.
