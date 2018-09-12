<?php
/**
 * Created by PhpStorm.
 * User: watson.zeng
 * Date: 2018/9/10
 * Time: 19:24
 */

namespace app\models;


use Yii;
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

    /**
     * addItem
     * @description:
     * @param $item
     * @param $type
     * @return bool|int
     * @throws \yii\db\Exception
     * @author watson.zeng
     * @time 2018-09-11 19:43
     */
    public function addItem($item, $type)
    {
        if (empty($item) || empty($type)) {
            return false;
        }
        $param = [
            'item_name' => $item,
            'type' => $type
        ];
        $res = Yii::$app->db->createCommand()
            ->insert('auth_item', $param)
            ->execute();
        return $res;
    }

    public function getChildRoles($roleId)
    {
        $result = [];
        $this->getChildrenRecursive($roleId, $this->getChildrenList(), $result);
        $roleList = array_keys($result);
        return $roleList;
    }

    public function getPermissionByRole($roleId)
    {
        if (empty($roleId)) {
            return [];
        }
        $childrenList = $this->getChildrenList();
        if (empty($childrenList)) {
            return [];
        }
        $result = [];
        $this->getChildrenRecursive($roleId, $childrenList, $result);
        $permissions = (new Query())->select('id')
            ->from('auth_item')
            ->where(['id' => array_keys($result)])
            ->column();
        return $permissions ? $permissions : [];
    }

    /**
     * addAssignment
     * @description:
     * @param $uid
     * @param $itemId
     * @return int
     * @throws \yii\db\Exception
     * @author watson.zeng
     * @time 2018-09-11 20:07
     */
    public function addAssignment($uid, $itemId)
    {
        if (empty($uid) || empty($itemId)) {
            return false;
        }
        $param = [
            'uid' => intval($uid),
            'item_id' => intval($itemId)
        ];
        $res = Yii::$app->db->createCommand()
            ->insert('auth_item_assignment', $param)
            ->execute();
        return $res;
    }

    /**
     * addChild
     * @description:
     * @param $parent
     * @param $child
     * @return int
     * @throws \yii\db\Exception
     * @author watson.zeng
     * @time 2018-09-11 20:09
     */
    public function addChild($parent, $child)
    {
        if (empty($parent) || empty($child)) {
            return false;
        }
        $param = [
            'parent' => intval($parent),
            'child' => intval($child)
        ];
        $res = Yii::$app->db->createCommand()
            ->insert('auth_item_child', $param)
            ->execute();
        return $res;

    }

    public function getRoleByUser($uid)
    {
        $directRole = $this->getDirectRoleByUser($uid);
        $inheritedRole = $this->getInheritedRoleByUser($uid);
        return array_merge($directRole, $inheritedRole);
    }

    public function getDirectRoleByUser($uid)
    {
        $roleList = (new Query())->select('a.item_id')
            ->from('auth_item_assignment as a')
            ->leftJoin('auth_item as i', 'a.item_id=i.id')
            ->where([
                'i.type' => self::TYPE_ROLE,
                'a.uid' => $uid
            ])
            ->column();
        return $roleList ? $roleList : [];
    }

    public function getInheritedRoleByUser($uid)
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
        $roleQuery = (new Query())->select('id')
            ->from('auth_item')
            ->where([
                'type' => self::TYPE_ROLE,
                'id' => $itemIdList
            ])
            ->column();
        if (empty($roleQuery)) {
            return [];
        }
        return $roleQuery;
    }
}