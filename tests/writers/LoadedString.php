<?php

namespace dvb\templater\tests\writers;

use dvb\templater\Compiler;
use dvb\templater\LoaderFactory;

class LoadedString extends AbstractTemplater
{
    protected $deleteTmp = true;

    public function render($vars=[])
    {
        $vars = array_merge($this->assignedVars, $vars);
        extract($vars);
        $this->createPhpTmp();
        return include $this->getCompiledFilePath();
    }


    public function loadSrcString($string)
    {

        $this->srcLoader = LoaderFactory::instance()->stringLoader();
        $this->template = $this->srcLoader->load($string);

        $this->compiler = new Compiler($this->srcLoader);
    }

    /**
     *
     * @return void
     */
    protected function compile()
    {
        echo '<pre>'; print_r( "Компилируем шаблон \"{$this->srcFile}\"" ); echo '</pre>';
        $this->phpCode = $this->compiler->compile($this->template);
    }

    protected function loadIncludes($includes){}

    public function __destruct()
    {
        // шаблон был взят из строки и выше стоит установка на удаление
        if($this->deleteTmp) {
            echo '<pre>'; print_r( "Удаляем \"{$this->getCompiledFilePath()}\"" ); echo '</pre>';
            unlink($this->getCompiledFilePath()); // удаляем временный файл
        }
    }


}