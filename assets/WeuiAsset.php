<?php
namespace app\assets;

use yii\web\AssetBundle;

/**
 * @author xiaohuasheng
 * @since 1.0
 */
class WeuiAsset extends AssetBundle
{
    public $basePath = '@webroot';
    public $baseUrl = '@web';
    public $css = [
//        'css/weui/style/icon.css',
        'css/weui/style/example.css',
        'css/weui/style/weui.css',
//        'css/weui/style/weui2.css',
//        'css/weui/style/weui3.css',
    ];
    public $js = [
        'js/weui/zepto.min.js',
        'js/weui/iscroll.js',
    ];
    public $depends = [
    ];
}
