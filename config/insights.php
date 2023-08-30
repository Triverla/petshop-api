<?php

declare(strict_types=1);

use NunoMaduro\PhpInsights\Domain\Insights\ForbiddenDefineFunctions;
use NunoMaduro\PhpInsights\Domain\Insights\ForbiddenFinalClasses;
use NunoMaduro\PhpInsights\Domain\Insights\ForbiddenNormalClasses;
use NunoMaduro\PhpInsights\Domain\Insights\ForbiddenPrivateMethods;
use NunoMaduro\PhpInsights\Domain\Insights\ForbiddenTraits;
use NunoMaduro\PhpInsights\Domain\Metrics\Architecture\Classes;
use SlevomatCodingStandard\Sniffs\Commenting\UselessFunctionDocCommentSniff;
use SlevomatCodingStandard\Sniffs\Namespaces\AlphabeticallySortedUsesSniff;
use SlevomatCodingStandard\Sniffs\TypeHints\DeclareStrictTypesSniff;
use SlevomatCodingStandard\Sniffs\TypeHints\DisallowMixedTypeHintSniff;
use SlevomatCodingStandard\Sniffs\TypeHints\ParameterTypeHintSniff;
use SlevomatCodingStandard\Sniffs\TypeHints\PropertyTypeHintSniff;
use SlevomatCodingStandard\Sniffs\TypeHints\ReturnTypeHintSniff;

return [

    /*
    |--------------------------------------------------------------------------
    | Default Preset
    |--------------------------------------------------------------------------
    |
    | This option controls the default preset that will be used by PHP Insights
    | to make your code reliable, simple, and clean. However, you can always
    | adjust the `Metrics` and `Insights` below in this configuration file.
    |
    | Supported: "default", "laravel", "symfony", "magento2", "drupal"
    |
    */

    'preset' => 'laravel',

    'ide' => 'phpstorm',

    'exclude' => [
        'app/Providers'
    ],

    'add' => [],

    'remove' => [
        // Code
        \SlevomatCodingStandard\Sniffs\TypeHints\DeclareStrictTypesSniff::class,          // Declare strict types
        \SlevomatCodingStandard\Sniffs\Classes\ForbiddenPublicPropertySniff::class,       // Forbidden public property
        \PhpCsFixer\Fixer\ClassNotation\VisibilityRequiredFixer::class,                   // Visibility required
        \SlevomatCodingStandard\Sniffs\Classes\ClassConstantVisibilitySniff::class,       // Class constant visibility
        \SlevomatCodingStandard\Sniffs\ControlStructures\DisallowEmptySniff::class,       // Disallow empty
        \PhpCsFixer\Fixer\Comment\NoEmptyCommentFixer::class,                             // No empty comment
        \SlevomatCodingStandard\Sniffs\Commenting\UselessFunctionDocCommentSniff::class,  // Useless function doc comment

        // Architecture
        \NunoMaduro\PhpInsights\Domain\Insights\ForbiddenNormalClasses::class,            //Normal classes are forbidden

        // Style
        \PHP_CodeSniffer\Standards\Generic\Sniffs\Formatting\SpaceAfterCastSniff::class,  // Space after cast
        \PHP_CodeSniffer\Standards\Generic\Sniffs\Formatting\SpaceAfterNotSniff::class,   // Space after not
        \SlevomatCodingStandard\Sniffs\Namespaces\AlphabeticallySortedUsesSniff::class,   // Alphabetically sorted uses
        \SlevomatCodingStandard\Sniffs\Commenting\DocCommentSpacingSniff::class,          // Doc comment spacing
        \PhpCsFixer\Fixer\ClassNotation\OrderedClassElementsFixer::class,                 // Ordered class elements
        \PhpCsFixer\Fixer\StringNotation\SingleQuoteFixer::class,                         // Single quote
    ],

    'config' => [
        \NunoMaduro\PhpInsights\Domain\Insights\CyclomaticComplexityIsHigh::class => [
            'maxComplexity' => 8,
        ],
        \PHP_CodeSniffer\Standards\Generic\Sniffs\Files\LineLengthSniff::class => [
            'lineLimit' => 120,
            'absoluteLineLimit' => 120,
            'ignoreComments' => false,
        ],
        \PhpCsFixer\Fixer\Import\OrderedImportsFixer::class => [
            'imports_order' => ['class', 'const', 'function'],
            'sort_algorithm' => 'length',
        ]
    ],

    'requirements' => [
        'min-quality' => 75,
        'min-complexity' => 75,
        'min-architecture' => 75,
        'min-style' => 75,
        'disable-security-check' => false,
    ],
];
