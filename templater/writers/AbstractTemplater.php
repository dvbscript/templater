<?php


namespace dvb\templater\writers;


abstract class AbstractTemplater
{

    /**
     * @var string - Корень сайта.
     * Крайне желательно установить его, чтобы вызывать шаблонизатор без указания полного пути в файловой системе при вызове скрипта из командной строки.
     */
    public $siteRoot = __DIR__."/";

    /**
     * @var string - Путь к скомпилированным файлам PHP. Менять эту настройку можно, но не нужно.
     */
    public $compiledDir = __DIR__ . "/../templates_compiled/";

    protected $deleteTmp = true;
    protected $srcDir = "";
    protected $template;
    protected $tmpPhpFileName = '__tmp__';
    protected $assignedVars = [];
    public    $forceRecompile = false;
    protected $phpCode = '';
    protected $renderedPhpFilePath = '';
    protected $srcLoader;
    protected $compiler;
    protected $srcFile;
    protected $srcPath;

    public function __construct($phpCode=null)
    {
        $this->siteRoot = $this->getSiteRoot();
        $this->getCompiledDir();
        if(!is_dir($this->compiledDir)){
            if(!mkdir($this->compiledDir))
                throw new \Exception("Отсутствует директория для скомпилированных файлов ({$this->compiledDir}).");
        }

        $this->phpCode = $phpCode;
    }

    protected function getCompiledDir()
    {
        $this->compiledDir = str_replace('\\', '/', $this->compiledDir);
        $this->compiledDir = ( $this->getSiteRoot() . preg_replace("%^" . $this->getSiteRoot() . "%", "", $this->compiledDir) ) . '/';
        $this->compiledDir = str_replace('//', '/', $this->compiledDir);
        return $this->compiledDir;
    }

    /**
     *
     * @return void
     */
    abstract protected function compile();

    abstract protected function loadIncludes($includes);

    /**
     * Вывод результата обработки шаблона в поток.
     *
     * @param array $vars - переменные, которые принимает шаблон. Например: ['name'=>'Дима', 'lots'=>[...]]. Эти переменные могут также устанавливаться методом assign()
     * @return mixed - Включаем PHP файл и возвращаем его.
     */
    abstract function render($vars=[]);

    protected function getSiteRoot()
    {
        return $_SERVER['DOCUMENT_ROOT'] ? $_SERVER['DOCUMENT_ROOT'].'/' : $this->siteRoot;
    }

    protected function getCompiledFilePath()
    {
        return $this->renderedPhpFilePath;
    }

    protected function createPhpTmp()
    {
        $phpFile = $this->compiledDir.$this->tmpPhpFileName.'.php';
        $this->compile();
        $this->renderedPhpFilePath = $phpFile;
        $this->savePhpFile();
    }

    protected function makePhpPathFromSrc()
    {
        $templateFileName = $this->srcFile;
        $templateFileName = preg_replace("%\.".pathinfo($templateFileName, PATHINFO_EXTENSION)."%", "", $templateFileName);

        // PHP файл берется или создается по имени шаблона
        $ret = $this->compiledDir.$templateFileName .'.php';
        return $ret;
    }

    protected function createPhp()
    {
        $this->renderedPhpFilePath = $this->makePhpPathFromSrc();
        $this->buildPHP();
    }

    /**
     * Проверяет, существует ли на диске PHP файл, если нет записывает его на диск;
     * Проверяет, обновлялся ли исходник шаблона, если обновлялся, перезаписывает его;
     * В любом случае парсит исходник на предмет инклюдов, т.к. они могли измениться
     *
     * @return bool
     */
    protected function buildPHP()
    {
        $phpFile = $this->makePhpPathFromSrc();

        $needToWritePhpFile = false;

        // PHP файла еще не существует - создаем его, проходя все функции парсинга
        if(!file_exists($phpFile)){
            $needToWritePhpFile = true;
        } else {
            $tplTime = $this->srcLoader->updatedAt();
            $phpTime = filemtime($phpFile);
            if($tplTime > $phpTime) {
                // PHP файл существует, но шаблон был обновлен - перезаписываем его, проходя все функции парсинга
                $needToWritePhpFile = true;
            }
        }

        // Если основной шаблон не надо перезаписывать, всё равно заходим в инклуды - там могли быть изменения
        if(!$needToWritePhpFile) {
            $this->compiler->parseIncludes($this->template);
            $includes = $this->compiler->includes();
            $this->loadIncludes($includes);
        }

        // Парсим, создаем PHP файл
        if($needToWritePhpFile || $this->forceRecompile){
            $this->compile();
            $this->savePhpFile();
        }

        return true;
    }

    /**
     *
     * @return false|int
     */
    protected function savePhpFile()
    {
        $phpFile = $this->renderedPhpFilePath;
        $phpCode = $this->phpCode;

        // Создаем директории для инклудов
        $dir = dirname($this->compiledDir.$this->srcFile);
        if(!is_dir($dir)) {
            mkdir($dir, 0777, true);
        }

        return file_put_contents($phpFile, $phpCode);
    }

    public function __destruct()
    {

    }

}