<?php
/**
 * Created by PhpStorm.
 * User: peterzhang
 * Date: 2020/7/13
 * Time: 3:30 PM
 */
function gen_one_to_three() {
    for ($i = 1; $i <= 3; $i++) {
        //注意变量$i的值在不同的yield之间是保持传递的。
        yield $i;
    }
}

$generator = gen_one_to_three();
foreach ($generator as $value) {
    echo "$value\n";
}

$i = 1;
function gen_one_to_three1() {
    global $i;
    if ($i<=3){
        return $i++;
    }
}

while ($value = gen_one_to_three1()) {
    echo "$value\n";
}