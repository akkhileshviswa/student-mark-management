<?php

$finder = PhpCsFixer\Finder::create()
    ->in(__DIR__)
    ->name('*.php')
    ->exclude('vendor');

    return (new PhpCsFixer\Config())
    ->setRules([
        '@PSR12' => true, // Apply PSR-12 coding standard
        'array_syntax' => ['syntax' => 'short'], // Short array syntax rule
        // Add other rules as needed
    ])
    ->setFinder($finder);
