<?php

declare(strict_types=1);

use WeasyPrint\Exceptions\InvalidConfigValueException;
use WeasyPrint\Objects\Config;

describe('config validation', function (): void {
  describe('dpi validation', function (): void {
    it('accepts valid dpi', function (): void {
      $config = new Config(dpi: 300);
      expect($config->dpi)->toBe(300);
    });

    it('accepts zero dpi', function (): void {
      $config = new Config(dpi: 0);
      expect($config->dpi)->toBe(0);
    });

    it('rejects negative dpi', function (): void {
      new Config(dpi: -10);
    })->throws(InvalidConfigValueException::class);
  });

  describe('jpeg quality validation', function (): void {
    it('accepts valid jpeg quality', function (): void {
      $config = new Config(jpegQuality: 50);
      expect($config->jpegQuality)->toBe(50);
    });

    it('accepts minimum jpeg quality', function (): void {
      $config = new Config(jpegQuality: 0);
      expect($config->jpegQuality)->toBe(0);
    });

    it('accepts maximum jpeg quality', function (): void {
      $config = new Config(jpegQuality: 95);
      expect($config->jpegQuality)->toBe(95);
    });

    it('rejects too high jpeg quality', function (): void {
      new Config(jpegQuality: 99);
    })->throws(InvalidConfigValueException::class);

    it('rejects negative jpeg quality', function (): void {
      new Config(jpegQuality: -1);
    })->throws(InvalidConfigValueException::class);
  });

  describe('input encoding validation', function (): void {
    it('accepts valid encoding', function (): void {
      $config = new Config(inputEncoding: 'utf-16');
      expect($config->inputEncoding)->toBe('utf-16');
    });

    it('accepts default encoding', function (): void {
      $config = new Config(inputEncoding: 'utf-8');
      expect($config->inputEncoding)->toBe('utf-8');
    });

    it('rejects invalid encoding', function (): void {
      new Config(inputEncoding: 'non-existent');
    })->throws(InvalidConfigValueException::class);
  });
});
