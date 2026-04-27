..  _feature-addeddeeplbasesvgiconprovider-1777259422:

=========================================
Feature: Added `DeeplBaseSvgIconProvider`
=========================================

Description
===========

The custom :php:`\WebVision\Deepl\Base\Imaging\IconProvider\DeeplBaseSvgIconProvider`
is added to allow registration and rendering of color mode aware `SVG` icons,
for example as action icons on buttons.

The :php:`\TYPO3\CMS\Core\Imaging\IconProvider\SvgIconProvider` lacks this
ability, because it renders `<img />` tags for the inline markup usage and
:php:`\TYPO3\CMS\Core\Imaging\IconProvider\SvgSpriteIconProvider` is not
usable in all cases.

Example registration
--------------------

..  code-block:: php
    :caption: EXT:my_ext/Configuration/Icons.php

    <?php

    declare(strict_types=1);

    use WebVision\Deepl\Base\Imaging\IconProvider\DeeplBaseSvgIconProvider;

    return [
        'myext-color-mode-aware-icon' => [
            'provider' => DeeplBaseSvgIconProvider::class,
            'source' => 'EXT:my_ext/Resources/Public/Icons/color-mode-aware-icon.svg',
        ],
    ];

Impact
======

Allows the registration of color mode aware SVG icons.

