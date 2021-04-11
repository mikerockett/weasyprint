<?php

declare(strict_types=1);

namespace WeasyPrint\Enums;

/**
 * @method static static none()
 * @method static static pdf()
 * @method static static png()
 */
class OutputType extends Enum
{
  protected const none = 'none';
  protected const pdf = 'pdf';
  protected const png = 'png';
}
