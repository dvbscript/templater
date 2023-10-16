<?php


namespace dvb\templater\parsers;


class Vars implements ParsersInterface
{
    public function replace($tpl)
    {
        $pattern = "%{{([a-zA-Z0-9_]*)}}%U";
        $replacement = '<?= $${1} ?>';
        $t = preg_replace($pattern, $replacement, $tpl);
        return $t;
    }
}