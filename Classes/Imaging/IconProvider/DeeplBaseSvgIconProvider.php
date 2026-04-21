<?php

declare(strict_types=1);

namespace WebVision\Deepl\Base\Imaging\IconProvider;

use TYPO3\CMS\Core\Imaging\Icon;
use TYPO3\CMS\Core\Imaging\IconProvider\AbstractSvgIconProvider;

/**
 * Provides a custom svg icon provider rendering svg use markups for svg's,
 * which allows support for light/dark mode action svg usage in the backend.
 */
final class DeeplBaseSvgIconProvider extends AbstractSvgIconProvider
{
    /**
     * @param array{source?: string} $options
     * @throws \InvalidArgumentException
     */
    protected function generateMarkup(Icon $icon, array $options): string
    {
        if (empty($options['source'])) {
            throw new \InvalidArgumentException('[' . $icon->getIdentifier() . '] The option "source" is required and must not be empty', 1460976566);
        }

        $source = $options['source'];
        return $this->generateSvgUseMarkup($source);
    }

    /**
     * @param array{source?: string} $options
     * @throws \InvalidArgumentException
     */
    protected function generateInlineMarkup(array $options): string
    {
        if (empty($options['source'])) {
            throw new \InvalidArgumentException('The option "source" is required and must not be empty', 1460976610);
        }

        $source = $options['source'];
        return $this->generateSvgUseMarkup($source);
    }

    private function generateSvgUseMarkup(string $source): string
    {
        return '<svg class="icon-color"><use xlink:href="' . htmlspecialchars($this->getPublicPath($source)) . '" /></svg>';
    }
}
