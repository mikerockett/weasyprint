<?php

namespace WeasyPrint\Commands;

interface Command
{
  public function maybePushArgument(string $key, $value): void;

  public function execute(): string;
}
