<?php

declare(strict_types=1);

namespace WebVision\Deepl\Base\EventListener;

use TYPO3\CMS\Core\Information\Typo3Version;
use WebVision\Deepl\Base\Event\ViewHelpers\ModifyInjectVariablesViewHelperEvent;

/**
 * Used in Template for providing the TYPO3 Core translation options in PageView
 * @depreacted used only for TYPO3 v13 compatibility and will not be dispatched for TYPO3 v14
 * *             and should be resort to TYPO3 v14 localization handler feature provided by the
 * *             TYPO3 Core.
 */
final class DefaultTranslationDropdownEventListener
{
    public function __invoke(ModifyInjectVariablesViewHelperEvent $event): void
    {
        if ((new Typo3Version())->getMajorVersion() > 13) {
            // Not needed in TYPO v14 or newer.
            return;
        }
        if ($event->getIdentifier() !== 'languageTranslationDropdown') {
            return;
        }
        $translationPartials = $event->getLocalVariableProvider()->get('translationPartials');
        if ($translationPartials === null) {
            $translationPartials = [];
        }
        $translationPartials[10] = 'Translation/DefaultTranslationDropdown';
        $event->getLocalVariableProvider()->add('translationPartials', $translationPartials);
    }
}
