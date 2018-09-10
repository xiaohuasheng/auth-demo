<?php
/**
 * Created by PhpStorm.
 * User: watson.zeng
 * Date: 2018/9/10
 * Time: 19:24
 */

namespace app\models;


use yii\db\Query;

class Auth
{
    const TYPE_ROLE = 1;
    const TYPE_PERMISSION = 2;

    const PERMISSION_CREATE_USER = 3;//创建用户
    const PERMISSION_DISTRIBUTE = 4;//分配权限

    public function can($uid, $itemId)
    {
        $permission = $this->getPermissionByUser($uid);
        return in_array($itemId, $permission);
    }

    public function getPermissionByUser($uid)
    {
        $directPermission = $this->getDirectPermissionByUser($uid);
        $inheritedPermission = $this->getInheritedPermissionByUser($uid);
        return array_merge($directPermission, $inheritedPermission);
    }

    public function getDirectPermissionByUser($uid)
    {
        $permissionList = (new Query())->select('a.item_id')
            ->from('auth_item_assignment as a')
            ->leftJoin('auth_item as i', 'a.item_id=i.id')
            ->where([
                'i.type' => self::TYPE_PERMISSION,
                'a.uid' => $uid
            ])
            ->column();
        return $permissionList ? $permissionList : [];
    }

    public function getInheritedPermissionByUser($uid)
    {
        $query = (new Query())->select('item_id')
            ->from('auth_item_assignment')
            ->where(['uid' => (int)$uid]);
        $itemList = $query->column();
        if (empty($itemList)) {
            return [];
        }

        $result = [];
        //获取所有的父子关系，角色-权限
        $childrenList = $this->getChildrenList();
        foreach ($itemList as $item) {
            $this->getChildrenRecursive($item, $childrenList, $result);
        }
        $itemIdList = array_keys($result);
        if (empty($itemIdList)) {
            return [];
        }
        $permissionQuery = (new Query())->select('id')
            ->from('auth_item')
            ->where([
                'type' => self::TYPE_PERMISSION,
                'id' => $itemIdList
            ])
            ->column();
        if (empty($permissionQuery)) {
            return [];
        }
        return $permissionQuery;
    }

    public function getChildrenList()
    {
        $query = (new Query())->from('auth_item_child')
            ->all();
        if (empty($query)) {
            return [];
        }
        $parents = [];
        foreach ($query as $item) {
            $parents[$item['parent']][] = $item['child'];
        }
        return $parents;
    }

    public function getChildrenRecursive($itemId, $childrenList, &$result)
    {
        if (isset($childrenList[$itemId]) && $childrenList[$itemId]) {
            foreach ($childrenList[$itemId] as $child) {
                $result[$child] = true;
                $this->getChildrenRecursive($child, $childrenList, $result);
            }
        }
    }
}