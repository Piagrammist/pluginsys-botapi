<?php declare(strict_types=1);

namespace Piagrammist\PluginSys\BotAPI;


class PluginManager
{
    protected string $path;
    protected array  $updates = [
        'message'              => null,
        'edited_message'       => null,
        'channel_post'         => null,
        'edited_channel_post'  => null,
        'inline_query'         => null,
        'chosen_inline_result' => null,
        'callback_query'       => null,
        'shipping_query'       => null,
        'pre_checkout_query'   => null,
        'poll'                 => null,
        'poll_answer'          => null,
        'my_chat_member'       => null,
        'chat_member'          => null,
        'chat_join_request'    => null,
    ];

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
        foreach (\glob(path($this->path, '*'), \GLOB_ONLYDIR | \GLOB_NOSORT) as $dir) {
            $dirname = \basename($dir);
            // do NOT use 'isset' here!
            try {
                $this->updates[$dirname];
            } catch (\Throwable) {
                continue;
            }
            $storage = new PluginStorage($dirname);
            foreach (readDirectoryClosures($dir) as $filename => $closure) {
                /** @var  string  $filename */
                /** @var \Closure $closure */
                $storage->attach(new Plugin($filename, $closure));
            }
            if (\count($storage) > 0) {
                $this->updates[$dirname] = $storage;
            }
        }
    }

    public function iter(): \Generator
    {
        foreach ($this->updates as $update => $val) {
            if ($val !== null) {
                yield $update => $val;
            }
        }
    }

    public function get(string $updateType): ?PluginStorage
    {
        foreach ($this->updates as $update => $val) {
            if ($update === $updateType) {
                return $val;
            }
        }
        throw new \Exception("Invalid update type provided");
    }

    public function getAll(): array
    {
        $copy = $this->updates;
        foreach ($copy as $update => $val) {
            if ($val === null) {
                unset($copy[$update]);
            }
        }
        return $copy;
    }
}
