<?php

$header = <<<EOF
This file is part of the YurinskiyValidators package.

(c) Yuriy Yurinskiy <yuriyyurinskiy@yandex.ru>

This source file is subject to the MIT license that is bundled
with this source code in the file LICENSE.
EOF;

/** @var \Symfony\Component\Finder\Finder $finder */
$finder = PhpCsFixer\Finder::create();
$finder
    ->in([__DIR__.'/src', __DIR__.'/tests'])
;

$config = new PhpCsFixer\Config();
$config
    ->setRiskyAllowed(true)
    ->setRules([
        '@Symfony' => true,
        '@Symfony:risky' => true,
        'fopen_flags' => false,
        'protected_to_private' => false,
    ])
    ->setFinder($finder);

return $config;
