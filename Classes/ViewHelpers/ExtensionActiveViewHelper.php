<?php

declare(strict_types=1);

namespace WebVision\Deepl\Base\ViewHelpers;

use TYPO3\CMS\Core\Utility\ExtensionManagementUtility;
use TYPO3Fluid\Fluid\Core\Rendering\RenderingContextInterface;
use TYPO3Fluid\Fluid\Core\ViewHelper\AbstractConditionViewHelper;

/**
 * condition ViewHelper for getting information about installed packages
 * Usage:
 * <deeplbase:extensionActive extension="enable_translated_content">
 *     <f:then>
 *         <!-- do stuff -->
 *     </f:then>
 *     <f:else>
 *         <!-- do other stuff -->
 *     </f:else>
 * </deeplbase:extensionActive>
 *
 * Inline example:
 * {deepl:be.extensionActive(extension: 'enable_translated_content', then: '', else: '')}
 */
final class ExtensionActiveViewHelper extends AbstractConditionViewHelper
{
    public function initializeArguments(): void
    {
        parent::initializeArguments();
        $this->registerArgument('extension', 'string', 'The extension to check', true);
    }

    public static function verdict(array $arguments, RenderingContextInterface $renderingContext): bool
    {
        if (ExtensionManagementUtility::isLoaded((string)($arguments['extension'] ?? ''))) {
            return true;
        }
        return false;
    }
}
