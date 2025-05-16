<?php

declare(strict_types=1);

namespace WebVision\Deepl\Base\Tests\Functional;

use PHPUnit\Framework\Attributes\Test;
use SBUERK\TYPO3\Testing\TestCase\FunctionalTestCase;
use TYPO3\CMS\Core\Utility\ExtensionManagementUtility;

final class ExtensionLoadedTest extends FunctionalTestCase
{
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
}
