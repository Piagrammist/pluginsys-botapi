<?php declare(strict_types=1);

namespace Piagrammist\PluginSys\BotAPI;


class PluginStorage extends Storage implements \Stringable
{
    use Traits\Nameable;
    use Traits\Activatable;

    public function __construct(string $name, Plugin ...$plugins)
    {
        if ($name === '') {
            throw new \Exception("Storage name could not be empty");
        }
        $this->name = \strtolower($name);
        parent::__construct(...$plugins);
    }

    public function executeAll(mixed ...$args): void
    {
        foreach ($this->container as $plugin) {
            $plugin(...$args);
        }
    }
}
