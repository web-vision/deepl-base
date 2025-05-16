<?php

declare(strict_types=1);

namespace WebVision\Deepl\Base\Controller\Backend;

use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Symfony\Component\DependencyInjection\Attribute\Autoconfigure;
use TYPO3\CMS\Backend\Controller\Page\LocalizationController as Typo3LocalizationController;
use TYPO3\CMS\Backend\Utility\BackendUtility;
use TYPO3\CMS\Core\DataHandling\DataHandler;
use TYPO3\CMS\Core\Exception\SiteNotFoundException;
use TYPO3\CMS\Core\Http\JsonResponse;
use TYPO3\CMS\Core\Localization\LanguageService;
use TYPO3\CMS\Core\Site\Entity\Site;
use TYPO3\CMS\Core\Site\Entity\SiteLanguage;
use TYPO3\CMS\Core\Site\SiteFinder;
use TYPO3\CMS\Core\Utility\GeneralUtility;
use TYPO3\CMS\Core\Utility\MathUtility;
use WebVision\Deepl\Base\Event\GetLocalizationModesEvent;
use WebVision\Deepl\Base\Event\LocalizationProcessPrepareDataHandlerCommandMapEvent;
use WebVision\Deepl\Base\Localization\LocalizationMode;
use WebVision\Deepl\Base\Localization\LocalizationModesCollection;

/**
 * Extend TYPO3 LocalizationController and enhance it with dynamic localization mode handling
 * by providing an PSR-14 event-based information gathering and handling.
 */
#[Autoconfigure(public: true)]
final class LocalizationController extends Typo3LocalizationController
{
    protected ?SiteFinder $siteFinder = null;

    public function injectSiteFinder(SiteFinder $siteFinder): void
    {
        $this->siteFinder = $siteFinder;
    }

    /**
     * NEW endpoint / method added in deepl-base.
     */
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
            $modes = $this->gatherLocalizationModes($uid, $languageUid);
        } catch (\Throwable $t) {
            return new JsonResponse([
                'message' => $t->getMessage(),
            ], 400);
        }

        return new JsonResponse($modes->jsonSerialize(), 200);
    }

    private function gatherLocalizationModes(int $pageId, int $languageId): LocalizationModesCollection
    {
        try {
            $site = $this->getSiteFinder()->getSiteByPageId($pageId);
        } catch (SiteNotFoundException) {
            // @todo Use concrete exception class.
            throw new \InvalidArgumentException(
                sprintf('Could not find site configuration for page %s', $pageId),
                1748838416,
            );
        }
        try {
            $siteLanguage = $site->getLanguageById($languageId);
        } catch (\InvalidArgumentException $e) {
            // @todo Use concrete exception class.
            throw new \InvalidArgumentException(
                (($e->getCode() === 1522960188)
                    ? sprintf('Could not find siteLanguage %s (site: %s)  for page %s.', $languageId, $site->getIdentifier(), $pageId)
                    : sprintf('Could not find siteLanguage %s (site: %s)  for page %s. %s', $languageId, $site->getIdentifier(), $pageId, $e->getMessage())),
                1748838493,
            );
        }
        $pageTsConfig = BackendUtility::getPagesTSconfig($pageId);
        return $this->dispatchGetLocalizationModesEvent(
            $this->getLanguageService(),
            new LocalizationModesCollection(),
            $site,
            $siteLanguage,
            $pageTsConfig,
            $pageId,
            $languageId,
        );
    }

    public function localizeRecords(ServerRequestInterface $request): ResponseInterface
    {
        $params = $request->getQueryParams();
        if (!isset($params['pageId'], $params['srcLanguageId'], $params['destLanguageId'], $params['action'], $params['uidList'])) {
            return new JsonResponse(null, 400);
        }
        /** @var array{pageId: int, action: string, srcLanguageId: int, destLanguageId: int, uidList: int[]} $params */
        $pageId = (int)$params['pageId'];
        $srcLanguageId = (int)$params['srcLanguageId'];
        $destLanguageId = (int)$params['destLanguageId'];

        try {
            $localizationModes = $this->gatherLocalizationModes($pageId, $destLanguageId);
        } catch (\Throwable $t) {
            return new JsonResponse([
                'message' => $t->getMessage(),
            ], 400);
        }

        if (!$localizationModes->hasIdentifier($params['action'])) {
            return new JsonResponse([
                'message' => 'Invalid action "' . $params['action'] . '" called.',
            ], 400);
        }
        $localizationMode = $localizationModes->getIdentifier($params['action']);
        if ($localizationMode === null) {
            return new JsonResponse([
                'message' => 'Invalid action "' . $params['action'] . '" called, cannot determine localization mode.',
            ], 400);
        }

        // Filter transmitted but invalid uids
        $params['uidList'] = $this->filterInvalidUids(
            (int)$params['pageId'],
            (int)$params['destLanguageId'],
            (int)$params['srcLanguageId'],
            $params['uidList']
        );

        $this->customProcess($localizationModes, $localizationMode, $pageId, $srcLanguageId, $destLanguageId, $params);

        return (new JsonResponse([
            'message' => $params['action'],
        ]))->withHeader('x-deepl-base', $params['action']);
    }

    /**
     * Processes the localization actions
     *
     * Custom {@see self::process()} method required due to incompatible signature change.
     *
     * @param array{pageId: int, action: string, srcLanguageId: int, destLanguageId: int, uidList: int[]} $params
     */
    protected function customProcess(LocalizationModesCollection $localizationModes, LocalizationMode $localizationMode, int $pageId, int $srcLanguageId, int $destLanguageId, array $params): void
    {
        // Build command map
        $cmd = [
            'tt_content' => [],
        ];
        $action = $params['action'];
        $uidList = $params['uidList'];
        $cmd = $this->eventDispatcher->dispatch(new LocalizationProcessPrepareDataHandlerCommandMapEvent(
            $localizationModes,
            $localizationMode,
            $action,
            $pageId,
            $srcLanguageId,
            $destLanguageId,
            $uidList,
            $cmd,
        ))->getCmd();

        $dataHandler = GeneralUtility::makeInstance(DataHandler::class);
        $dataHandler->start([], $cmd);
        $dataHandler->process_cmdmap();
    }

    /**
     * Dispatch event to gather available localization modes.
     *
     * @param array<string, mixed> $pageTsConfig
     */
    private function dispatchGetLocalizationModesEvent(
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

    private function getSiteFinder(): SiteFinder
    {
        return $this->siteFinder ??= GeneralUtility::makeInstance(SiteFinder::class);
    }

    public function getUsedLanguagesInPage(ServerRequestInterface $request): ResponseInterface
    {
        // @todo for development/debug purpose
        return parent::getUsedLanguagesInPage($request);
    }

    public function getRecordLocalizeSummary(ServerRequestInterface $request): ResponseInterface
    {
        // @todo for development/debug purpose
        return parent::getRecordLocalizeSummary($request);
    }
}
