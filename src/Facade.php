<?php

declare(strict_types=1);

namespace WeasyPrint;

use Illuminate\Support\Facades\Facade as SupportFacade;
use WeasyPrint\Contracts\Factory;

/**
 * @method static string getWeasyPrintVersion()
 * @method static Factory setConfig(Objects\Config $config)
 * @method static Factory tapConfig(callable $callback)
 * @method static Factory prepareSource(Objects\Source|\Illuminate\Contracts\Support\Renderable|string $source)
 */
class Facade extends SupportFacade
{
  public static function getFacadeAccessor(): string
  {
    return Factory::class;
  }
}
