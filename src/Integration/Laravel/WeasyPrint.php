<?php

declare(strict_types=1);

namespace WeasyPrint\Integration\Laravel;

use Illuminate\Support\Facades\Facade;
use WeasyPrint\Contracts\WeasyPrint as Contract;

/**
 * @method static string getWeasyPrintVersion()
 * @method static WeasyPrint setConfig(Objects\Config $config)
 * @method static WeasyPrint tapConfig(callable $callback)
 * @method static WeasyPrint prepareSource(Objects\Source|\Illuminate\Contracts\Support\Renderable|string $source)
 *
 * @see WeasyPrint\Contracts\WeasyPrint
 */
class WeasyPrint extends Facade
{
  public static function getFacadeAccessor(): string
  {
    return Contract::class;
  }
}
