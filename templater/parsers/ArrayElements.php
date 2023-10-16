<?php


namespace dvb\templater\parsers;


class ArrayElements implements ParsersInterface
{

    /**
     * @param $chain - например, data.lotsCount
     * @return string - например, $data['lotsCount']
     */
    protected function makeArrayElement($chain)
    {
        $items = explode('.', $chain);
        $arr = '$'.$items[0];
        unset($items[0]);
        $elems = "['".implode("']['", $items)."']";
        return $arr . $elems;
    }

    protected function parseArrayElementsCallback($matches)
    {
        return '<?= ' . $this->makeArrayElement($matches[1]) . ' ?>';
    }


    public function replace($tpl)
    {
        $pattern = "%{{([a-zA-Z0-9_\.]*)}}%U";
        $t = preg_replace_callback($pattern, [$this, 'parseArrayElementsCallback'], $tpl);
        return $t;
    }
}