<?php

namespace dvb\templater\tests\writers;

use dvb\templater\Compiler;
use dvb\templater\LoaderFactory;

class LoadedFile extends AbstractTemplater
{

    public function render($vars=[])
    {
        $vars = array_merge($this->assignedVars, $vars);
        extract($vars);
        $this->createPhp();
        return include $this->getCompiledFilePath();
    }


    public function loadSrcFile($folder, $file)
    {
        $this->srcLoader = LoaderFactory::instance()->fileLoader();
        $this->template = $this->srcLoader->folder($folder)->load($file);
        $this->srcPath = $this->srcLoader->getFullPath();
        $this->srcDir = $folder;
        $this->srcFile = $file;

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

        $includes = $this->compiler->includes();
        $this->loadIncludes($includes);
    }


    /**
     * Рекурсивно загружает шаблонизатор с шаблоном, указанным в include
     *
     * @param $includes
     * @return void
     */
    protected function loadIncludes($includes)
    {
        if($includes){
            foreach ($includes as $include){

                // Путь к включаемому файлу не начинается со слэша, значит нужно искать файл в этой же директории.
                // А если начинается со слэша, то, скорее всего, будем считать, что поиск идет от корня сайта.
                if( !preg_match("%^\/%", $include) ){
                    echo '<pre>'; print_r( "Загружаем шаблон \"{$include}\"" ); echo '</pre>';

                    $currentDir = dirname($this->srcPath);

                    // Получаем вложенную папку относительно основной директории исходников (напр. "includes")
                    $currentInnerFolder = preg_replace("%^" . trim($this->srcLoader->folder(), '/') . "%", "", $currentDir);

                    // Добавляем эту папку к относительному пути инклуда (напр. "includes" . "banners/logo")
                    // и теперь мы будем создвать файл из основной директории исходников + полный путь от нее.
                    $newFileName = $currentInnerFolder.'/'.$include;

                    // Рекурсивно запускаем новый процесс шаблонизатора с постоянной папкой исходников, но где имя файла будет полным путем от нее
                    $templater = new self();
                    $templater->loadSrcFile( $this->srcDir, $newFileName );
                    $templater->createPhp();
                }

            }
        }
    }

}