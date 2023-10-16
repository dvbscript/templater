<?php


namespace dvb\templater;


use dvb\templater\loader\LoaderInterface;

class Loader
{

    private $strategy;

    public function __construct(LoaderInterface $strategy)
    {
        $this->strategy = $strategy;
    }

    public function load($template)
    {
        return $this->strategy->load($template);
    }

    /**
     * Для возможности вызова методов, присущих только конкретной стратегии
     *
     * @param $name
     * @param $arguments
     * @return mixed
     */
    public function __call($name, $arguments)
    {
        return $this->strategy->{$name}($arguments);
    }

}