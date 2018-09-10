<?php
/**
 * Created by PhpStorm.
 * User: xhs
 * Date: 2017/8/20
 * Time: 22:52
 */

namespace app\models;


use yii\base\Object;

class Property extends Object
{
    private $title;

    public function getTitle()
    {
        return $this->title;
    }

    public function setTitle($value)
    {
        $this->title = trim($value);
    }
}