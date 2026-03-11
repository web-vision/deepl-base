<?php

use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Loader\Configurator\ContainerConfigurator;
use TYPO3\CMS\Core\Information\Typo3Version;

return static function (
    ContainerConfigurator $configurator,
    ContainerBuilder $builder,
) {
    $typo3Version = new Typo3Version();
    $majorVersion = $typo3Version->getMajorVersion();

    //==================================================================================================================
    // We retrieve the services class.
    //==================================================================================================================
    $services = $configurator->services();
    //==================================================================================================================

    //==================================================================================================================
    // The default configuration: allow autowire and autoconfigure,
    // no need to make every class public.
    //==================================================================================================================
    $services->defaults()
        ->autowire()
        ->autoconfigure()
        ->private(); // "private" is the default and can safely be omitted
    //==================================================================================================================

    if ($typo3Version->getMajorVersion() === 13) {
        //==================================================================================================================
        // Define the location of the PHP sources of our extension.
        // In addition, exclude Extbase models that should never be used via DI.
        //==================================================================================================================
        $services->load('WebVision\\Deepl\\Base\\Core13\\', __DIR__ . '/../Core13/Classes/*');
        //==================================================================================================================
    }
};
