<?php
namespace WeasyPrint\Exceptions;

use Exception;

class InvalidOutputModeException extends Exception
{
  public function __construct()
  {
    parent::__construct('The output mode must be set to png or pdf.');
  }
}
