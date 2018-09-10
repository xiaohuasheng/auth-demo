<?php
/**
 * Created by PhpStorm.
 * User: xhs
 * Date: 2017/10/17
 * Time: 23:47
 */

namespace app\models;


class Utils
{
    public static function vardump($obj)
    {
        echo '<pre>';
        var_dump($obj);
        echo '</pre>';
    }

    public static function printf($obj)
    {
        echo '<pre>';
        print_r($obj);
        echo '</pre>';
    }
}