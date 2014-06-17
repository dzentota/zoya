<?php
namespace Zoya\ProxySwitcher;

abstract class GenericAdapter implements AdapterInterface
{
    protected $switcher;

    public function __construct($switcher)
    {
        $this->switcher = $switcher;
    }

    public function getSwitcher()
    {
        return $this->switcher;
    }

    abstract public function switchProxy();
}
