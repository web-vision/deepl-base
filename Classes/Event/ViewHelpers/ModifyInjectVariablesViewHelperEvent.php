<?php

declare(strict_types=1);

namespace WebVision\Deepl\Base\Event\ViewHelpers;

use TYPO3Fluid\Fluid\Core\Variables\VariableProviderInterface;
use WebVision\Deepl\Base\ViewHelpers\InjectVariablesViewHelper;

/**
 * {@see InjectVariablesViewHelper} fires this event allow assigning additional variables
 * to either the {@see self::$localVariableProvider} local (children) context or in global
 * context of the currently used template {@see InjectVariablesViewHelper}.
 */
final class ModifyInjectVariablesViewHelperEvent
{
    public function __construct(
        private readonly string $identifier,
        private readonly VariableProviderInterface $globalVariableProvider,
        private readonly VariableProviderInterface $localVariableProvider,
    ) {
    }

    public function getIdentifier(): string
    {
        return $this->identifier;
    }

    /**
     * Holds global variables used within the current fluid template, and which will be available
     * after the {@see InjectVariablesViewHelper} ViewHelper call in a given template.
     *
     * Can be used to enrich a template in a more generic way.
     */
    public function getGlobalVariableProvider(): VariableProviderInterface
    {
        return $this->globalVariableProvider;
    }

    /**
     * Holds variable only usable within the children block of the {@see InjectVariablesViewHelper}.
     *
     * Can be used to set only variables for the children context of given identifier.
     */
    public function getLocalVariableProvider(): VariableProviderInterface
    {
        return $this->localVariableProvider;
    }
}
