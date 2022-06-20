<?php declare(strict_types=1);

namespace Piagrammist\PluginSys\BotAPI;


function readDirectoryClosures(array|string $dir): \Generator {
    if (\is_array($dir)) {
        $dir = path(...$dir);
    }
    if (!\is_dir($dir)) {
        throw new \RuntimeException('Directory does not exists');
    }
    foreach (\glob(path($dir, '*.php'), \GLOB_NOSORT) as $file) {
        $content = require $file;
        if ($content instanceof \Closure) {
            yield \basename($file, '.php') => $content;
        }
    }
}

function path(string ...$args): string {
    return \implode(\DIRECTORY_SEPARATOR, $args);
}
