<?php
/**
 * Created by PhpStorm.
 * User: xhs
 * Date: 2017/9/21
 * Time: 22:29
 */

Yii::$app->cache->get("postCount");
//对缓存组件配置
//文件$cache
//把cache目录删除，耗时
// .bin 文件
/*
runtime/cache
=== 与 ==
multiGet()
delete
flush
缓存组件的可替代性
缓存过期的实现方式
trade-off

DbDependency
File
Chain
Expression
Group 标记为一个组
invalidate()




 * */
?>
<div>
    <p>
        实例太少；
        RBAC
        HTTP缓存
        ActiveRecord
        DetailView
        授权
        标签云
        控制台程序

        缓存

        数据缓存
        总共播放数
        疑问，更新策略呢？
        缓存过期，依赖
    </p>
    <p>
        片段缓存
            html
            支持嵌套，动态变化
        页面缓存
            behaviors
            pageCache
            'only' => ['index']
            'variations' => [
                ''
        ]
        <?php
        [
            'variations'=>[
                Yii::$app->request->get("page"),
                Yii::$app->request->get("PostSearch"),
            ]
        ];

        //HTTP 缓存
        $arr = [
            'httpCache'=>[
                'class' => 'HttpCache',
                'lastModified'=> '',
                'etagSeed' => function($action, $params){
                    serialize(); //Hash
                },
                'cacheControl' => ''
            ]
        ];
//        Last-Modified
//        HTTP 头，

        ?>

    </p>
    <p>
        yii.bat 运行脚本
        应用实体
        路由解析
        请求处理组件

        执行过滤
        响应处理组件
    </p>
    <p>
        结合上下文
        应用主体：对象
        behaviour
    </p>
    <p>
        称视图模板文件为视图
        模板
        $this 是View 对象
        $content
        zh-CN
        basePath
        defaultRoute
        components
    </p>
    <p>
        设置已提交标志
        块赋值
        验证规则
        数据导出

        业务数据
        业务规则
        业务逻辑

        ActiveRecord

        属性
        属性标签
        attributeLabel
        rules()
        导出

        像数组一样访问属性
        validate()
        :
        else:

    </p>
    <p>
        别名

    </p>














</div>
<div>
    对于计算比较耗时，访问频繁的数据，变化较小的数据，将
    结果存起来，二次访问的时候就直接取出结果，减少计算开销，
    提高系统响应速度。
    yii支持多种缓存
    数据缓存
    片段缓存
    页面缓存
    HTTP缓存

    最简单的是数据缓存
    <?php
    Yii::$app->cache->set("name", "xiaohuasheng");
    Yii::$app->cache->get("name");

    ?>
</div>
