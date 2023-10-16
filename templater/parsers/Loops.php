<?php


namespace dvb\templater\parsers;


class Loops extends PhpFunctions implements ParsersInterface
{


    private function parseLoopsCallback($matches)
    {
        $arrayName = $matches[2];

        $_arrayName = explode('.', $arrayName);
        $arrayNameArr = $arrayNameVal = $arrayName;
        if( count($_arrayName) > 1 ){
            $arrayNameArr = $_arrayName[0].'["'.$_arrayName[1].'"]';
            $arrayNameVal = $_arrayName[0].'_'.$_arrayName[1];
        }

        $matches[1] = '<?php foreach(';
        $matches[2] = '$'.$arrayNameArr.'';
        $matches[4] = '<?php endforeach; ?>';

        $values = $matches[3]; // Например, {{id}}, если массив двухмерный. Во входном массиве это $arr['id'].
        $pattern = "%({{)([a-zA-Z_0-9]+)(}})%m";
        preg_match_all($pattern, $values, $m);
        $replacement = '<?= $value_'.$arrayNameVal.'["'.'${2}'.'"] ?>';
        $values = preg_replace($pattern, $replacement, $values);

        $pattern = '%({{\$value}})%m'; // Если массив одномерный, то вставляем в шаблон {{$value}} @todo - описать, что вставлять при вложеном массиве.
        if(preg_match_all($pattern, $values, $m2)){
            $replacement = '<?= $value_'.$arrayNameVal.' ?>';
            $values = preg_replace($pattern, $replacement, $values);
        }

        $pattern = '%({{\$key}})%m';
        if(preg_match_all($pattern, $values, $m2)){
            $replacement = '<?= $key_'.$arrayNameVal.' ?>';
            $values = preg_replace($pattern, $replacement, $values);
        }

        // Ищет конструкции типа {{mileage|number_format: 0 "" " "}}
        if(preg_match_all($this->parsePhpFunctionsPattern(), $values, $m3)) {
            $values = preg_replace_callback($this->parsePhpFunctionsPattern(), function ($m4) use ($arrayNameVal){
                $matches = $m4;
                return $this->makePhpFunction($matches[4], $matches[3], '$value_'.$arrayNameVal.'["'.$matches[2].'"]');
            }, $values);
        }

        return $matches[1].($matches[2].' as $key_'.$arrayNameVal.' => $value_'.$arrayNameVal.'): ?>').$values.$matches[4];
    }

    function groupLoopsCallback($matches)
    {
        $pattern = "%({{loop )([a-zA-Z_0-9\.]+)}}(.*)({{\/loop}})%sU";
        $result = preg_replace_callback($pattern, [$this, 'parseLoopsCallback'], $matches[0]);
        return $result;
    }


    public function replace($tpl)
    {
        // К сожалению, при большом количестве текста это регулярное выражение встает колом,
        // поэтому сначала будем вычленять циклы, а разбивать на то, что у них внутри (регулярное выражение с группировкой) будем отдельным колбэком.
        // ЭТО РЕГУЛЯРНОЕ ВЫРАЖЕНИЕ ЛОВИТ ТОЛЬКО ВЛОЖЕННЫЕ ЦИКЛЫ, НЕ ИМЕЮЩИЕ ВЛОЖЕННОСТИ, ИЛИ ЦИКЛЫ БЕЗ ВЛОЖЕННОСТИ.
        $pattern = "%{{loop(?:(?!({{loop|{{\/loop}})).)*{{\/loop}}%sU";
        $tpl = preg_replace_callback($pattern, [$this, 'groupLoopsCallback'], $tpl);

        // Теперь отлавливаем внешние циклы. Внутренние циклы уже превратились из {{loop... в <?php foreach..., и не будут мешать.
        // И рекурсивно их рабираем
        $test = preg_match("%{{loop%", $tpl);
        if($test) {
            return $this->replace($tpl);
        }

        return $tpl;
    }

}