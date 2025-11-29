<?php

$finder = PhpCsFixer\Finder::create()
  ->in(__DIR__)
  ->exclude('vendor')
  ->exclude('node_modules')
  ->exclude('storage')
  ->exclude('bootstrap/cache');

$config = new PhpCsFixer\Config()->setParallelConfig(
  PhpCsFixer\Runner\Parallel\ParallelConfigFactory::detect()
);

return $config
  ->setRules([
    '@PER-CS2x0' => true,
    'declare_strict_types' => true,
    'no_unused_imports' => true,
    'no_useless_else' => true,
    'no_useless_return' => true,
    'strict_comparison' => true,
    'strict_param' => true,
  ])
  ->setIndent("  ")
  ->setLineEnding("\n")
  ->setFinder($finder);
