<?php

$directory = __DIR__;

$iterator = new RecursiveIteratorIterator(
    new RecursiveDirectoryIterator($directory)
);

foreach ($iterator as $file) {

    if ($file->isDir()) {
        continue;
    }

    $path = $file->getPathname();

    $content = @file_get_contents($path);

    if ($content !== false && substr($content, 0, 3) === "\xEF\xBB\xBF") {
        echo $path . PHP_EOL;
    }
}