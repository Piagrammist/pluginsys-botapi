<?php declare(strict_types=1);

namespace Piagrammist\PluginSys\BotAPI\Traits;


trait Activatable
{
    protected bool $__active = true;

    final public function activate(): self
    {
        $this->__active = true;
        return $this;
    }

    final public function deactivate(): self
    {
        $this->__active = false;
        return $this;
    }

    final public function isActive(): bool
    {
        return $this->__active;
    }
}
