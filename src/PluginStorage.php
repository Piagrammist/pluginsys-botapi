<?php declare(strict_types=1);

namespace Piagrammist\PluginSys\BotAPI;


class PluginStorage implements \Countable, \Stringable
{
    use Traits\Nameable;
    use Traits\Activatable;

    protected \SplObjectStorage $container;

    public function __construct(string $name, Plugin ...$plugins)
    {
        if ($name === '') {
            throw new \Exception("Storage name could not be empty");
        }

        $this->name      = \strtolower($name);
        $this->container = new \SplObjectStorage;

        foreach ($plugins as $plugin) {
            $this->container->attach($plugin);
        }
    }

    // Storage
    public function contains(Plugin $plugin): bool
    {
        return $this->container->contains($plugin);
    }
    public function attach(Plugin $plugin): self
    {
        if (!$this->contains($plugin)) {
            $this->container->attach($plugin);
        } else {
            #LOG: Plugin already exists!
        }
        return $this;
    }
    public function detach(Plugin $plugin): self
    {
        if ($this->contains($plugin)) {
            $this->container->detach($plugin);
        } else {
            #LOG: Plugin doesn't exist!
        }
        return $this;
    }

    // Countable
    public function count(): int
    {
        return $this->container->count();
    }

    // Iterator
    public function iter(): \Generator
    {
        foreach ($this->container as $plugin) {
            yield $plugin;
        }
    }

    public function getAll(): array
    {
        return \iterator_to_array($this->container);
    }
}
