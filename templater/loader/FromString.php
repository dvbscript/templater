<?php


namespace dvb\templater\loader;


class FromString implements LoaderInterface
{
    public function load($template)
    {
        return $template;
    }

}