<?php

use TYPO3\CMS\Core\Information\Typo3Version;

defined('TYPO3') or die();

(static function (): void {
    $typo3version = new Typo3Version();

    // TYPO3 v13 changed the markup and UX of the PageLayout translation modal which breaks with longer icon labels.
    // This has been reported to the core and a bugfix is in the making, but we ship a workaround for now but load
    // the custom backend css only.
    //
    // Using custom backend css loading introduced with: https://docs.typo3.org/c/typo3/cms-core/main/en-us/Changelog/12.3/Feature-100232-LoadAdditionalStylesheetsInTYPO3Backend.html
    //
    // @todo Remove this with TYPO3 v13.4.10 release as minimum version: https://review.typo3.org/c/Packages/TYPO3.CMS/+/87576
    //
    if ($typo3version->getMajorVersion() === 13
        && version_compare($typo3version->getVersion(), '13.4.10', '<')
    ) {
        $GLOBALS['TYPO3_CONF_VARS']['BE']['stylesheets']['web-vision/deepl-base']
            = 'EXT:deepl_base/Resources/Public/Css/patch-105853.css';
    }
})();
