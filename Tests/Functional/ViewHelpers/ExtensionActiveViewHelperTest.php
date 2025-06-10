<?php

declare(strict_types=1);

namespace WebVision\Deepl\Base\Tests\Functional\ViewHelpers;

use Generator;
use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\Attributes\Test;
use TYPO3\CMS\Core\Information\Typo3Version;
use TYPO3\CMS\Fluid\Core\Rendering\RenderingContextFactory;
use TYPO3\TestingFramework\Core\Functional\FunctionalTestCase;
use TYPO3Fluid\Fluid\Core\Cache\FluidCacheInterface;
use TYPO3Fluid\Fluid\Core\Cache\SimpleFileCache;
use TYPO3Fluid\Fluid\Core\Rendering\RenderingContext;
use TYPO3Fluid\Fluid\View\TemplateView;

final class ExtensionActiveViewHelperTest extends FunctionalTestCase
{
    protected bool $initializeDatabase = false;

    protected array $testExtensionsToLoad = [
        'web-vision/deepl-base',
    ];

    protected static FluidCacheInterface $cache;

    /**
     *  Absolute path to cache directory
     */
    protected static string $cachePath;

    public static function setUpBeforeClass(): void
    {
        self::$cachePath = sys_get_temp_dir() . '/' . 'fluid-functional-tests-' . sha1(__CLASS__);
        mkdir(self::$cachePath);
        self::$cache = (new SimpleFileCache(self::$cachePath));
    }

    public static function tearDownAfterClass(): void
    {
        self::$cache->flush();
        rmdir(self::$cachePath);
    }

    protected function setUp(): void
    {
        if ((new Typo3Version())->getMajorVersion() >= 13) {
            $this->coreExtensionsToLoad[] = 'typo3/cms-install';
        }
        parent::setUp();
    }

    /**
     * @return Generator<string, array{template: string, variables: array<string, mixed>, expected: string}>
     */
    public static function renderDataProvider(): Generator
    {
        yield 'extension name empty, await else' => [
            'template' => '<deeplbase:extensionActive extension="" then="thenArgument" else="elseArgument" />',
            'variables' => [],
            'expected' => 'elseArgument',
        ];
        yield 'extension set to own, await then' => [
            'template' => '<deeplbase:extensionActive extension="deepl_base" then="thenArgument" else="elseArgument" />',
            'variables' => [],
            'expected' => 'thenArgument',
        ];

        yield 'extension set to non existent, await else' => [
            'template' => '<deeplbase:extensionActive extension="non_existent" then="thenArgument" else="elseArgument" />',
            'variables' => [],
            'expected' => 'elseArgument',
        ];

        yield 'extension provided as undefined fluid variable placeholder, await else' => [
            'template' => '<deeplbase:extensionActive extension="{someUndefinedVariable}" then="thenArgument" else="elseArgument" />',
            'variables' => [],
            'expected' => 'elseArgument',
        ];
    }

    /**
     * @param array<string, mixed> $variables
     */
    #[DataProvider('renderDataProvider')]
    #[Test]
    public function render(string $template, array $variables, string $expected): void
    {
        /** @var RenderingContext $context */
        $context = $this->get(RenderingContextFactory::class)->create();
        $context->getViewHelperResolver()->addNamespace('deeplbase', 'WebVision\\Deepl\\Base\\ViewHelpers');
        $context->setCache(self::$cache);
        $context->getTemplatePaths()->setTemplateSource($template);
        foreach ($variables as $key => $value) {
            $context->getVariableProvider()->add($key, $value);
        }
        $this->assertSame($expected, (new TemplateView($context))->render());
    }
}
