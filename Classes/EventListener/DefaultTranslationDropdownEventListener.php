<?php

declare(strict_types=1);

namespace WebVision\Deepl\Base\EventListener;

use WebVision\Deepl\Base\Event\ViewHelpers\ModifyInjectVariablesViewHelperEvent;

/**
 * Used in Template for providing the TYPO3 Core translation options in PageView
 */
final class DefaultTranslationDropdownEventListener
{
    public function __invoke(ModifyInjectVariablesViewHelperEvent $event): void
    {
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
