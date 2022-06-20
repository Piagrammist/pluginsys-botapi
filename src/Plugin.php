<?php declare(strict_types=1);

namespace Piagrammist\PluginSys\BotAPI;

use Opis\Closure\SerializableClosure;


class Plugin implements \Stringable
{
    use Traits\Nameable;
    use Traits\Activatable;

    public readonly SerializableClosure $callback;

    public function __construct(string $name, callable $callback)
    {
        if ($name === '') {
            throw new \Exception("Plugin name could not be empty");
        }

        $this->name     = \strtolower($name);
        $this->callback = new SerializableClosure(
            $callback instanceof \Closure
                ? $callback
                : \Closure::fromCallable($callback)
        );
    }

    public function __invoke(mixed ...$args): mixed
    {
        return ($this->callback)(...$args);
    }
}
