<?php



use dvb\templater\Templater;

/**
 * Шаблон в виде строки
 */
$tpl = '
Начало строки<br /><br />
Привет, {{name}}!<br /><br />
Пример вложенности двух разных массивов:<br>
{{loop arr}} 
    марка {{id}} <br />
    {{loop arr3}} 
        &nbsp;&nbsp;&nbsp;Lot {{lot}} <br />
    {{/loop}}
{{/loop}}
<br />Это некий текст
<br /><br />
{{loop arr2}} имя: {{name}}  фамилия: {{lastName}} <br />{{/loop}}
<br />Опять какой-то текст
<br /><br />
Пример вложенного массива из одного многомерного массива:<br />
{{loop arr4}}
    Марка: {{name}} <br />
    {{loop value_arr4.lot}} 
        &nbsp;&nbsp;&nbsp;Lot {{$value}} <br />
    {{/loop}}
{{/loop}}
<br />Подвал, конец строки.
';


/**
 * Различные данные, которые будут передаваться в шаблон
 */
$arr = [
    ['id'=>1],
    ['id'=>2],
    ['id'=>3],
];

$arr2 = [
    ['name'=>'Иван', 'lastName'=>'Иванов'],
    ['name'=>'Петр', 'lastName'=>'Петров'],
];

$arr3 = [
    ['lot'=>111111],
    ['lot'=>222222],
];

$arr4 = [
    [
        'name'=>'KIA',
        'lot'=>[
            333333,
            444444,
        ]
    ],
    [
        'name'=>'FORD',
        'lot'=>[
            555555,
            666666,
        ]
    ],
];


spl_autoload_register('autoloader');
function autoloader($class) {
    $class = str_replace("dvb", "", $class);
    $class = str_replace("\\", "/", $class);
    include_once('./../'.$class.'.php');
}




// Шаблонизация из данных в виде строки
$templater = new Templater();
$out = $templater->loadSrcString( $tpl )
    ->render(['name'=>'Дмитрий', 'arr'=>$arr, 'arr2'=>$arr2, 'arr3'=>$arr3, 'arr4'=>$arr4]);

// Шаблонизация из файла
define('TEMPLATE_SRC_FOLDER', '/templater/examples/templates_src/');
$templater = new Templater();
$out = $templater->loadSrcFile( TEMPLATE_SRC_FOLDER, 'demo' )
    ->render(['name'=>'Дмитрий', 'arr'=>$arr, 'arr2'=>$arr2, 'arr3'=>$arr3, 'arr4'=>$arr4]);







