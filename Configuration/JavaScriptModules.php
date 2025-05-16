<?php

$majorVersion = (new \TYPO3\CMS\Core\Information\Typo3Version())->getMajorVersion();
$imports = match($majorVersion) {
    13 => [
        '@typo3/backend/localization/provider-list.js' => sprintf('EXT:deepl_base/Resources/Public/JavaScript/Core%s/localization/provider-list.js', $majorVersion),
        '@typo3/backend/localization.js' => sprintf('EXT:deepl_base/Resources/Public/JavaScript/Core%s/localization.js', $majorVersion),
    ],
    12 => [
        // '@typo3/backend/localization/provider-list.js' => sprintf('EXT:deepl_base/Resources/Public/JavaScript/Core%s/localization/provider-list.js', $majorVersion),
        '@typo3/backend/localization.js' => sprintf('EXT:deepl_base/Resources/Public/JavaScript/Core%s/localization.js', $majorVersion),
    ],
    default => [],
};

return [
    'dependencies' => ['core', 'backend'],
    'tags' => [
        'backend.module',
        'backend.form',
        'backend.navigation-component',
    ],
    'imports' => $imports,
];
