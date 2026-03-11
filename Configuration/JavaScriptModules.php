<?php

$majorVersion = (new \TYPO3\CMS\Core\Information\Typo3Version())->getMajorVersion();
return match($majorVersion) {
    13 => [
        'dependencies' => ['core', 'backend'],
        'tags' => [
            'backend.module',
            'backend.form',
            'backend.navigation-component',
        ],
        'imports' => [
            '@typo3/backend/localization/provider-list.js' => sprintf('EXT:deepl_base/Resources/Public/JavaScript/Core%s/localization/provider-list.js', $majorVersion),
            '@typo3/backend/localization.js' => sprintf('EXT:deepl_base/Resources/Public/JavaScript/Core%s/localization.js', $majorVersion),
        ],
    ],
    default => [],
};
