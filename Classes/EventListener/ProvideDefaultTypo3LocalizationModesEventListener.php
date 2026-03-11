<?php

declare(strict_types=1);

namespace WebVision\Deepl\Base\EventListener;

use TYPO3\CMS\Backend\Controller\Page\LocalizationController;
use TYPO3\CMS\Core\Information\Typo3Version;
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
 * @depreacted used only for TYPO3 v13 compatibility and will not be dispatched for TYPO3 v14
 *             and should be resort to TYPO3 v14 localization handler feature provided by the
 *             TYPO3 Core.
 */
final class ProvideDefaultTypo3LocalizationModesEventListener
{
    public function __invoke(GetLocalizationModesEvent $event): void
    {
        if ((new Typo3Version())->getMajorVersion() > 13) {
            // Not needed in TYPO v14 or newer.
            return;
        }
        $modes = [];
        if ($this->allowTranslate($event)) {
            $modes[] = new LocalizationMode(
                identifier: LocalizationController::ACTION_LOCALIZE,
                title: $event->getLanguageService()->sL('LLL:EXT:backend/Resources/Private/Language/locallang_layout.xlf:localize.wizard.button.translate'),
                description: $event->getLanguageService()->sL('LLL:EXT:backend/Resources/Private/Language/locallang_layout.xlf:localize.educate.translate'),
                icon: 'actions-localize',
                before: [LocalizationController::ACTION_COPY],
                after: [],
            );
        }
        if ($this->allowCopy($event)) {
            $modes[] = new LocalizationMode(
                identifier: LocalizationController::ACTION_COPY,
                title: $event->getLanguageService()->sL('LLL:EXT:backend/Resources/Private/Language/locallang_layout.xlf:localize.wizard.button.copy'),
                description: $event->getLanguageService()->sL('LLL:EXT:backend/Resources/Private/Language/locallang_layout.xlf:localize.educate.copy'),
                icon: 'actions-edit-copy',
                before: [],
                after: [LocalizationController::ACTION_LOCALIZE],
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
