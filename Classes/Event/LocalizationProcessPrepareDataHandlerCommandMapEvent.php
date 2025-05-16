<?php

declare(strict_types=1);

namespace WebVision\Deepl\Base\Event;

use WebVision\Deepl\Base\Controller\Backend\LocalizationController;
use WebVision\Deepl\Base\Localization\LocalizationMode;
use WebVision\Deepl\Base\Localization\LocalizationModesCollection;

/**
 * This event is fired in {@see LocalizationController::customProcess()} and can be used to prepare the
 * DataHandler map based on custom localization modes used to call {@see DataHandler}.
 *
 * Allows to implement custom localization modes in the localization model and handling together with
 * the PSR-14 {@see GetLocalizationModesEvent} also dispatched in {@see LocalizationController}.
 */
final class LocalizationProcessPrepareDataHandlerCommandMapEvent
{
    /**
     * @param int $destLanguageId
     * @param int[] $uidList
     * @param array<string, array<int, array<string, string|int|bool>>> $cmd
     */
    public function __construct(
        private readonly LocalizationModesCollection $localizationModes,
        private readonly LocalizationMode $localizationMode,
        private readonly string $action,
        private readonly int $pageId,
        private readonly int $srcLanguageId,
        private readonly int $destLanguageId,
        private readonly array $uidList,
        private array $cmd,
    ) {
    }

    public function getAction(): string
    {
        return $this->action;
    }

    public function getPageId(): int
    {
        return $this->pageId;
    }

    public function getLocalizationModes(): LocalizationModesCollection
    {
        return $this->localizationModes;
    }

    public function getLocalizationMode(): LocalizationMode
    {
        return $this->localizationMode;
    }

    public function getSrcLanguageId(): int
    {
        return $this->srcLanguageId;
    }

    public function getDestLanguageId(): int
    {
        return $this->destLanguageId;
    }

    /**
     * @return int[]
     */
    public function getUidList(): array
    {
        return $this->uidList;
    }

    /**
     * @return array<string, array<int, array<string, string|int|bool>>>
     */
    public function getCmd(): array
    {
        return $this->cmd;
    }

    /**
     * @param array<string, array<int, array<string, string|int|bool>>> $cmd
     */
    public function setCmd(array $cmd): void
    {
        $this->cmd = $cmd;
    }
}
