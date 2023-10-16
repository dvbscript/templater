<?php include "includes/header.php"; ?>

Здравствуйте, <?= $name ?>!<br /><br />

<?= $address['city'] ?>

<hr />
<h3>Пример вложенности двух разных массивов:</h3>
<?php foreach($arr as $key_arr => $value_arr): ?>
    марка <?= $value_arr["id"] ?> <br />
    <?php foreach($arr3 as $key_arr3 => $value_arr3): ?>
        &nbsp;&nbsp;&nbsp;Товар <?= $value_arr3["lot"] ?> <br />
    <?php endforeach; ?>
<?php endforeach; ?>
<hr />

<br />Это некий текст
<br /><br />
<hr />

<?php foreach($arr2 as $key_arr2 => $value_arr2): ?> имя: <?= $value_arr2["name"] ?>,  фамилия: <?= $value_arr2["lastName"] ?> <br /><?php endforeach; ?>
<hr />

<br />Опять какой-то текст
<br /><br />
<hr />

<h3>Пример вложенного массива из одного многомерного массива:</h3>
<?php foreach($arr4 as $key_arr4 => $value_arr4): ?>
    Марка: <?= $value_arr4["name"] ?> <br />
    <?php foreach($value_arr4["lot"] as $key_value_arr4_lot => $value_value_arr4_lot): ?>
        &nbsp;&nbsp;&nbsp;Товар <?= $value_value_arr4_lot ?> <br />
    <?php endforeach; ?>
<?php endforeach; ?>
<hr />

<?php if($name == 'Дмитрий'): ?>
      <div>Условие с именем <?= $name ?> совпало</div>
      <?php if($name == 'Иван'): ?>
            <div>Условие с именем Иван совпало</div>
      <?php elseif(1 != 1): ?>
            <div>name == 1</div>
      <?php else: ?>
            <div>Вложенный ELSE</div>
      <?php endif; ?>
<?php endif; ?>

<br />Подвал.

