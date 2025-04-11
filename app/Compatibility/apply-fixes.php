<?php

// This script is run in CI for PHP 8.3 to apply compatibility fixes

require_once __DIR__ . '/../../vendor/autoload.php';

use App\Compatibility\PHP83Fixes;

// Apply PHP 8.3 compatibility fixes
PHP83Fixes::apply();

echo "Applied PHP 8.3 compatibility fixes.\n";
