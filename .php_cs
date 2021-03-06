<?php

return EzSystems\EzPlatformCodeStyle\PhpCsFixer\Config::create()
    ->setFinder(
        PhpCsFixer\Finder::create()
            ->in([__DIR__ . '/src', __DIR__ . '/tests'])
            ->files()->name('*.php')
    )
;
