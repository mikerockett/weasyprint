<?php

return [
  'binary' => env(
    'WEASYPRINT_BINARY',
    getenv('WEASYPRINT_BINARY') ?: '/usr/local/bin/weasyprint'
  ),

  'cache_prefix' => 'weasyprint-cache_',
  'timeout' => 3600,
];
