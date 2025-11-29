<?php

declare(strict_types=1);

namespace WeasyPrint\Integration\Laravel;

use Illuminate\Support\Facades\Facade;
use WeasyPrint\Contracts\WeasyPrintFactory;

/**
 * @method static string getWeasyPrintVersion()
 * @method static WeasyPrintFactory setConfig(Objects\Config $config)
 * @method static WeasyPrintFactory tapConfig(callable $callback)
 * @method static WeasyPrintFactory prepareSource(Objects\Source|\Illuminate\Contracts\Support\Renderable|string $source)
 *
 * @see WeasyPrint\Contracts\WeasyPrintFactory
 */
class WeasyPrint extends Facade
{
  public static function getFacadeAccessor(): string
  {
    return WeasyPrintFactory::class;
  }
}
