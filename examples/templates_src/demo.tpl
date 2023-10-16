{{include includes/header}}

Здравствуйте, {{name}}!<br /><br />

{{address.city}}

<hr />
<h3>Пример вложенности двух разных массивов:</h3>
{{loop arr}}
    марка {{id}} <br />
    {{loop arr3}}
        &nbsp;&nbsp;&nbsp;Товар {{lot}} <br />
    {{/loop}}
{{/loop}}
<hr />

<br />Это некий текст
<br /><br />
<hr />

{{loop arr2}} имя: {{name}},  фамилия: {{lastName}} <br />{{/loop}}
<hr />

<br />Опять какой-то текст
<br /><br />
<hr />

<h3>Пример вложенного массива из одного многомерного массива:</h3>
{{loop arr4}}
    Марка: {{name}} <br />
    {{loop value_arr4.lot}}
        &nbsp;&nbsp;&nbsp;Товар {{$value}} <br />
    {{/loop}}
{{/loop}}
<hr />

{{if name=='Дмитрий'}}
      <div>Условие с именем {{name}} совпало</div>
      {{if name=='Иван'}}
            <div>Условие с именем Иван совпало</div>
      {{elseif 1!=1}}
            <div>name == 1</div>
      {{else}}
            <div>Вложенный ELSE</div>
      {{/if}}
{{/if}}

<br />Подвал.

