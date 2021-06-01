<?php

use EzSystems\EzPlatformCodeStyle\PhpCsFixer\EzPlatformInternalConfigFactory;

return EzPlatformInternalConfigFactory::build()
    ->setFinder(
        PhpCsFixer\Finder::create()
            ->in([__DIR__ . '/src', __DIR__ . '/tests'])
            ->files()->name('*.php')
    )
;
