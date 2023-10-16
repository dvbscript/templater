<?php


namespace dvb\templater;


use dvb\templater\loader\FromFile;
use dvb\templater\loader\FromString;

class LoaderFactory
{
    private static $instance = null;

    public static function instance()
    {
        if(is_null(self::$instance))
            self::$instance = new self();
        return self::$instance;
    }

    public function fromFile($filePath)
    {
        return $this->fileLoader()->load($filePath);
    }

    public function fromString($str)
    {
        return $this->stringLoader()->load($str);
    }

    public function fileLoader()
    {
        return new Loader(new FromFile());
    }

    public function stringLoader()
    {
        return new Loader(new FromString());
    }

}