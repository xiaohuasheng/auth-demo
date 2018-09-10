<?php
/**
 * Created by PhpStorm.
 * User: xhs
 * Date: 2017/9/17
 * Time: 20:40
 */

namespace app\controllers;


use app\models\Article;
use yii\BaseYii;
use yii\debug\models\search\Log;
use yii\web\Controller;
use Yii;

class IndexController extends Controller
{
    public function actionIndex()
    {
//        $this->layout = 'simple';
        return $this->render('index');
    }

    public function task()
    {
        $str = "2017-10-15"; //我和华哥
        $startTime = strtotime($str);
        $twoWeek = 1209600;
        $now = strtotime(date("Y-m-d", time()));
        //$now = strtotime("2017-08-06");
        //$now = strtotime("2017-07-24");
        //$now = strtotime("2017-08-21");
        //echo ($now - $startTime) / $twoWeek;
        $condition = floor((($now - $startTime) / $twoWeek)) % 2;
        if ( $condition == 0 )
        {
            echo "华生，卫华";
        }
        else if ( $condition == 1 )
        {
            echo "灿彬，杨帆";
        }else
        {
            echo "error";
        }

    }
}