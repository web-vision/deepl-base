..  _start:

==========
DeepL Base
==========

:Extension key:
    deepl_base

:Package name:
    web-vision/deepl-base

:Version:
    |release|

:Language:
    en

:Copyright:
    2018

:Author:
    web-vision GmbH & Contributors

:Rendered:
    |today|

:License:
    This document is published under the
    `Open Publication License <https://www.opencontent.org/openpub/>`__.

----

This extension provides basic and shared functionality required by multiple
extensions and includes:

*   :guilabel:`DEPRECATED` Replaces the :guilabel:`PageLayout module localization wizard`
    for TYPO3 v13 to a dynamic and extensible way keeping the look and feel from TYPO3 v13
    native wizard. This includes dispatching several :guilabel:`PSR-14` events to allow
    other extension to register custom translation modes, for example
    `EXT:deepltranslate_core <https://extensions.typo3.org/extension/deepltranslate_core>`_
    or `EXT:deepl_write <https://extensions.typo3.org/extension/deepl_write>`_.

    TYPO3 v14 introduced a new :guilabel:`Localization Handler` allowing extensions
    to extend that in a more proper way and incooperate better with the completely
    revamped localization handling and therefore the implementation is untouched for
    it.

    ..  figure:: /Images/deepl-localization-mode.png
        :alt: Select `Translate with DeepL` localization mode in TYPo3 v13

*   :php:`\WebVision\Deepl\Base\Imaging\IconProvider\DeeplBaseSvgIconProvider` to
    allow providing `SVG Icons` for action not coming from a `SVG Sprite` with
    `icon-color` support (light and dark mode) .

..  attention::

    This extension is provided as shared base for :guilabel:`web-vision GmbH`
    public extension and not considered as public API for other extensions.

    Despite taking this extension internally it tries to follow :guilabel:`semver`
    and common extension `feature & breaking policy` aligned with TYPO3 Core
    policy, but it's not ensured and could still break on `minor version level`.

----

..  toctree::
    :maxdepth: 2
    :titlesonly:
    :hidden:

    Changelog/Index

..  Meta Menu

..  toctree::
    :hidden:

    Sitemap
