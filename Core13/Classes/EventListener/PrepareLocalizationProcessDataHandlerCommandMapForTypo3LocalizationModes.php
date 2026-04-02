<?php

declare(strict_types=1);

namespace WebVision\Deepl\Base\Core13\EventListener;

use TYPO3\CMS\Backend\Controller\Page\LocalizationController;
use TYPO3\CMS\Core\Attribute\AsEventListener;
use WebVision\Deepl\Base\Event\LocalizationProcessPrepareDataHandlerCommandMapEvent;

/**
 * Prepare DataHandler command map for default TYPO3 localization modes, dispatched in
 * {@see \WebVision\Deepl\Base\Core13\Controller\Backend\LocalizationController::customProcess()}.
 *
 * @depreacted used only for TYPO3 v13 compatibility and will not be dispatched for TYPO3 v14
 *             and should be resort to TYPO3 v14 localization handler feature provided by the
 *             TYPO3 Core.
 */
final class PrepareLocalizationProcessDataHandlerCommandMapForTypo3LocalizationModes
{
    #[AsEventListener(
        identifier: 'deepl-base/process-default-typo3-localization-modes',
    )]
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
