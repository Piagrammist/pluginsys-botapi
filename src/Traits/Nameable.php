<?php declare(strict_types=1);

namespace Piagrammist\PluginSys\BotAPI\Traits;


trait Nameable
{
    public readonly string $name;

    public function __toString(): string
    {
        return $this->name;
    }
}
