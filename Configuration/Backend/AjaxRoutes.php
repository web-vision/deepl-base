<?php

use WebVision\Deepl\Base\Core13\Controller\Backend\LocalizationController as WebVisionBaseLocalizationController;

$majorVersion = (new \TYPO3\CMS\Core\Information\Typo3Version())->getMajorVersion();

return match($majorVersion) {
    // @todo typo3/cms-backend >14.0 Remove with TYPO3 v14 minimum version.
    13 => [
        // Localize the records
        // NOTE: Only adjust path for this TYPO3 core route.
        'records_localize' => [
            'path' => '/records/localize/process',
            'target' => WebVisionBaseLocalizationController::class . '::localizeRecords',
        ],
        // Get localization providers
        'records_localize_providers' => [
            'path' => '/records/localize/providers',
            'target' => WebVisionBaseLocalizationController::class . '::getLocalizationModes',
        ],
    ],
    default => [],
};
