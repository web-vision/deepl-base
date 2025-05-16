<?php

declare(strict_types=1);

namespace WebVision\Deepl\Base\Tests\Functional\Controller\Backend;

use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\Attributes\Test;
use SBUERK\TYPO3\Testing\SiteHandling\SiteBasedTestTrait;
use SBUERK\TYPO3\Testing\TestCase\FunctionalTestCase;
use TYPO3\CMS\Backend\Utility\BackendUtility;
use TYPO3\CMS\Core\Localization\LanguageService;
use TYPO3\CMS\Core\Site\SiteFinder;
use TYPO3\CMS\Core\Utility\GeneralUtility;
use WebVision\Deepl\Base\Controller\Backend\LocalizationController;
use WebVision\Deepl\Base\Localization\LocalizationModesCollection;

final class LocalizationControllerTest extends FunctionalTestCase
{
    use SiteBasedTestTrait;

    protected const LANGUAGE_PRESETS = [
        'EN' => ['id' => 0, 'title' => 'English', 'locale' => 'en_US.UTF8'],
        'DE' => ['id' => 1, 'title' => 'Deutsch', 'locale' => 'de_DE.UTF-8'],
        'FR' => ['id' => 2, 'title' => 'Französisch', 'locale' => 'fr_FR.UTF-8'],
    ];

    protected array $testExtensionsToLoad = [
        'web-vision/deepl-base',
    ];

    #[Test]
    public function canBeRetrievedFromDependencyInjectionContainer(): void
    {
        $this->get(LocalizationController::class);
    }

    #[Test]
    public function canBeCreatedWithNewKeywordRetrievingDependencyFromDIContainer(): void
    {
        $this->createSubject();
    }

    public static function dispatchGetLocalizationModesEventReturnsExpectedDefaultModesBasedOnPageSetupDataSets(): \Generator
    {
        yield 'copy and translate modes - not set PageTSConfig options' => [
            'fixtureFile' => 'CopyAndTranslateModes_notSet.csv',
            'expectedModesJsonString' => \json_encode(
                [
                    [
                        'identifier' => 'localize',
                        'title' => 'LLL:EXT:backend/Resources/Private/Language/locallang_layout.xlf:localize.wizard.button.translate',
                        'description' => 'LLL:EXT:backend/Resources/Private/Language/locallang_layout.xlf:localize.educate.translate',
                        'icon' => 'actions-localize',
                        'before' => ['copy'],
                        'after' => [],
                        'restrictedSourceLanguageIds' => null,
                    ],
                    [
                        'identifier' => 'copy',
                        'title' => 'LLL:EXT:backend/Resources/Private/Language/locallang_layout.xlf:localize.wizard.button.copy',
                        'description' => 'LLL:EXT:backend/Resources/Private/Language/locallang_layout.xlf:localize.educate.copy',
                        'icon' => 'actions-edit-copy',
                        'before' => [],
                        'after' => ['localize'],
                        'restrictedSourceLanguageIds' => null,
                    ],
                ],
                JSON_THROW_ON_ERROR,
            ),
        ];

        yield 'copy and translate modes - both set PageTSConfig options' => [
            'fixtureFile' => 'CopyAndTranslateModes_bothSet.csv',
            'expectedModesJsonString' => \json_encode(
                [
                    [
                        'identifier' => 'localize',
                        'title' => 'LLL:EXT:backend/Resources/Private/Language/locallang_layout.xlf:localize.wizard.button.translate',
                        'description' => 'LLL:EXT:backend/Resources/Private/Language/locallang_layout.xlf:localize.educate.translate',
                        'icon' => 'actions-localize',
                        'before' => ['copy'],
                        'after' => [],
                        'restrictedSourceLanguageIds' => null,
                    ],
                    [
                        'identifier' => 'copy',
                        'title' => 'LLL:EXT:backend/Resources/Private/Language/locallang_layout.xlf:localize.wizard.button.copy',
                        'description' => 'LLL:EXT:backend/Resources/Private/Language/locallang_layout.xlf:localize.educate.copy',
                        'icon' => 'actions-edit-copy',
                        'before' => [],
                        'after' => ['localize'],
                        'restrictedSourceLanguageIds' => null,
                    ],
                ],
                JSON_THROW_ON_ERROR,
            ),
        ];

        yield 'copy and translate modes - both disabled PageTSConfig options' => [
            'fixtureFile' => 'CopyAndTranslateModes_bothDisabled.csv',
            'expectedModesJsonString' => \json_encode(
                [],
                JSON_THROW_ON_ERROR,
            ),
        ];

        yield 'copy and translate modes - copy disabled' => [
            'fixtureFile' => 'CopyAndTranslateModes_copyDisabled.csv',
            'expectedModesJsonString' => \json_encode(
                [
                    [
                        'identifier' => 'localize',
                        'title' => 'LLL:EXT:backend/Resources/Private/Language/locallang_layout.xlf:localize.wizard.button.translate',
                        'description' => 'LLL:EXT:backend/Resources/Private/Language/locallang_layout.xlf:localize.educate.translate',
                        'icon' => 'actions-localize',
                        'before' => ['copy'],
                        'after' => [],
                        'restrictedSourceLanguageIds' => null,
                    ],
                ],
                JSON_THROW_ON_ERROR,
            ),
        ];

        yield 'copy and translate modes - translate disabled' => [
            'fixtureFile' => 'CopyAndTranslateModes_translateDisabled.csv',
            'expectedModesJsonString' => \json_encode(
                [
                    [
                        'identifier' => 'copy',
                        'title' => 'LLL:EXT:backend/Resources/Private/Language/locallang_layout.xlf:localize.wizard.button.copy',
                        'description' => 'LLL:EXT:backend/Resources/Private/Language/locallang_layout.xlf:localize.educate.copy',
                        'icon' => 'actions-edit-copy',
                        'before' => [],
                        'after' => ['localize'],
                        'restrictedSourceLanguageIds' => null,
                    ],
                ],
                JSON_THROW_ON_ERROR,
            ),
        ];
    }

    #[DataProvider('dispatchGetLocalizationModesEventReturnsExpectedDefaultModesBasedOnPageSetupDataSets')]
    #[Test]
    public function dispatchGetLocalizationModesEventReturnsExpectedDefaultModesBasedOnPageSetup(
        string $fixtureFile,
        string $expectedModesJsonString,
    ): void {
        $this->importCSVDataSet(__DIR__ . sprintf('/Fixtures/GatherLocalizationModes/%s', $fixtureFile));
        $this->setUpFrontendRootPage(1, [], [], false);
        $this->writeSiteConfiguration(
            identifier: 'acme',
            site: $this->buildSiteConfiguration(rootPageId: 1),
            languages: [
                $this->buildDefaultLanguageConfiguration(
                    identifier: 'EN',
                    base: '/',
                ),
                $this->buildLanguageConfiguration(
                    identifier: 'DE',
                    base: '/de/',
                    fallbackIdentifiers: ['EN'],
                    fallbackType: 'strict',
                ),
            ],
        );
        $localizationService = $this->createStub(LanguageService::class);
        $localizationService->method('sL')->willReturnArgument(0);
        $modes = new LocalizationModesCollection();
        $site = GeneralUtility::makeInstance(SiteFinder::class)->getSiteByIdentifier('acme');
        $siteLanguage = $site->getLanguageById(1);
        $pageTsConfig = BackendUtility::getPagesTSconfig(3);
        $controller = $this->createSubject();
        $reflectionController = new \ReflectionClass($controller);
        $modes = $reflectionController->getMethod('dispatchGetLocalizationModesEvent')->invoke(
            $controller,
            $localizationService,
            $modes,
            $site,
            $siteLanguage,
            $pageTsConfig,
            3,
            $siteLanguage->getLanguageId(),
        );
        $this->assertSame($expectedModesJsonString, \json_encode($modes, JSON_THROW_ON_ERROR));
    }

    private function createSubject(): LocalizationController
    {
        return $this->get(LocalizationController::class);
    }
}
