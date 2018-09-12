<?php

use yii\bootstrap\ActiveForm;
use yii\helpers\Html;


/* @var $this yii\web\View */
/* @var $model app\models\User */

$this->title = '角色设置';
$this->params['breadcrumbs'][] = ['label' => '分配角色', 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="admin-user-update">

    <h1><?= Html::encode($this->title) ?></h1>
    <div class="admin-user-privilege-form">
        <?php $form = ActiveForm::begin();?>
        <?= Html::checkboxList('newPri', $authItemAssignmentArray, $allPrivilegesArray)?>
        <div class="form-group">
            <?=Html::submitButton('设置', ['class' => 'btn btn-success'])?>
        </div>
        <?php ActiveForm::end();?>
    </div>

</div>
