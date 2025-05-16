<?php

declare(strict_types=1);

namespace WebVision\Deepl\Base\Tests\Unit;

use PHPUnit\Framework\Attributes\Test;
use TYPO3\CMS\Core\Information\Typo3Version;
use TYPO3\TestingFramework\Core\Unit\UnitTestCase;

final class VersionCompatTest extends UnitTestCase
{
    private const ALLOWED_MAJOR_VERSIONS = [12, 13];

    #[Test]
    public function allowedMajorTypo3Version(): void
    {
        $this->assertContains((new Typo3Version())->getMajorVersion(), self::ALLOWED_MAJOR_VERSIONS);
    }
}
