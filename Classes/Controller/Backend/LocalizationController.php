<?php

declare(strict_types=1);

namespace WebVision\Deepl\Base\Controller\Backend;

use Psr\EventDispatcher\EventDispatcherInterface;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Symfony\Component\DependencyInjection\Attribute\Autoconfigure;
use TYPO3\CMS\Backend\Utility\BackendUtility;
use TYPO3\CMS\Core\Exception\SiteNotFoundException;
use TYPO3\CMS\Core\Http\JsonResponse;
use TYPO3\CMS\Core\Localization\LanguageService;
use TYPO3\CMS\Core\Site\Entity\Site;
use TYPO3\CMS\Core\Site\Entity\SiteLanguage;
use TYPO3\CMS\Core\Site\SiteFinder;
use TYPO3\CMS\Core\Utility\MathUtility;
use WebVision\Deepl\Base\Event\GetLocalizationModesEvent;
use WebVision\Deepl\Base\Localization\LocalizationModesCollection;

#[Autoconfigure(public: true)]
final class LocalizationController
{
    public function __construct(
        private EventDispatcherInterface $eventDispatcher,
        private SiteFinder $siteFinder,
    ) {
    }

    public function getLocalizationModes(ServerRequestInterface $request): ResponseInterface
    {
        $uid = $request->getQueryParams()['uid'] ?? null;
        if ($uid === null || !MathUtility::canBeInterpretedAsInteger($uid)) {
            return new JsonResponse([
                'message' => 'No valid page id given',
            ], 400);
        }
        $uid = (int)$uid;

        $languageUid = $request->getQueryParams()['languageId'] ?? null;
        if ($languageUid === null || !MathUtility::canBeInterpretedAsInteger($languageUid)) {
            return new JsonResponse([
                'message' => 'No valid language id given',
            ], 400);
        }
        $languageUid = (int)$languageUid;

        try {
            $site = $this->siteFinder->getSiteByPageId($uid);
        } catch (SiteNotFoundException) {
            return new JsonResponse([
                'message' => sprintf('Could not find site configuration for page %s', $uid),
            ], 400);
        }
        try {
            $siteLanguage = $site->getLanguageById($languageUid);
        } catch (\InvalidArgumentException $e) {
            return new JsonResponse([
                'message' => (
                    ($e->getCode() === 1522960188)
                    ? sprintf('Could not find siteLanguage %s (site: %s)  for page %s.', $languageUid, $site->getIdentifier(), $uid)
                    : sprintf('Could not find siteLanguage %s (site: %s)  for page %s. %s', $languageUid, $site->getIdentifier(), $uid, $e->getMessage())
                ),
            ], 400);
        }
        $pageTsConfig = BackendUtility::getPagesTSconfig($uid);
        $modes = $this->gatherLocalizationModes(
            $this->getLanguageService(),
            new LocalizationModesCollection(),
            $site,
            $siteLanguage,
            $pageTsConfig,
            $uid,
            $languageUid,
        );
        return new JsonResponse($modes->jsonSerialize(), 200);
    }

    /**
     * Dispatch event to gather available localization modes.
     *
     * @param array<string, mixed> $pageTsConfig
     */
    private function gatherLocalizationModes(
        LanguageService $languageService,
        LocalizationModesCollection $modes,
        Site $site,
        SiteLanguage $siteLanguage,
        array $pageTsConfig,
        int $pageUid,
        int $languageUid,
    ): LocalizationModesCollection {
        return $this->eventDispatcher->dispatch(new GetLocalizationModesEvent(
            site: $site,
            siteLanguage: $siteLanguage,
            pageTsConfig: $pageTsConfig,
            pageUid: $pageUid,
            languageId: $languageUid,
            modes: $modes,
            languageService: $languageService,
        ))->getModes();
    }

    private function getLanguageService(): LanguageService
    {
        return $GLOBALS['LANG'];
    }
}
