<?php


namespace dvb\templater\parsers;


class ConditionIf extends AbstractConditions implements ParsersInterface
{
    public function replace($tpl)
    {
        // Та же история, что и с циклами.
        // Вообще, проблема в блочных структурах, в которых пытаемся СРАЗУ проводить проверку на ВЛОЖЕННОСТЬ.
        // В итоге, мы идем "изнутри" вложенности, т.е. первым проходом превращаем в PHP самый внутренний IF (или IF не имеющий вложений),
        // а потом рекурсивно добиваем более внешние блоки.
        // ВЫЧЛЕНЯЕМ БЛОКИ, КОТОРЫЕ НЕ ИМЕЮТ ВЛОЖЕННОСТИ.
        $pattern = "%{{if(?:(?!({{if|{{\/if}})).)*{{\/if}}%sU";
        $t = preg_replace_callback($pattern, [$this, 'groupIfCallback'], $tpl);

        $test = preg_match("%{{if%", $t);
        if($test) {
            return $this->replace($t);
        }

        return $t;
    }
}