<?php

declare(strict_types=1);

namespace WebVision\Deepl\Base\Event;

use TYPO3\CMS\Core\Localization\LanguageService;
use TYPO3\CMS\Core\Site\Entity\Site;
use TYPO3\CMS\Core\Site\Entity\SiteLanguage;
use WebVision\Deepl\Base\Localization\LocalizationModesCollection;

/**
 * PSR-14 event dispatched in {@see LocalizationController::getLocalizationModes()} to gather
 * available localization modes displayed within the localization modal in PageLayout module.
 *
 * Can be used to add additional localization modes or remove previously registered modes.
 */
final class GetLocalizationModesEvent
{
    /**
     * @param array<string, mixed> $pageTsConfig
     */
    public function __construct(
        private readonly Site $site,
        private readonly SiteLanguage $siteLanguage,
        private readonly array $pageTsConfig,
        private readonly int $pageUid,
        private readonly int $languageId,
        private readonly LocalizationModesCollection $modes,
        private readonly LanguageService $languageService,
    ) {
    }

    public function getSite(): Site
    {
        return $this->site;
    }

    /**
     * @return array<string, mixed>
     */
    public function getPageTsConfig(): array
    {
        return $this->pageTsConfig;
    }

    public function getPageUid(): int
    {
        return $this->pageUid;
    }

    public function getModes(): LocalizationModesCollection
    {
        return $this->modes;
    }

    public function getSiteLanguage(): SiteLanguage
    {
        return $this->siteLanguage;
    }

    public function getLanguageId(): int
    {
        return $this->languageId;
    }

    public function getLanguageService(): LanguageService
    {
        return $this->languageService;
    }
}
