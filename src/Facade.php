<?php

declare(strict_types=1);

namespace WeasyPrint;

use Illuminate\Support\Facades\Facade as SupportFacade;
use WeasyPrint\Contracts\Factory;
use WeasyPrint\Objects\Config;

/**
 * @method static Factory mergeConfig(mixed ...$config)
 * @method static Factory prepareSource(Source|Renderable|string $source)
 * @method static Config getConfig()
 */
class Facade extends SupportFacade
{
  public static function getFacadeAccessor(): string
  {
    return Factory::class;
  }
}
