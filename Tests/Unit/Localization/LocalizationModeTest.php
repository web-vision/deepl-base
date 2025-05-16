<?php

declare(strict_types=1);

namespace WebVision\Deepl\Base\Tests\Unit\Localization;

use PHPUnit\Framework\Attributes\Test;
use TYPO3\TestingFramework\Core\Unit\UnitTestCase;
use WebVision\Deepl\Base\Localization\LocalizationMode;

final class LocalizationModeTest extends UnitTestCase
{
    #[Test]
    public function publicIdentifierPropertyReturnsConstructorValue(): void
    {
        $subject = $this->createTestSubject();
        $this->assertSame('some-identifier', $subject->identifier);
    }

    #[Test]
    public function publicTitlePropertyReturnsConstructorValue(): void
    {
        $subject = $this->createTestSubject();
        $this->assertSame('some title', $subject->title);
    }

    #[Test]
    public function publicDescriptionPropertyReturnsConstructorValue(): void
    {
        $subject = $this->createTestSubject();
        $this->assertSame('some description', $subject->description);
    }

    #[Test]
    public function publicIconPropertyReturnsConstructorValue(): void
    {
        $subject = $this->createTestSubject();
        $this->assertSame('some-icon', $subject->icon);
    }

    #[Test]
    public function publicBeforePropertyReturnsConstructorValue(): void
    {
        $subject = $this->createTestSubject();
        $this->assertSame(['before-other-identifier'], $subject->before);
    }

    #[Test]
    public function publicAfterPropertyReturnsConstructorValue(): void
    {
        $subject = $this->createTestSubject();
        $this->assertSame(['after-other-identifier'], $subject->after);
    }

    #[Test]
    public function publicIdentifierPropertyCannotBeSet(): void
    {
        $this->expectException(\Error::class);
        $this->expectExceptionMessage('Cannot modify readonly property WebVision\\Deepl\\Base\\Localization\\LocalizationMode::$identifier');
        $subject = $this->createTestSubject();
        /** @phpstan-ignore-next-line on purpose due to testing explicitly for invalid readonly write access */
        $subject->identifier = 'new-identifier';
    }

    #[Test]
    public function publicTitlePropertyCanBeSet(): void
    {
        $subject = $this->createTestSubject();
        $this->assertSame('some title', $subject->title);

        $subject->title = 'New Title';
        $this->assertSame('New Title', $subject->title);
    }

    #[Test]
    public function publicDescriptionPropertyCanBeSet(): void
    {
        $subject = $this->createTestSubject();
        $this->assertSame('some description', $subject->description);

        $subject->description = 'Changed localization mode description';
        $this->assertSame('Changed localization mode description', $subject->description);
    }

    #[Test]
    public function publicIconPropertyCanBeSet(): void
    {
        $subject = $this->createTestSubject();
        $this->assertSame('some-icon', $subject->icon);

        $subject->icon = 'another-icon';
        $this->assertSame('another-icon', $subject->icon);
    }

    #[Test]
    public function publicBeforePropertyCanBeSetToEmptyArray(): void
    {
        $subject = $this->createTestSubject();
        $this->assertSame(['before-other-identifier'], $subject->before);

        $subject->before = [];
        $this->assertSame([], $subject->before);
    }

    #[Test]
    public function publicBeforePropertyCanGetNewItemsAppended(): void
    {
        $subject = $this->createTestSubject();
        $this->assertSame(['before-other-identifier'], $subject->before);

        $subject->before[] = 'appended-identifier';
        $this->assertSame(['before-other-identifier', 'appended-identifier'], $subject->before);
    }

    #[Test]
    public function publicAfterPropertyCanBeSetToEmptyArray(): void
    {
        $subject = $this->createTestSubject();
        $this->assertSame(['after-other-identifier'], $subject->after);

        $subject->after = [];
        $this->assertSame([], $subject->after);
    }

    #[Test]
    public function publicAfterPropertyCanGetNewItemsAppended(): void
    {
        $subject = $this->createTestSubject();
        $this->assertSame(['after-other-identifier'], $subject->after);

        $subject->after[] = 'appended-identifier';
        $this->assertSame(['after-other-identifier', 'appended-identifier'], $subject->after);
    }

    #[Test]
    public function jsonEncodeReturnsExpectedString(): void
    {
        $expectedString = '{"identifier":"some-identifier","title":"some title","description":"some description","icon":"some-icon","before":["before-other-identifier"],"after":["after-other-identifier"],"restrictedSourceLanguageIds":null}';
        $this->assertSame($expectedString, \json_encode($this->createTestSubject(), JSON_THROW_ON_ERROR));
    }

    private function createTestSubject(): LocalizationMode
    {
        return new LocalizationMode(
            identifier: 'some-identifier',
            title: 'some title',
            description: 'some description',
            icon: 'some-icon',
            before: ['before-other-identifier'],
            after: ['after-other-identifier'],
        );
    }
}
