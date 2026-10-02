<?php declare(strict_types=1);

$finder = (new PhpCsFixer\Finder())
    ->in([__DIR__ . '/src', __DIR__ . '/tests']);

return (new PhpCsFixer\Config())
    ->setRiskyAllowed(true)
    ->setRules([
        '@PER-CS2.0' => true,
        '@PHP84Migration' => true,
        'declare_strict_types' => true,
        'blank_line_after_opening_tag' => false,
        'linebreak_after_opening_tag' => false,
        'no_unused_imports' => true,
        'ordered_imports' => ['imports_order' => ['class', 'function', 'const']],
        'single_quote' => true,
        'binary_operator_spaces' => [
            'default' => 'single_space',
            'operators' => ['=' => 'align_single_space_minimal', '=>' => 'align_single_space_minimal'],
        ],
    ])
    ->setFinder($finder);
