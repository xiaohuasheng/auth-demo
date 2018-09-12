<?php

namespace app\controllers;

use app\models\Auth;
use app\models\AuthItem;
use app\models\authItemAssignment;
use app\models\SignupForm;
use app\models\User;
use app\models\UserSearch;
use Yii;
use yii\filters\AccessControl;
use yii\filters\VerbFilter;
use yii\web\Controller;
use yii\web\ForbiddenHttpException;
use yii\web\NotFoundHttpException;

/**
 * UserController implements the CRUD actions for User model.
 */
class UserController extends Controller
{
    /**
     * @inheritdoc
     */
    public function behaviors()
    {
        return [
            'access' => [
                'class' => AccessControl::className(),
                'rules' => [
                    [
                        'actions' => ['login', 'error'],
                        'allow' => true,
                    ],
                    [
                        'actions' => ['logout', 'index', 'signup', 'privilege'],
                        'allow' => true,
                        'roles' => ['@'],
                    ],
                ],
            ],
            'verbs' => [
                'class' => VerbFilter::className(),
                'actions' => [
                    'delete' => ['POST'],
                ],
            ],
        ];
    }

    /**
     * Lists all User models.
     * @return mixed
     */
    public function actionIndex()
    {
        $searchModel = new UserSearch();
        $dataProvider = $searchModel->search(Yii::$app->request->queryParams);

        return $this->render('index', [
            'searchModel' => $searchModel,
            'dataProvider' => $dataProvider,
        ]);
    }

    /**
     * Displays a single User model.
     * @param integer $id
     * @return mixed
     * @throws NotFoundHttpException if the model cannot be found
     */
    public function actionView($id)
    {
        return $this->render('view', [
            'model' => $this->findModel($id),
        ]);
    }

    /**
     * Creates a new User model.
     * If creation is successful, the browser will be redirected to the 'view' page.
     * @return mixed
     */
    public function actionCreate()
    {
        $model = new User();

        if ($model->load(Yii::$app->request->post()) && $model->save()) {
            return $this->redirect(['view', 'id' => $model->id]);
        }

        return $this->render('create', [
            'model' => $model,
        ]);
    }

    /**
     * Updates an existing User model.
     * If update is successful, the browser will be redirected to the 'view' page.
     * @param integer $id
     * @return mixed
     * @throws NotFoundHttpException if the model cannot be found
     */
    public function actionUpdate($id)
    {
        $model = $this->findModel($id);

        if ($model->load(Yii::$app->request->post()) && $model->save()) {
            return $this->redirect(['view', 'id' => $model->id]);
        }

        return $this->render('update', [
            'model' => $model,
        ]);
    }

    /**
     * Deletes an existing User model.
     * If deletion is successful, the browser will be redirected to the 'index' page.
     * @param integer $id
     * @return mixed
     * @throws NotFoundHttpException if the model cannot be found
     */
    public function actionDelete($id)
    {
        $this->findModel($id)->delete();

        return $this->redirect(['index']);
    }

    /**
     * Finds the User model based on its primary key value.
     * If the model is not found, a 404 HTTP exception will be thrown.
     * @param integer $id
     * @return User the loaded model
     * @throws NotFoundHttpException if the model cannot be found
     */
    protected function findModel($id)
    {
        if (($model = User::findOne($id)) !== null) {
            return $model;
        }

        throw new NotFoundHttpException('The requested page does not exist.');
    }

    /**
     * actionPrivilege
     * @description:
     * @param $id
     * @return string|\yii\web\Response
     * @throws ForbiddenHttpException
     * @author watson.zeng
     * @time 2018-09-10 20:20
     */
    public function actionPrivilege($id)
    {
        $loginUid = Yii::$app->user->id;
        $can = (new Auth())->can($loginUid, Auth::PERMISSION_DISTRIBUTE);
        if (!$can) {
            throw new ForbiddenHttpException('对不起，你没有进行该操作的权限。');
        }
        if (empty($id)) {
            throwException(new NotFoundHttpException('请选择用户'));
        }
        //1、查所有角色
        $allPrivileges = AuthItem::find()->select(['id', 'item_name'])
            ->where(['type' => Auth::TYPE_ROLE])
            ->orderBy('id')
            ->all();
        $allPrivilegesArray = [];
        if (!empty($allPrivileges)) {
            foreach ($allPrivileges as $item) {
                $allPrivilegesArray[$item->id] = $item->item_name;
            }
        }

        //2、查当前用户角色
        $authItemAssignmentArray = (new Auth())->getRoleByUser($id);

        //3、更新分配表
        if (Yii::$app->request->post("newPri")) {
            $newPri = Yii::$app->request->post("newPri");
            if (!empty($newPri)) {
                authItemAssignment::deleteAll('uid=:id', [':id' => $id]);

                foreach ($newPri as $item) {
                    $aPri = new authItemAssignment();
                    $aPri->uid = $id;
                    $aPri->item_id = $item;
                    $aPri->save();
                }
                return $this->redirect(['index']);
            }
        }

        //4、渲染表单
        return $this->render('privilege', [
            'id' => $id,
            'authItemAssignmentArray' => $authItemAssignmentArray,
            'allPrivilegesArray' => $allPrivilegesArray,
        ]);

    }

    /**
     * Signs user up.
     *
     * @return mixed
     * @throws ForbiddenHttpException
     */
    public function actionSignup()
    {
        $id = Yii::$app->user->id;
        $can = (new Auth())->can($id, Auth::PERMISSION_CREATE_USER);
        if (!$can) {
            throw new ForbiddenHttpException('对不起，你没有进行该操作的权限。');
        }
        $model = new SignupForm();
        if ($model->load(Yii::$app->request->post())) {

            if ($user = $model->signup()) {
                return $this->goHome();
            }
        }

        return $this->render('signup', [
            'model' => $model,
        ]);
    }
}
