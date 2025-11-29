<?php

declare(strict_types=1);

namespace WeasyPrint\Commands;

interface Command
{
  public function maybePushArgument(string $key, $value): void;

  public function execute(): string;
}
