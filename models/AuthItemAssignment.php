<?php

namespace app\models;

use Yii;

/**
 * This is the model class for table "auth_item_assignment".
 *
 * @property string $id
 * @property string $uid 用户ID
 * @property string $item_id 角色或权限ID
 */
class AuthItemAssignment extends \yii\db\ActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'auth_item_assignment';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['uid', 'item_id'], 'integer'],
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'uid' => 'Uid',
            'item_id' => 'Item ID',
        ];
    }
}
