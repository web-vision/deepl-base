<?php

declare(strict_types=1);

namespace WebVision\Deepl\Base\ViewHelpers;

use Psr\EventDispatcher\EventDispatcherInterface;
use TYPO3Fluid\Fluid\Core\Variables\ScopedVariableProvider;
use TYPO3Fluid\Fluid\Core\Variables\StandardVariableProvider;
use TYPO3Fluid\Fluid\Core\ViewHelper\AbstractViewHelper;
use WebVision\Deepl\Base\Event\ViewHelpers\ModifyInjectVariablesViewHelperEvent;

/**
 * Allows to declare variables in global or local context variables based on a specified `identifier`
 * using PSR-14 EventListener on event {@see ModifyInjectVariablesViewHelperEvent}.
 */
final class InjectVariablesViewHelper extends AbstractViewHelper
{
    public function __construct(
        private EventDispatcherInterface $eventDispatcher,
    ) {
    }

    public function initializeArguments(): void
    {
        $this->registerArgument('identifier', 'string', 'Unique identifier for the place', true);
    }

    public function render(): string
    {
        $globalVariableProvider = $this->renderingContext->getVariableProvider();
        $localVariableProvider = new StandardVariableProvider();
        /** @phpstan-ignore-next-line  */
        $identifier = (string)($this->arguments['identifier'] ?? '');
        if ($identifier === '') {
            throw new \InvalidArgumentException(
                'InjectVariableViewHelper argument "identifier" must be a non-empty string.',
                1748872475,
            );
        }
        $event = $this->eventDispatcher->dispatch(new ModifyInjectVariablesViewHelperEvent(
            identifier: $identifier,
            globalVariableProvider: $globalVariableProvider,
            localVariableProvider: $localVariableProvider,
        ));
        $globalVariableProvider = $event->getGlobalVariableProvider();
        $localVariableProvider = $event->getLocalVariableProvider();
        $scopedVariableProvider = new ScopedVariableProvider($globalVariableProvider, $localVariableProvider);
        // Render children with combined global and local variable context
        $this->renderingContext->setVariableProvider($scopedVariableProvider);
        /** @phpstan-ignore-next-line  */
        $value = (string)$this->renderChildren();
        // Restore enriched global variables
        $this->renderingContext->setVariableProvider($globalVariableProvider);
        // Return rendered children.
        return $value;
    }
}
