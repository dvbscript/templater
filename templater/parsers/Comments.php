<?php


namespace dvb\templater\parsers;


class Comments implements ParsersInterface
{
    public function replace($tpl)
    {
        $pattern = "%{{\*(.+)\*}}%U";
        $replacement = '<?php /* ${1} */ ?>';
        $t = preg_replace($pattern, $replacement, $tpl);
        return $t;
    }
}