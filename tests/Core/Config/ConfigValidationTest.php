<?php

declare(strict_types=1);

use WeasyPrint\Enums\MediaType;
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

  describe('media type validation', function (): void {
    it('accepts valid media type as string', function (): void {
      $config = new Config(mediaType: 'print');
      expect($config->mediaType)->toBe(MediaType::PRINT);
    });

    it('accepts valid media type as enum', function (): void {
      $config = new Config(mediaType: MediaType::SCREEN);
      expect($config->mediaType)->toBe(MediaType::SCREEN);
    });

    it('accepts null media type', function (): void {
      $config = new Config(mediaType: null);
      expect($config->mediaType)->toBeNull();
    });

    it('rejects invalid media type string', function (): void {
      new Config(mediaType: 'invalid');
    })->throws(InvalidConfigValueException::class);

    it('rejects flag-like media type string', function (): void {
      new Config(mediaType: '--verbose');
    })->throws(InvalidConfigValueException::class);
  });

  describe('cache prefix validation', function (): void {
    it('accepts valid cache prefix', function (): void {
      $config = new Config(cachePrefix: 'my_app_cache');
      expect($config->cachePrefix)->toBe('my_app_cache');
    });

    it('rejects cache prefix with path separators', function (): void {
      new Config(cachePrefix: '../evil');
    })->throws(InvalidConfigValueException::class);

    it('rejects cache prefix with null bytes', function (): void {
      new Config(cachePrefix: "prefix\0suffix");
    })->throws(InvalidConfigValueException::class);
  });
});
