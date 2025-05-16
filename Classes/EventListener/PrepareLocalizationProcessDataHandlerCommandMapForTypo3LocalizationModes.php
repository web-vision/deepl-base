<?php

declare(strict_types=1);

namespace WebVision\Deepl\Base\EventListener;

use TYPO3\CMS\Backend\Controller\Page\LocalizationController;
use WebVision\Deepl\Base\Event\LocalizationProcessPrepareDataHandlerCommandMapEvent;

/**
 * Prepare DataHandler command map for default TYPO3 localization modes, dispatched in
 * {@see \WebVision\Deepl\Base\Controller\Backend\LocalizationController::customProcess()}.
 */
final class PrepareLocalizationProcessDataHandlerCommandMapForTypo3LocalizationModes
{
    public function __invoke(LocalizationProcessPrepareDataHandlerCommandMapEvent $event): void
    {
        if (!in_array($event->getLocalizationMode()->identifier, [LocalizationController::ACTION_COPY, LocalizationController::ACTION_LOCALIZE], true)) {
            // Not responsible, early return.
            return;
        }
        $cmd = $event->getCmd();
        foreach ($event->getUidList() as $currentUid) {
            if ($event->getLocalizationMode()->identifier === LocalizationController::ACTION_LOCALIZE) {
                $cmd['tt_content'][$currentUid] = [
                    'localize' => $event->getDestLanguageId(),
                ];
                continue;
            }
            $cmd['tt_content'][$currentUid] = [
                'copyToLanguage' => $event->getDestLanguageId(),
            ];
        }
        $event->setCmd($cmd);
    }
}
