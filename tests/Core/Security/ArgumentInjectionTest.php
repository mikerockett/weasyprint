<?php

declare(strict_types=1);

use WeasyPrint\Commands\BuildCommand;
use WeasyPrint\Exceptions\InvalidConfigValueException;
use WeasyPrint\Objects\Config;

describe('argument injection resistance', function (): void {
  function buildArguments(array $configOverrides = []): array
  {
    $config = new Config(...$configOverrides);
    $command = new BuildCommand(
      config: $config,
      inputPath: '/tmp/test_input.html',
      outputPath: '/tmp/test_output.pdf',
    );

    $reflection = new ReflectionProperty($command, 'arguments');
    return $reflection->getValue($command)->toArray();
  }

  it('places -- separator before positional arguments', function (): void {
    $args = buildArguments();

    $separatorIndex = array_search('--', $args, true);
    assert($separatorIndex !== false);

    $afterSeparator = array_slice($args, $separatorIndex + 1);
    expect($afterSeparator)->toBe(['/tmp/test_input.html', '/tmp/test_output.pdf']);
  });

  it('places all flags before -- separator', function (): void {
    $args = buildArguments([
      'presentationalHints' => true,
      'dpi' => 300,
      'optimizeImages' => true,
    ]);

    $separatorIndex = array_search('--', $args, true);
    assert($separatorIndex !== false);

    for ($i = 1; $i < $separatorIndex; $i++) {
      if (str_starts_with((string) $args[$i], '--')) {
        expect($args[$i])->toStartWith('--');
      }
    }
  });

  it('rejects mediaType values that are not in the enum', function (): void {
    new Config(mediaType: '--verbose');
  })->throws(InvalidConfigValueException::class);

  it('rejects cachePrefix with path separators', function (): void {
    new Config(cachePrefix: '../etc/passwd');
  })->throws(InvalidConfigValueException::class);

  it('rejects cachePrefix with null bytes', function (): void {
    new Config(cachePrefix: "prefix\0suffix");
  })->throws(InvalidConfigValueException::class);

  it('does not allow flag-like strings in inputEncoding', function (): void {
    new Config(inputEncoding: '--help');
  })->throws(InvalidConfigValueException::class);

  it('handles special characters in base URL safely', function (): void {
    $args = buildArguments(['baseUrl' => 'https://example.com/path?q=1&b=2']);

    expect($args)->toContain('--base-url');
    expect($args)->toContain('https://example.com/path?q=1&b=2');

    $separatorIndex = array_search('--', $args, true);
    $baseUrlIndex = array_search('https://example.com/path?q=1&b=2', $args, true);
    assert($separatorIndex !== false && $baseUrlIndex !== false);
    expect($baseUrlIndex)->toBeLessThan($separatorIndex);
  });

  it('treats integer values as separate argv entries', function (): void {
    $args = buildArguments(['dpi' => 300, 'jpegQuality' => 85]);

    $dpiIndex = array_search('--dpi', $args, true);
    assert($dpiIndex !== false);
    expect($args[$dpiIndex + 1])->toBe(300);

    $jpegIndex = array_search('--jpeg-quality', $args, true);
    assert($jpegIndex !== false);
    expect($args[$jpegIndex + 1])->toBe(85);
  });

  it('does not concatenate flag name and value', function (): void {
    $args = buildArguments(['inputEncoding' => 'utf-8']);

    expect($args)->not->toContain('--encoding=utf-8');
    expect($args)->toContain('--encoding');
    expect($args)->toContain('utf-8');
  });
});

describe('fuzz: config values with shell metacharacters', function (): void {
  $maliciousValues = [
    'spaces' => 'value with spaces',
    'tabs' => "value\twith\ttabs",
    'double-quotes' => 'value"with"quotes',
    'single-quotes' => "value'with'quotes",
    'semicolon' => 'value;rm -rf /',
    'dollar-subshell' => 'value$(whoami)',
    'backticks' => 'value`id`',
    'pipe' => 'value|cat /etc/passwd',
    'ampersand' => 'value&& echo pwned',
    'redirect' => 'value > /tmp/pwned',
    'newlines' => "value\nwith\nnewlines",
    'backslash' => 'value\\backslash',
    'percent-null' => 'value%00null',
  ];

  foreach ($maliciousValues as $label => $value) {
    it("passes shell metacharacters through as literal argv entries ({$label})", function () use ($value): void {
      $args = buildArguments(['baseUrl' => $value]);

      expect($args)->toContain($value);
      expect(count(array_keys($args, $value, true)))->toBe(1);
    });
  }
});

describe('fuzz: enum-validated fields reject arbitrary strings', function (): void {
  $injectionAttempts = [
    'flag-verbose' => '--verbose',
    'flag-output' => '--output=/tmp/evil',
    'flag-rf' => '-rf',
    'semicolon-command' => '; rm -rf /',
    'dollar-subshell' => '$(whoami)',
    'value-with-semicolon' => 'print; echo pwned',
    'value-with-newline' => "print\n--verbose",
  ];

  foreach ($injectionAttempts as $label => $attempt) {
    it("rejects mediaType injection attempt ({$label})", function () use ($attempt): void {
      expect(fn() => new Config(mediaType: $attempt))
        ->toThrow(InvalidConfigValueException::class);
    });
  }
});
