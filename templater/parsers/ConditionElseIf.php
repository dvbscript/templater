<?php


namespace dvb\templater\parsers;


class ConditionElseIf extends AbstractConditions implements ParsersInterface
{
    public function replace($tpl)
    {
        $pattern = "%({{elseif )(.+)}}%U";
        $t = preg_replace_callback($pattern, [$this, 'parseElseIfCallback'], $tpl);
        return $t;
    }
}