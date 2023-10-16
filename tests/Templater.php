<?php


namespace dvb\templater\tests;


use dvb\templater\tests\writers\LoadedFile;
use dvb\templater\tests\writers\LoadedString;

class Templater
{

    public function loadSrcFile($folder, $file)
    {
        $templater = new LoadedFile();
        $templater->loadSrcFile( $folder, $file );
        return $templater;
    }

    public function loadSrcString($string)
    {
        $templater = new LoadedString();
        $templater->loadSrcString( $string );
        return $templater;
    }

}