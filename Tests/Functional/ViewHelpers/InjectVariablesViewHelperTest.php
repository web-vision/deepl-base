<?php

declare(strict_types=1);

namespace WebVision\Deepl\Base\Tests\Functional\ViewHelpers;

use PHPUnit\Framework\Attributes\Test;
use SBUERK\TYPO3\Testing\TestCase\FunctionalTestCase;
use Symfony\Component\DependencyInjection\Container;
use TYPO3\CMS\Core\EventDispatcher\ListenerProvider;
use TYPO3\CMS\Fluid\Core\Rendering\RenderingContextFactory;
use TYPO3Fluid\Fluid\View\TemplateView;
use WebVision\Deepl\Base\Event\ViewHelpers\ModifyInjectVariablesViewHelperEvent;

final class InjectVariablesViewHelperTest extends FunctionalTestCase
{
    protected bool $initializeDatabase = false;

    protected array $testExtensionsToLoad = [
        'web-vision/deepl-base',
    ];

    #[Test]
    public function globalVariablesCanBeUsedInChildrenWhenNotModifiedByDispatchedEvent(): void
    {
        $context = $this->get(RenderingContextFactory::class)->create();
        $context->getViewHelperResolver()->addNamespace('deeplbase', 'WebVision\\Deepl\\Base\\ViewHelpers');
        $context->getTemplatePaths()->setTemplateSource('<deeplbase:injectVariables identifier="no-event-modifications">CHECK: {templateVariable}</deeplbase:injectVariables>');
        $context->getVariableProvider()->add('templateVariable', 'test1234');
        $expected = 'CHECK: test1234';

        $this->assertSame($expected, (new TemplateView($context))->render());
    }

    #[Test]
    public function modifyInjectVariablesViewHelperEventIsDispatched(): void
    {
        /** @var ModifyInjectVariablesViewHelperEvent[] $dispatchedEvents */
        $dispatchedEvents = [];
        /** @var Container $container */
        $container = $this->get('service_container');
        $container->set(
            'modify-inject-variables-event-is-dispatched',
            static function (ModifyInjectVariablesViewHelperEvent $event) use (
                &$dispatchedEvents
            ) {
                $dispatchedEvents[] = $event;
            }
        );
        $listenerProvider = $container->get(ListenerProvider::class);
        $listenerProvider->addListener(ModifyInjectVariablesViewHelperEvent::class, 'modify-inject-variables-event-is-dispatched');
        $context = $this->get(RenderingContextFactory::class)->create();
        $context->getViewHelperResolver()->addNamespace('deeplbase', 'WebVision\\Deepl\\Base\\ViewHelpers');
        $context->getTemplatePaths()->setTemplateSource('<deeplbase:injectVariables identifier="event-is-dispatched-identifier">some value</deeplbase:injectVariables>');
        $expected = 'some value';

        $this->assertSame($expected, (new TemplateView($context))->render());
        $this->assertCount(1, $dispatchedEvents);
        $firstEvent = $dispatchedEvents[array_key_first($dispatchedEvents)];
        $this->assertSame('event-is-dispatched-identifier', $firstEvent->getIdentifier());
    }

    #[Test]
    public function modifyInjectVariablesViewHelperEventIsDispatchedMultipleTimes(): void
    {
        /** @var ModifyInjectVariablesViewHelperEvent[] $dispatchedEvents */
        $dispatchedEvents = [];
        /** @var Container $container */
        $container = $this->get('service_container');
        $container->set(
            'modify-inject-variables-event-is-dispatched-multiple-times',
            static function (ModifyInjectVariablesViewHelperEvent $event) use (
                &$dispatchedEvents
            ) {
                $dispatchedEvents[] = $event;
            }
        );
        $listenerProvider = $container->get(ListenerProvider::class);
        $listenerProvider->addListener(ModifyInjectVariablesViewHelperEvent::class, 'modify-inject-variables-event-is-dispatched-multiple-times');
        $context = $this->get(RenderingContextFactory::class)->create();
        $context->getViewHelperResolver()->addNamespace('deeplbase', 'WebVision\\Deepl\\Base\\ViewHelpers');
        $context->getTemplatePaths()->setTemplateSource('<deeplbase:injectVariables identifier="event-is-dispatched-identifier">some value</deeplbase:injectVariables> <deeplbase:injectVariables identifier="another-variables-inject-event">other value</deeplbase:injectVariables>');
        $expected = 'some value other value';

        $this->assertSame($expected, (new TemplateView($context))->render());
        $this->assertCount(2, $dispatchedEvents);
        $firstEvent = array_shift($dispatchedEvents);
        $secondEvent = array_shift($dispatchedEvents);
        $this->assertInstanceOf(ModifyInjectVariablesViewHelperEvent::class, $firstEvent);
        $this->assertInstanceOf(ModifyInjectVariablesViewHelperEvent::class, $secondEvent);
        $this->assertSame('event-is-dispatched-identifier', $firstEvent->getIdentifier());
        $this->assertSame('another-variables-inject-event', $secondEvent->getIdentifier());
    }

    #[Test]
    public function modifyInjectVariablesViewHelperEventIsDispatchedAndLocalVariableCanBeSetRestoringToOriginalGlobalAfterwards(): void
    {
        /** @var ModifyInjectVariablesViewHelperEvent[] $dispatchedEvents */
        $dispatchedEvents = [];
        /** @var Container $container */
        $container = $this->get('service_container');
        $container->set(
            'modify-inject-variables-event-is-dispatched',
            static function (ModifyInjectVariablesViewHelperEvent $event) use (
                &$dispatchedEvents
            ) {
                if ($event->getIdentifier() === 'modify-variables') {
                    $event->getLocalVariableProvider()->add('testVariable', 'local-replacement');
                }
                $dispatchedEvents[] = $event;
            }
        );
        $listenerProvider = $container->get(ListenerProvider::class);
        $listenerProvider->addListener(ModifyInjectVariablesViewHelperEvent::class, 'modify-inject-variables-event-is-dispatched');
        $context = $this->get(RenderingContextFactory::class)->create();
        $context->getViewHelperResolver()->addNamespace('deeplbase', 'WebVision\\Deepl\\Base\\ViewHelpers');
        $context->getTemplatePaths()->setTemplateSource('START: {testVariable} <deeplbase:injectVariables identifier="modify-variables">LOCAL: {testVariable}</deeplbase:injectVariables> AFTER: {testVariable}');
        $context->getVariableProvider()->add('testVariable', 'global-start');
        $expected = 'START: global-start LOCAL: local-replacement AFTER: global-start';

        $this->assertSame($expected, (new TemplateView($context))->render());
        $this->assertCount(1, $dispatchedEvents);
        $firstEvent = $dispatchedEvents[array_key_first($dispatchedEvents)];
        $this->assertSame('modify-variables', $firstEvent->getIdentifier());
    }

    #[Test]
    public function modifyInjectVariablesViewHelperEventIsDispatchedAndModifiedGlobalVariablesIsAvailableAfterwards(): void
    {
        /** @var ModifyInjectVariablesViewHelperEvent[] $dispatchedEvents */
        $dispatchedEvents = [];
        /** @var Container $container */
        $container = $this->get('service_container');
        $container->set(
            'modify-inject-variables-event-is-dispatched',
            static function (ModifyInjectVariablesViewHelperEvent $event) use (
                &$dispatchedEvents
            ) {
                if ($event->getIdentifier() === 'modify-variables') {
                    $event->getLocalVariableProvider()->add('testVariable', 'local-replacement');
                    $event->getGlobalVariableProvider()->add('testVariable', 'global-after');
                }
                $dispatchedEvents[] = $event;
            }
        );
        $listenerProvider = $container->get(ListenerProvider::class);
        $listenerProvider->addListener(ModifyInjectVariablesViewHelperEvent::class, 'modify-inject-variables-event-is-dispatched');
        $context = $this->get(RenderingContextFactory::class)->create();
        $context->getViewHelperResolver()->addNamespace('deeplbase', 'WebVision\\Deepl\\Base\\ViewHelpers');
        $context->getTemplatePaths()->setTemplateSource('START: {testVariable} <deeplbase:injectVariables identifier="modify-variables">LOCAL: {testVariable}</deeplbase:injectVariables> AFTER: {testVariable}');
        $context->getVariableProvider()->add('testVariable', 'global-start');
        $expected = 'START: global-start LOCAL: local-replacement AFTER: global-after';

        $this->assertSame($expected, (new TemplateView($context))->render());
        $this->assertCount(1, $dispatchedEvents);
        $firstEvent = $dispatchedEvents[array_key_first($dispatchedEvents)];
        $this->assertSame('modify-variables', $firstEvent->getIdentifier());
    }
}
