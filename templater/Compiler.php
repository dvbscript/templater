<?php


namespace dvb\templater;

use \dvb\templater\parsers as parsers;

class Compiler
{
    protected $register = [
        \dvb\templater\parsers\Comments::class,
        \dvb\templater\parsers\ConditionIf::class,
        \dvb\templater\parsers\ConditionElseIf::class,
        \dvb\templater\parsers\ConditionElse::class,
        \dvb\templater\parsers\Loops::class,
        \dvb\templater\parsers\Vars::class,
        \dvb\templater\parsers\ArrayElements::class,
        \dvb\templater\parsers\PhpFunctions::class,
        // Сюда добавлять парсеры других элементов, если появятся
    ];

    protected $loader;
    private $includes = false;

    public function __construct(Loader $loader)
    {
        $this->loader = $loader;
    }

    public function loader()
    {
        return $this->loader;
    }

    public function includes()
    {
        return $this->includes;
    }

    public function parseIncludes($template)
    {
        $parser = new parsers\Includes($this->loader);
        $php = $parser->replace($template);
        $this->includes = $parser->includes();
        return $php;
    }

    public function compile($template)
    {
        $php = $this->parseIncludes($template);
        foreach ($this->register as $register){
            $parser = new $register;
            $php = $parser->replace($php);
        }
        return $php;
    }

}