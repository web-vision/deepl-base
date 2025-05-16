<?php

use WebVision\Deepl\Base\Controller\Backend\LocalizationController as WebVisionBaseLocalizationController;

return [
    // Localize the records
    // NOTE: Only adjust path for this TYPO3 core route.
    // @todo Recheck when moving towards v14 support.
    'records_localize' => [
        'path' => '/records/localize/process',
        'target' => WebVisionBaseLocalizationController::class . '::localizeRecords',
    ],

    // Get localization providers
    // @todo New ajax route as it may be added to TYPO3 v14 in a similar way. Recheck when moving towards v14 support.
    'records_localize_providers' => [
        'path' => '/records/localize/providers',
        'target' => WebVisionBaseLocalizationController::class . '::getLocalizationModes',
    ],
];
