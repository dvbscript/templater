<?php


namespace dvb\templater\parsers;


class PhpFunctions extends ArrayElements implements ParsersInterface
{


    /**
     * @return string - шаблон регулярного выражения для получения вызова функции PHP:
     * {{mileage|number_format: 0 "" " "}}
     */
    protected function parsePhpFunctionsPattern()
    {
        return "%({{)([a-zA-Z_0-9\.]+)\|([a-zA-Z_0-9]+):\s?(.+)(}})%U";
    }

    /**
     * Разбирает параметры, обрамленные кавычками (как двойными, так и одинарными), и числа
     * @param $str - примеры:
     *      0 "" " "
     *      "," "строка", 125
     *      ...
     * @return string - 0, "", " "
     */
    protected function makeFunctionParameters($str)
    {
        $openQuotesStartPos = 0;
        $paramsQuoted = [];
        while (1){
            $singleQuotesPos = @strpos($str, "'", $openQuotesStartPos);
            $doubleQuotesPos = @strpos($str, '"', $openQuotesStartPos);

            $numberPos = false;
            if(preg_match("%(\d+)%", $str, $matches, PREG_OFFSET_CAPTURE, $openQuotesStartPos)){
                $match = $matches[1];
                $numberPos = $match[1];
                $number = $match[0];
            }

            $foundedPos = 0;
            if($singleQuotesPos !== false && $doubleQuotesPos !== false && $numberPos !== false){
                $foundedPos = min($singleQuotesPos, $doubleQuotesPos, $numberPos);
            } elseif($singleQuotesPos !== false && $doubleQuotesPos !== false){
                $foundedPos = min($singleQuotesPos, $doubleQuotesPos);
            } elseif($singleQuotesPos !== false && $numberPos !== false){
                $foundedPos = min($singleQuotesPos, $numberPos);
            } elseif($doubleQuotesPos !== false && $numberPos !== false){
                $foundedPos = min($doubleQuotesPos, $numberPos);
            } elseif($singleQuotesPos !== false){
                $foundedPos = $singleQuotesPos;
            } elseif($doubleQuotesPos !== false){
                $foundedPos = $doubleQuotesPos;
            } elseif($numberPos !== false){
                $foundedPos = $numberPos;
            } else {
                break;
            }

            $foundedPosIs = '';
            if($foundedPos === $singleQuotesPos){
                $foundedPosIs = 'singleQuotesPos';
            } elseif($foundedPos === $doubleQuotesPos){
                $foundedPosIs = 'doubleQuotesPos';
            } elseif($foundedPos === $numberPos){
                $foundedPosIs = 'numberPos';
            }

            if($foundedPosIs == 'numberPos'){
                $paramQuoted = $param = $number;
                $paramsQuoted[] = $paramQuoted;
                $openQuotesStartPos = ($numberPos + strlen($param) + 1); // $closeQuotesPos + 1
                continue;
            }

            if($singleQuotesPos && $doubleQuotesPos){
                if($singleQuotesPos < $doubleQuotesPos) {
                    $openQuotes = $singleQuotesPos;
                    $quotes = "'";
                } elseif($doubleQuotesPos < $singleQuotesPos) {
                    $openQuotes = $doubleQuotesPos;
                    $quotes = '"';
                }
            } elseif ($singleQuotesPos){
                $openQuotes = $singleQuotesPos;
                $quotes = "'";
            } elseif ($doubleQuotesPos){
                $openQuotes = $doubleQuotesPos;
                $quotes = '"';
            } else {
                break;
            }

            $closeQuotesPos = strpos($str, $quotes, $openQuotes+1);
            $paramLength = ($closeQuotesPos-$openQuotes-1);
            $param = substr($str, $openQuotes+1, $paramLength);
            if($quotes == '"')
                $paramQuoted = '"'.$param.'"';
            if($quotes == "'")
                $paramQuoted = "'".$param."'";
            $paramsQuoted[] = $paramQuoted;

            $openQuotesStartPos = $closeQuotesPos+1; // $closeQuotesPos + 1
        }

        return implode(', ', $paramsQuoted);
    }

    /**
     * Возвращает готовый PHP код вызова функции
     * @param $str - То же, что и makeFunctionParameters($str) ( 0 "" " " )
     * @param $function - название функции PHP
     * @param $value - Значение в параметр функции, наприме для number_format($value_data_lots["start_price"], 0, "", " ") это будет $value_data_lots["start_price"]
     * @return string
     * @throws \Exception
     */
    protected function makePhpFunction($str, $function, $value)
    {
        $paramStr = $this->makeFunctionParameters($str);
        switch ($function){
            case 'number_format':
                return '<?= ' . $function.'('.$value.', '. $paramStr .')' . ' ?>';
                break;
            default:
                throw new \Exception("Функция {$function} не поддерживается");
        }
    }



    public function replace($tpl)
    {
        return preg_replace_callback($this->parsePhpFunctionsPattern(), function ($matches){
            $var = $this->makeArrayElement($matches[2]);
            return $this->makePhpFunction($matches[4], $matches[3], $var);
        }, $tpl);
    }
}