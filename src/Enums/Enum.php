<?php

declare(strict_types=1);

namespace WeasyPrint\Enums;

use BadMethodCallException;
use ReflectionClass;
use UnexpectedValueException;

abstract class Enum
{
  protected mixed $value;
  private string $key;

  protected static array $cache = [];
  protected static array $instances = [];

  public function __construct(mixed $value)
  {
    $this->value = $value instanceof static
      ? $value->getValue()
      : $value;

    $this->key = static::keyForValue($value);
  }

  public static function from(mixed $value): static
  {
    return self::__callStatic(static::keyForValue($value), []);
  }

  public function getValue(): mixed
  {
    return $this->value;
  }

  public function getKey(): string
  {
    return $this->key;
  }

  private static function keyForValue($value): string
  {
    if (($key = static::search($value)) === false) {
      throw new UnexpectedValueException(
        "Value '$value' is not part of the enum " . static::class
      );
    }

    return $key;
  }

  public static function search($value)
  {
    return array_search($value, static::toArray(), true);
  }

  public static function toArray()
  {
    $class = static::class;

    if (!isset(static::$cache[$class])) {
      static::$cache[$class] = (new ReflectionClass($class))->getConstants();
    }

    return static::$cache[$class];
  }

  public static function __callStatic($name, $arguments)
  {
    $class = static::class;

    if (!isset(static::$instances[$class][$name])) {
      $array = static::toArray();

      if (!isset($array[$name]) && !array_key_exists($name, $array)) {
        throw new BadMethodCallException(
          "No static method or enum constant '$name' in class " . static::class
        );
      }

      return static::$instances[$class][$name] = new static($array[$name]);
    }

    return clone static::$instances[$class][$name];
  }
}
