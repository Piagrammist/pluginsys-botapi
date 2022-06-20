<?php declare(strict_types=1);

namespace Piagrammist\PluginSys\BotAPI;


class Storage implements \Countable, \Iterator
{
    public readonly \SplObjectStorage $container;

    public function __construct(object ...$objects)
    {
        $this->container = new \SplObjectStorage;
        foreach ($objects as $object) {
            $this->container->attach($object);
        }
    }

    public function reset(): void
    {
        $this->container->removeAll($this->container);
    }

    final public function count()  : int   { return $this->container->count()  ; }
    final public function current(): mixed { return $this->container->current(); }
    final public function key()    : mixed { return $this->container->key()    ; }
    final public function next()   : void  {        $this->container->next()   ; }
    final public function rewind() : void  {        $this->container->rewind() ; }
    final public function valid()  : bool  { return $this->container->valid()  ; }
}
