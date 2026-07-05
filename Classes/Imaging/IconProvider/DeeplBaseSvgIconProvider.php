<?php

declare(strict_types=1);

namespace WebVision\Deepl\Base\Imaging\IconProvider;

use TYPO3\CMS\Core\Core\Environment;
use TYPO3\CMS\Core\Imaging\Icon;
use TYPO3\CMS\Core\Imaging\IconProvider\AbstractSvgIconProvider;
use TYPO3\CMS\Core\Information\Typo3Version;

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
        return match ((new Typo3Version())->getMajorVersion()) {
            13 => $this->getCore13InlineSvg($source),
            default => $this->getInlineSvg($source),
        };
    }

    private function generateSvgUseMarkup(string $source): string
    {
        return '<svg class="icon-color"><use xlink:href="' . htmlspecialchars($this->getPublicPath($source)) . '" /></svg>';
    }

    private function getCore13InlineSvg(string $source): string
    {
        $source = $this->getPublicPath($source);
        $source = rtrim(Environment::getPublicPath(), '/') . '/' . ltrim($source, '/');
        return $this->getInlineSvg($source);
    }
}
