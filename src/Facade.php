<?php

declare(strict_types=1);

namespace WeasyPrint;

use Illuminate\Support\Facades\Facade as SupportFacade;
use WeasyPrint\Contracts\Factory;
use WeasyPrint\Objects\Config;

/**
 * @method self withConfiguration(mixed ...$configurationOptions)
 * @method self prepareSource(Source|Renderable|string $source)
 * @method Config getConfig()
 * @method Factory to(OutputType $outputType)
 */
class Facade extends SupportFacade
{
  public static function getFacadeAccessor(): string
  {
    return Factory::class;
  }
}
