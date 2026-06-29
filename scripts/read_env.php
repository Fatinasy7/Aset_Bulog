<?php
$path = __DIR__ . '/../.env';
if (!file_exists($path)) { echo "MISSING: $path\n"; exit(1); }
echo "FILE_EXISTS\n";
$content = @file_get_contents($path);
if ($content === false) {
	echo "READ_FAILED\n";
	var_dump(error_get_last());
} else {
	echo "READ_OK\n";
	echo 'LENGTH=' . strlen($content) . PHP_EOL;
	echo 'HEAD=' . substr($content,0,400) . PHP_EOL;
}
