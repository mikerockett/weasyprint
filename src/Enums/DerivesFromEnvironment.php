<?php

namespace WeasyPrint\Enums;

trait DerivesFromEnvironment
{
  public static function fromEnvironment(string $key): ?static
  {
    return match ($env = env($key)) {
      null => null,
      default => static::tryFrom($env)
    };
  }
}
