<?php

declare(strict_types=1);

namespace WebVision\Deepl\Base\Tests\Unit\Localization;

use PHPUnit\Framework\Attributes\Test;
use TYPO3\TestingFramework\Core\Unit\UnitTestCase;
use WebVision\Deepl\Base\Localization\LocalizationMode;
use WebVision\Deepl\Base\Localization\LocalizationModesCollection;

final class LocalizationModesCollectionTest extends UnitTestCase
{
    #[Test]
    public function emptyCollectionCanBeCreated(): void
    {
        new LocalizationModesCollection();
    }

    #[Test]
    public function countMethodOnEmptyCollectionReturnsZeroInteger(): void
    {
        $this->assertSame(0, (new LocalizationModesCollection())->count());
    }

    #[Test]
    public function countMethodCanBeUsedOnEmptyCollectionReturningZeroInteger(): void
    {
        $this->assertCount(0, (new LocalizationModesCollection()));
    }

    #[Test]
    public function singleModeCanBeAddedToCollection(): void
    {
        $modeOne = new LocalizationMode(
            identifier: 'mode-one',
            title: 'Mode 1',
            description: 'Mode One localization mode',
            icon: 'icon-one',
            before: [],
            after: [],
        );

        $subject = new LocalizationModesCollection();
        $this->assertCount(0, $subject);

        $subject->add($modeOne);
        $this->assertCount(1, $subject);
    }

    #[Test]
    public function twoModesCanBeAddedWithTwoAddCallsToCollection(): void
    {
        $modeOne = new LocalizationMode(
            identifier: 'mode-one',
            title: 'Mode 1',
            description: 'Mode One localization mode',
            icon: 'icon-icon',
            before: [],
            after: [],
        );
        $modeTwo = new LocalizationMode(
            identifier: 'mode-two',
            title: 'Mode 2',
            description: 'Mode Two localization mode',
            icon: 'icon-two',
            before: [],
            after: [],
        );

        $subject = new LocalizationModesCollection();
        $this->assertCount(0, $subject);

        $subject->add($modeOne);
        $this->assertCount(1, $subject);

        $subject->add($modeTwo);
        $this->assertCount(2, $subject);
    }

    #[Test]
    public function twoModesCanBeAddedWithOneAddCallToCollection(): void
    {
        $modeOne = new LocalizationMode(
            identifier: 'mode-one',
            title: 'Mode 1',
            description: 'Mode One localization mode',
            icon: 'icon-one',
            before: [],
            after: [],
        );
        $modeTwo = new LocalizationMode(
            identifier: 'mode-two',
            title: 'Mode 2',
            description: 'Mode Two localization mode',
            icon: 'icon-two',
            before: [],
            after: [],
        );

        $subject = new LocalizationModesCollection();
        $this->assertCount(0, $subject);

        $subject->add($modeOne, $modeTwo);
        $this->assertCount(2, $subject);
    }

    #[Test]
    public function twoModesCanBeAddedWithOneAddCallAndArrayExpandingToCollection(): void
    {
        $modeOne = new LocalizationMode(
            identifier: 'mode-one',
            title: 'Mode 1',
            description: 'Mode One localization mode',
            icon: 'icon-one',
            before: [],
            after: [],
        );
        $modeTwo = new LocalizationMode(
            identifier: 'mode-two',
            title: 'Mode 2',
            description: 'Mode Two localization mode',
            icon: 'icon-two',
            before: [],
            after: [],
        );
        $arrayOfModes = [$modeOne, $modeTwo];

        $subject = new LocalizationModesCollection();
        $this->assertCount(0, $subject);

        $subject->add(...$arrayOfModes);
        $this->assertCount(2, $subject);
    }

    #[Test]
    public function modesAreAddedWithIdentifierAsArrayKeyToInternalProperty(): void
    {
        $modeOne = new LocalizationMode(
            identifier: 'mode-one',
            title: 'Mode 1',
            description: 'Mode One localization mode',
            icon: 'icon-one',
            before: [],
            after: [],
        );
        $modeTwo = new LocalizationMode(
            identifier: 'mode-two',
            title: 'Mode 2',
            description: 'Mode Two localization mode',
            icon: 'icon-two',
            before: [],
            after: [],
        );
        $subject = new LocalizationModesCollection();
        $subject->add($modeOne, $modeTwo);

        $reflectionClass = new \ReflectionClass($subject);
        $internalModesProperty = $reflectionClass->getProperty('modes')->getValue($subject);
        $this->assertIsArray($internalModesProperty);
        $this->assertCount(2, $internalModesProperty);
        $this->assertSame(['mode-one', 'mode-two'], array_keys($internalModesProperty));
    }

    #[Test]
    public function singeModeCanBeSetToCollection(): void
    {
        $modeOne = new LocalizationMode(
            identifier: 'mode-one',
            title: 'Mode 1',
            description: 'Mode One localization mode',
            icon: 'icon-one',
            before: [],
            after: [],
        );
        $subject = new LocalizationModesCollection();
        $subject->set($modeOne);
        $this->assertSame(1, $subject->count());
        $this->assertCount(1, $subject);
        $this->assertSame([$modeOne], $subject->modes());
    }

    #[Test]
    public function twoModesCanBeSetToCollectionAsSingleArguments(): void
    {
        $modeOne = new LocalizationMode(
            identifier: 'mode-one',
            title: 'Mode 1',
            description: 'Mode One localization mode',
            icon: 'icon-one',
            before: [],
            after: [],
        );
        $modeTwo = new LocalizationMode(
            identifier: 'mode-two',
            title: 'Mode 2',
            description: 'Mode Two localization mode',
            icon: 'icon-two',
            before: [],
            after: [],
        );
        $subject = new LocalizationModesCollection();
        $subject->set($modeOne, $modeTwo);
        $this->assertSame(2, $subject->count());
        $this->assertCount(2, $subject);
        $this->assertSame([$modeOne, $modeTwo], $subject->modes());
    }

    #[Test]
    public function twoModesCanBeSetToCollectionUsingArrayExpansion(): void
    {
        $modeOne = new LocalizationMode(
            identifier: 'mode-one',
            title: 'Mode 1',
            description: 'Mode One localization mode',
            icon: 'icon-one',
            before: [],
            after: [],
        );
        $modeTwo = new LocalizationMode(
            identifier: 'mode-two',
            title: 'Mode 2',
            description: 'Mode Two localization mode',
            icon: 'icon-two',
            before: [],
            after: [],
        );
        $arrayOfModes = [$modeOne, $modeTwo];
        $subject = new LocalizationModesCollection();
        $subject->set(...$arrayOfModes);
        $this->assertSame(2, $subject->count());
        $this->assertCount(2, $subject);
        $this->assertSame($arrayOfModes, $subject->modes());
    }

    #[Test]
    public function secondSetCallToCollectionResetsAlreadySetModes(): void
    {
        $modeOne = new LocalizationMode(
            identifier: 'mode-one',
            title: 'Mode 1',
            description: 'Mode One localization mode',
            icon: 'icon-one',
            before: [],
            after: [],
        );
        $modeTwo = new LocalizationMode(
            identifier: 'mode-two',
            title: 'Mode 2',
            description: 'Mode Two localization mode',
            icon: 'icon-two',
            before: [],
            after: [],
        );
        $subject = new LocalizationModesCollection();

        $subject->set($modeOne);
        $this->assertSame(1, $subject->count());
        $this->assertCount(1, $subject);
        $this->assertSame([$modeOne], $subject->modes());

        $subject->set($modeTwo);
        $this->assertSame(1, $subject->count());
        $this->assertCount(1, $subject);
        $this->assertSame([$modeTwo], $subject->modes());
    }

    #[Test]
    public function collectionWithMultipleModesCanBeLoopedUsingForeach(): void
    {
        $modeOne = new LocalizationMode(
            identifier: 'mode-one',
            title: 'Mode 1',
            description: 'Mode One localization mode',
            icon: 'icon-one',
            before: [],
            after: [],
        );
        $modeTwo = new LocalizationMode(
            identifier: 'mode-two',
            title: 'Mode 2',
            description: 'Mode Two localization mode',
            icon: 'icon-two',
            before: [],
            after: [],
        );
        $expectedModeKeys = ['mode-one', 'mode-two'];

        $subject = new LocalizationModesCollection();
        $subject->set($modeOne, $modeTwo);

        $keysFirstLoop = [];
        foreach ($subject as $identifier => $mode) {
            $keysFirstLoop[] = $identifier;
        }
        $this->assertSame($expectedModeKeys, $keysFirstLoop);

        $keysSecondLoop = [];
        foreach ($subject as $identifier => $mode) {
            $this->assertSame($identifier, $mode->identifier);
            $keysSecondLoop[] = $mode->identifier;
        }
        $this->assertSame($expectedModeKeys, $keysSecondLoop);
    }

    #[Test]
    public function collectionWithMultipleModesCanBeLoopedUsingForeachAndLoopBreakResetsStateCorrectly(): void
    {
        $modeOne = new LocalizationMode(
            identifier: 'mode-one',
            title: 'Mode 1',
            description: 'Mode One localization mode',
            icon: 'icon-one',
            before: [],
            after: [],
        );
        $modeTwo = new LocalizationMode(
            identifier: 'mode-two',
            title: 'Mode 2',
            description: 'Mode Two localization mode',
            icon: 'icon-two',
            before: [],
            after: [],
        );
        $expectedModeKeys = ['mode-one', 'mode-two'];
        $expectedModeKeysLoopOne = ['mode-one'];

        $subject = new LocalizationModesCollection();
        $subject->set($modeOne, $modeTwo);

        $keysFirstLoop = [];
        foreach ($subject as $identifier => $mode) {
            $keysFirstLoop[] = $identifier;
            break;
        }
        $this->assertSame($expectedModeKeysLoopOne, $keysFirstLoop);

        $keysSecondLoop = [];
        foreach ($subject as $identifier => $mode) {
            $this->assertSame($identifier, $mode->identifier);
            $keysSecondLoop[] = $mode->identifier;
        }
        $this->assertSame($expectedModeKeys, $keysSecondLoop);
    }

    #[Test]
    public function beforeAndAfterSortingWorks(): void
    {
        $expectedKeys = [
            'mode-one',
            'mode-three',
            'mode-two',
            'mode-four',
            'mode-last',
        ];
        $modes = [
            new LocalizationMode(
                identifier: 'mode-three',
                title: 'Mode 3',
                description: 'Mode Three localization mode',
                icon: 'icon-three',
                before: ['mode-two'],
                after: ['mode-one'],
            ),
            new LocalizationMode(
                identifier: 'mode-four',
                title: 'Mode 4',
                description: 'Mode Four localization mode',
                icon: 'icon-four',
                before: [],
                after: [
                    'mode-three',
                    'mode-two',
                ],
            ),
            new LocalizationMode(
                identifier: 'mode-two',
                title: 'Mode 2',
                description: 'Mode Two localization mode',
                icon: 'icon-two',
                before: [],
                after: ['mode-one'],
            ),
            new LocalizationMode(
                identifier: 'mode-one',
                title: 'Mode 1',
                description: 'Mode One localization mode',
                icon: 'icon-two',
                before: [],
                after: [],
            ),
            new LocalizationMode(
                identifier: 'mode-last',
                title: 'Mode Last',
                description: 'Mode Last localization mode',
                icon: 'icon-last',
                before: [],
                after: [
                    'mode-one',
                    'mode-two',
                    'mode-three',
                    'mode-four',
                ],
            ),
        ];
        $subject = new LocalizationModesCollection();
        $subject->set(...$modes);
        $reflectionClass = new \ReflectionClass($subject);
        $internalModesProperty = $reflectionClass->getProperty('modes')->getValue($subject);
        $this->assertIsArray($internalModesProperty);
        $this->assertCount(5, $internalModesProperty);
        $this->assertSame($expectedKeys, array_keys($internalModesProperty));
    }

    #[Test]
    public function jsonEncodeReturnsExpectedString(): void
    {
        $modes = [
            new LocalizationMode(
                identifier: 'mode-one',
                title: 'Mode 1',
                description: 'Mode One localization mode',
                icon: 'icon-one',
                before: [
                    'mode-two',
                ],
                after: [],
            ),
            new LocalizationMode(
                identifier: 'mode-two',
                title: 'Mode 2',
                description: 'Mode Two localization mode',
                icon: 'icon-two',
                before: [],
                after: [
                    'mode-one',
                ],
            ),
        ];
        $expectedJsonString = \json_encode([
            [
                'identifier' => 'mode-one',
                'title' => 'Mode 1',
                'description' => 'Mode One localization mode',
                'icon' => 'icon-one',
                'before' => ['mode-two'],
                'after' => [],
                'restrictedSourceLanguageIds' => null,
            ],
            [
                'identifier' => 'mode-two',
                'title' => 'Mode 2',
                'description' => 'Mode Two localization mode',
                'icon' => 'icon-two',
                'before' => [],
                'after' => ['mode-one'],
                'restrictedSourceLanguageIds' => null,
            ],
        ]);
        $subject = new LocalizationModesCollection();
        $subject->set(...$modes);
        $this->assertCount(2, $subject);
        $this->assertSame($expectedJsonString, \json_encode($subject, JSON_THROW_ON_ERROR));
    }
}
