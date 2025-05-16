<?php

declare(strict_types=1);

namespace WebVision\Deepl\Base\EventListener;

use TYPO3\CMS\Core\Utility\MathUtility;
use WebVision\Deepl\Base\Event\GetLocalizationModesEvent;
use WebVision\Deepl\Base\Localization\LocalizationMode;

/**
 * PSR-14 event listener to provide default TYPO3 localization modes for the
 * localization modal based on PageTSConfig settings, required to allow them
 * to be removed using dedicated event listener.
 *
 * Default adopted from EXT:backend localization JavaScript model class.
 *
 * @todo Use PHP attribute to register event when TYPO3 13.4 is minimal supported version.
 */
final class ProvideDefaultTypo3LocalizationModesEventListener
{
    public function __invoke(GetLocalizationModesEvent $event): void
    {
        $modes = [];
        if ($this->allowTranslate($event)) {
            $modes[] = new LocalizationMode(
                identifier: 'localize',
                title: $event->getLanguageService()->sL('LLL:EXT:backend/Resources/Private/Language/locallang_layout.xlf:localize.wizard.button.translate'),
                description: $event->getLanguageService()->sL('LLL:EXT:backend/Resources/Private/Language/locallang_layout.xlf:localize.educate.translate'),
                icon: 'actions-localize',
                before: ['copy'],
                after: [],
            );
        }
        if ($this->allowCopy($event)) {
            $modes[] = new LocalizationMode(
                identifier: 'copy',
                title: $event->getLanguageService()->sL('LLL:EXT:backend/Resources/Private/Language/locallang_layout.xlf:localize.wizard.button.copy'),
                description: $event->getLanguageService()->sL('LLL:EXT:backend/Resources/Private/Language/locallang_layout.xlf:localize.educate.copy'),
                icon: 'actions-edit-copy',
                before: [],
                after: ['localize'],
            );
        }

        if ($modes !== []) {
            $event->getModes()->add(...array_values($modes));
        }
    }

    private function allowCopy(GetLocalizationModesEvent $event): bool
    {
        $pageTsConfig = $event->getPageTsConfig();
        if (!is_array($pageTsConfig['mod.'] ?? null)
            || !is_array($pageTsConfig['mod.']['web_layout.'] ?? null)
            || !is_array($pageTsConfig['mod.']['web_layout.']['localization.'] ?? null)
            || !(
                is_bool($pageTsConfig['mod.']['web_layout.']['localization.']['enableCopy'] ?? null)
                || is_int($pageTsConfig['mod.']['web_layout.']['localization.']['enableCopy'] ?? null)
                || (is_string($pageTsConfig['mod.']['web_layout.']['localization.']['enableCopy'] ?? null) && MathUtility::canBeInterpretedAsInteger($pageTsConfig['mod.']['web_layout.']['localization.']['enableCopy']))
            )
        ) {
            return true;
        }
        return (bool)$pageTsConfig['mod.']['web_layout.']['localization.']['enableCopy'];
    }

    private function allowTranslate(GetLocalizationModesEvent $event): bool
    {
        $pageTsConfig = $event->getPageTsConfig();
        if (!is_array($pageTsConfig['mod.'] ?? null)
            || !is_array($pageTsConfig['mod.']['web_layout.'] ?? null)
            || !is_array($pageTsConfig['mod.']['web_layout.']['localization.'] ?? null)
            || !(
                is_bool($pageTsConfig['mod.']['web_layout.']['localization.']['enableTranslate'] ?? null)
                || is_int($pageTsConfig['mod.']['web_layout.']['localization.']['enableTranslate'] ?? null)
                || (is_string($pageTsConfig['mod.']['web_layout.']['localization.']['enableTranslate'] ?? null) && MathUtility::canBeInterpretedAsInteger($pageTsConfig['mod.']['web_layout.']['localization.']['enableTranslate']))
            )
        ) {
            return true;
        }
        return (bool)$pageTsConfig['mod.']['web_layout.']['localization.']['enableTranslate'];
    }
}
