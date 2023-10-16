<?php


namespace dvb\templater\parsers;


use dvb\templater\Loader;

class Includes implements ParsersInterface
{
    private $includes = '';
    protected $loader;

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

    public function replace($tpl)
    {
        $pattern = "%{{\s?include ([a-zA-Z0-9_\/]*)\s?}}%U";
        $replacement = '<?php include "${1}.php"; ?>';
        if(preg_match_all($pattern, $tpl, $matches)) {
            $this->includes = $matches[1];
        }
        $t = preg_replace($pattern, $replacement, $tpl);
        return $t;
    }
}