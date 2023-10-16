<?php


namespace dvb\templater\parsers;


abstract class AbstractConditions
{

    /**
     * Calling from parseIfCallbackPrepare
     * @param $str
     * @return string
     */
    protected function replaceDotToArrayElem($str)
    {
        $chain = $str;
        $items = explode('.', $chain);
        if(is_numeric($items[0]))
            return $items[0];
        $var = '$'.$items[0];
        if(count($items)>1) {
            unset($items[0]);
            $elems = "['" . implode("']['", $items) . "']";
            $var = $var . $elems;
        }
        return $var;
    }

    /**
     * Calling from parseIfCallback, parseElseIfCallback
     * @param $matches
     * @return array
     */
    protected function parseIfCallbackPrepare($matches)
    {
        $criteria = $matches[2];
        $inner = (isset($matches[3])) ? $matches[3] : ''; // Часть между {{if ...}} и {{/if}}

        $pattern = "%([a-zA-Zа-яА-Я_0-9\.'\"]+)\s?([\=\!\>\<]*)\s?([а-яА-Яa-zA-Z_0-9\.'\"]*)%u";
        preg_match($pattern, $criteria, $m4);
        $operandLeft = $m4[1];
        $operator = $m4[2];
        $operandRight = $m4[3];

        $operandLeft = $this->replaceDotToArrayElem($operandLeft);

        if($operator && $operandRight){
            $t = ['false', 'true'];
            $keyWords = $t;

            if(is_numeric($operandRight) || preg_match("%^('|\")(.+)('|\")$%", $operandRight)){
                // Оставляем, как есть
            } else {
                if(in_array($operandRight, $keyWords)){
                    // Оставляем, как есть
                } else {
                    $operandRight = $this->replaceDotToArrayElem($operandRight);
                }
            }

            $criteriaStr = $operandLeft.' '.$operator.' '.$operandRight;
        } else {
            $criteriaStr = 'isset(' . $operandLeft . ') && !empty(' . $operandLeft . ')';
        }

        return ['criteriaStr'=>$criteriaStr, 'inner'=>$inner];
    }

    /**
     * Calling from groupIfCallback
     * @param $matches
     * @return string
     */
    protected function parseIfCallback($matches)
    {
        $t = $this->parseIfCallbackPrepare($matches);
        $open = '<?php if(';
        $criteriaStr = $t['criteriaStr'];
        $close = '<?php endif; ?>';

        return $open.$criteriaStr.'): ?>'.$t['inner'].$close;
    }
    /**
     * Calling from parseIf
     * @param $matches
     * @return string
     */
    function groupIfCallback($matches)
    {
        $pattern = "%({{if )(.+)}}(.*)({{\/if}})%sU";
        $result = preg_replace_callback($pattern, [$this, 'parseIfCallback'], $matches[0]);
        return $result;
    }
    /**
     * Calling from parseElseIf
     * @param $matches
     * @return string
     */
    protected function parseElseIfCallback($matches)
    {
        $t = $this->parseIfCallbackPrepare($matches);
        $open = '<?php elseif(';
        $criteriaStr = $t['criteriaStr'];

        return $open.$criteriaStr.'): ?>';
    }

}