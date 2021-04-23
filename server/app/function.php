<?php
/**
 * Created by PhpStorm.
 * User: yirilin
 * Date: 2018/4/28
 * Time: 10:56
 */

/**
 * 获取随机数组
 * @param int $mix
 * @param int $max
 * @param int $limit
 * @return array
 */
function randLimit($mix=1,$max=1000,$limit=20){
    $rand_array = range($mix, $max);
    shuffle($rand_array);
    return array_slice($rand_array, 0, $limit);
}

/**
 * 生成相应长度的随机字符串
 * @param $length
 * @return string|null
 */
function getNonceStr($length){
    $str = null;
    $strPol = "ABCDEFGHIJKLMNOPQRSTUVWXYZ0123456789abcdefghijklmnopqrstuvwxyz";
    $max = strlen($strPol)-1;
    for ($i=0; $i<$length; $i++) {
        $str .= $strPol[mt_rand(0,$max)];
    }
    return $str;
}

/**
 * 计算两个时间戳相差多少年，月，日
 * @param $time1
 * @param $time2
 * @return array
 */
function diffDate($time1, $time2) {
    if ($time1 > $time2) {
        $temp = $time2;
        $time2 = $time1;
        $time1 = $temp;
    }
    list($y1, $m1, $d1) = explode('-', date('Y-m-d',$time1));
    list($y2, $m2, $d2) = explode('-', date('Y-m-d',$time2));
    $y = $m = $d = $_m = 0;
    $math = ($y2 - $y1) * 12 + $m2 - $m1;
    $y = round($math / 12);
    $m = intval($math % 12);
    $d = (mktime(0, 0, 0, $m2, $d2, $y2) - mktime(0, 0, 0, $m2, $d1, $y2)) / 86400;
    if ($d < 0) {
        $m -= 1;
        $d += date('j', mktime(0, 0, 0, $m2, 0, $y2));
    }
    $m < 0 && $y -= 1;
    return array($y, $m, $d);
}
