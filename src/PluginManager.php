<?php declare(strict_types=1);

namespace Piagrammist\PluginSys\BotAPI;


class PluginManager extends Storage
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

    public function __construct(string $dir)
    {
        if ($dir === '') {
            throw new \Exception("Invalid path provided for plugins' directory");
        }
        if (!\is_dir($dir)) {
            throw new \RuntimeException("Directory '$dir' not found");
        }
        parent::__construct();
        $this->path = $dir;
        $this->sync();
    }

    public function sync(): void
    {
        $this->reset();
        foreach (\glob(path($this->path, '*'), \GLOB_ONLYDIR | \GLOB_NOSORT) as $dir) {
            $dirname = \basename($dir);
            if ($dirname !== 'any' && !in_array($dirname, self::UPDATE_TYPES)) {
                continue;
            }
            $storage = new PluginStorage($dirname);
            foreach (readDirectoryClosures($dir) as $filename => $closure) {
                /** @var  string  $filename */
                /** @var \Closure $closure */
                $storage->container->attach(new Plugin($filename, $closure));
            }
            if (\count($storage) > 0) {
                $this->container->attach($storage);
            }
        }
    }

    public function get(string $updateType): PluginStorage
    {
        foreach ($this->container as $storage) {
            /** @var PluginStorage $storage */
            if ($storage->name === $updateType) {
                return $storage;
            }
        }
        throw new \Exception("Invalid update type provided");
    }
}
