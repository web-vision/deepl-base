<?php

declare(strict_types=1);

namespace WebVision\Deepl\Base\Core13\EventListener;

use TYPO3\CMS\Core\Attribute\AsEventListener;
use WebVision\Deepl\Base\Event\ViewHelpers\ModifyInjectVariablesViewHelperEvent;

/**
 * Used in Template for providing the TYPO3 Core translation options in PageView
 * @depreacted used only for TYPO3 v13 compatibility and will not be dispatched for TYPO3 v14
 * *             and should be resort to TYPO3 v14 localization handler feature provided by the
 * *             TYPO3 Core.
 */
final class DefaultTranslationDropdownEventListener
{
    #[AsEventListener(
        identifier: 'deepl-base/default-translation',
    )]
    public function __invoke(ModifyInjectVariablesViewHelperEvent $event): void
    {
        // Note that this identifier / ViewHelper is only included in TYPO3 v13
        // backend template overrides and therefore restricts this already down.
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
