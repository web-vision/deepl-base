<?php

declare(strict_types=1);

namespace WebVision\Deepl\Base\Tests\Functional;

use PHPUnit\Framework\Attributes\Group;
use PHPUnit\Framework\Attributes\Test;
use SBUERK\TYPO3\Testing\TestCase\FunctionalTestCase;
use TYPO3\CMS\Core\Information\Typo3Version;
use TYPO3\CMS\Core\Utility\ExtensionManagementUtility;

final class ExtensionLoadedTest extends FunctionalTestCase
{
    private const ALLOWED_MAJOR_VERSIONS = [13, 14];

    protected array $testExtensionsToLoad = [
        'web-vision/deepl-base',
    ];

    #[Test]
    public function isLoadedExtensionKey(): void
    {
        $this->assertTrue(ExtensionManagementUtility::isLoaded('deepl_base'));
    }

    #[Test]
    public function isLoadedComposerPackageName(): void
    {
        $this->assertTrue(ExtensionManagementUtility::isLoaded('web-vision/deepl-base'));
    }

    #[Test]
    public function allowedMajorTypo3Version(): void
    {
        $this->assertContains((new Typo3Version())->getMajorVersion(), self::ALLOWED_MAJOR_VERSIONS);
    }

    #[Group('not-core-14')]
    #[Test]
    public function verifyCore13(): void
    {
        $this->assertSame(13, (new Typo3Version())->getMajorVersion());
    }

    #[Group('not-core-13')]
    #[Test]
    public function verifyCore14(): void
    {
        $this->assertSame(14, (new Typo3Version())->getMajorVersion());
    }
}
