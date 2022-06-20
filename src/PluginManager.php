<?php declare(strict_types=1);

namespace Piagrammist\PluginSys\BotAPI;


class PluginManager
{
    public const UPDATE_TYPES = [
        'message',
        'edited_message',
        'channel_post',
        'edited_channel_post',
        'inline_query',
        'chosen_inline_result',
        'callback_query',
        'shipping_query',
        'pre_checkout_query',
        'poll',
        'poll_answer',
        'my_chat_member',
        'chat_member',
        'chat_join_request',
    ];

    protected string $path;
    protected array  $container;

    public function __construct(string $dir)
    {
        if ($dir === '') {
            throw new \Exception("Invalid path provided for plugins' directory");
        }
        if (!\is_dir($dir)) {
            throw new \RuntimeException("Directory '$dir' not found");
        }
        $this->path = $dir;
        $this->sync();
    }

    public function sync(): void
    {
        $this->reset();
        foreach (\glob(path($this->path, '*'), \GLOB_ONLYDIR | \GLOB_NOSORT) as $dir) {
            $dirname = \basename($dir);
            if ($dirname !== 'any') {
                // do NOT use 'isset' here!
                try {
                    $this->container[$dirname];
                } catch (\Throwable) {
                    continue;
                }
            }
            $storage = new PluginStorage($dirname);
            foreach (readDirectoryClosures($dir) as $filename => $closure) {
                /** @var  string  $filename */
                /** @var \Closure $closure */
                $storage->attach(new Plugin($filename, $closure));
            }
            if (\count($storage) > 0) {
                $this->container[$dirname] = $storage;
            }
        }
    }

    public function reset(): void
    {
        $this->container = \array_fill_keys(self::UPDATE_TYPES, null);
    }

    public function iter(): \Generator
    {
        foreach ($this->container as $update => $val) {
            if ($val !== null) {
                yield $update => $val;
            }
        }
    }

    public function get(string $updateType): ?PluginStorage
    {
        foreach ($this->container as $update => $val) {
            if ($update === $updateType) {
                return $val;
            }
        }
        throw new \Exception("Invalid update type provided");
    }

    public function getAll(): array
    {
        $copy = $this->container;
        foreach ($copy as $update => $val) {
            if ($val === null) {
                unset($copy[$update]);
            }
        }
        return $copy;
    }
}
