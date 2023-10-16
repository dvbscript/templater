<?php


namespace dvb\templater\parsers;


class ConditionElse extends AbstractConditions implements ParsersInterface
{
    public function replace($tpl)
    {
        $pattern = "%{{else}}%U";
        $replacement = '<?php else: ?>';
        $t = preg_replace($pattern, $replacement, $tpl);
        return $t;
    }
}