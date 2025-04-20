<?php

// This script is run in CI for PHP 8.3 to apply compatibility fixes

require_once __DIR__ . '/../../vendor/autoload.php';

use App\Compatibility\PHP83Fixes;
use App\Compatibility\PHP84Fixes;

// Apply PHP 8.3 compatibility fixes
PHP83Fixes::apply();

// Apply PHP 8.4 compatibility fixes
PHP84Fixes::apply();

echo "Applied PHP 8.3 and PHP 8.4 compatibility fixes.\n";
