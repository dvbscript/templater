<?php


namespace dvb\templater\loader;


class FromFile implements LoaderInterface
{

    protected $siteRoot = '';
    private $srcFolder = '';
    private $file;

    /**
     * @var FromString[] - Допустимые расширения файлов с шаблонами.
     */
    public $extensions = ['', 'tpl'];

    protected $template;

    public function folder($folder='')
    {
        if($folder) {
            // Пришло вызовом через \dvb\templater\Loader::__call()
            if(is_array($folder))
                $folder = $folder[0];
            $this->srcFolder = $folder;
        }
        $_folder = str_replace('\\', '/', $this->srcFolder);
        $_folder = trim($_folder, '/');
        $_folder = ( $this->getSiteRoot() . preg_replace("%^" . $this->getSiteRoot() . "%", "", $_folder) ) . '/';
        if(is_dir($_folder)){
            if($folder)
                return $this;
            return $_folder;
        } else {
            throw new \Exception("Директория для исходников {$_folder} отсутствует.");
        }
    }

    public function file()
    {
        return $this->file;
    }

    public function load($template='')
    {
        if($template){
            $this->file = $template;
        } else {
            $template = $this->file;
        }
        $this->template = str_replace('\\', '/', $template);
        $this->template = trim($this->template, '/');
        $this->template = $this->folder().$this->template;

        $templateFileName = $this->getRawFullPath();
        $file_exists = false;

        foreach ($this->extensions as $ext){
            if($ext) $ext = '.'.$ext;
            if(file_exists($templateFileName.$ext)){

                // Дальше нам нет нужды работать с псевдоименем без расширения, если у реального файла оно есть
                $this->template = $templateFileName = $templateFileName.$ext;
                $file_exists = true;
                break;
            }
        }

        if($file_exists){
            return file_get_contents($templateFileName);
        } else {
            throw new \Exception("Шаблон {$templateFileName} отсутствует.");
        }
    }

    private function getSiteRoot()
    {
        return ( $_SERVER['DOCUMENT_ROOT'] ? $_SERVER['DOCUMENT_ROOT'].'/' : $this->siteRoot );
    }

    private function getRawFullPath()
    {
        $template = $this->template;
        $clear = preg_replace("%^" . $this->getSiteRoot() . "%", "", $template);
        return ( $this->getSiteRoot() .  $clear);
    }

    public function getFullPath()
    {
        return ( $this->getSiteRoot() . preg_replace("%^" . $this->getSiteRoot() . "%", "", $this->template) );
    }

    public function getFileName()
    {
        return basename($this->template);
    }

    /** @todo - добавить в интерфейс?
     * @return false|int
     */
    public function updatedAt()
    {
        return filemtime($this->getFullPath());
    }

}