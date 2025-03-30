<?php

namespace app\controllers;

use yii\rest\ActiveController;
use yii\data\ActiveDataProvider;
use yii\web\NotFoundHttpException;
use yii\web\ServerErrorHttpException;
use app\models\User;

class UserController extends ActiveController
{
    public $modelClass = User::class;

    public function actions()
    {
        $actions = parent::actions();
        $actions['index']['prepareDataProvider'] = [$this, 'prepareDataProvider'];
        return $actions;
    }

    public function prepareDataProvider()
    {
        $query = User::find();
        $request = \Yii::$app->request;
        

        $pageParam = 'page';
        $pageSize = 10;
        $currentPage = max(1, (int) $request->get($pageParam, 1)); 
        
        $dataProvider = new ActiveDataProvider([
            'query' => $query,
            'pagination' => [
                'pageSize' => $pageSize,
                'page' => $currentPage - 1, 
                'pageParam' => $pageParam,
            ],
            'sort' => ['attributes' => ['first_name', 'last_name', 'email']],
        ]);
        
        $pagination = $dataProvider->getPagination();
        $totalCount = $dataProvider->getTotalCount();
        
    
        $baseUrl = $request->hostInfo . '/'.$request->pathInfo; 

        $queryParams = $request->getQueryParams();
        unset($queryParams[$pageParam]); 
        
        $queryString = http_build_query($queryParams);
        $queryString = $queryString ? "&{$queryString}" : ''; 
        
        $previousPage = $currentPage > 1 
            ? "{$baseUrl}?{$pageParam}=" . ($currentPage - 1) . $queryString
            : null;
    
        $nextPage = ($currentPage * $pageSize) < $totalCount 
            ? "{$baseUrl}?{$pageParam}=" . ($currentPage + 1) . $queryString
            : null;
        
        return [
            'items' => $dataProvider->getModels(),
            'paging' => [
                'previous' => $previousPage,
                'next' => $nextPage,
            ],
        ];
    }

    public function actionCreate()
    {
        $user = new User();
        $user->attributes = \Yii::$app->request->post();

        if ($user->validate() && $user->save()) {
            \Yii::$app->response->statusCode = 201; 
            return $user;  
        } else {
            throw new ServerErrorHttpException('Failed to create the user for unknown reasons.');
        }
    }

    public function actionView($id)
    {
        $user = $this->findModel($id);
        return $user;
    }

    public function actionUpdate($id)
    {
        $user = $this->findModel($id);
        $user->attributes = \Yii::$app->request->put();

        if ($user->validate() && $user->save()) {
            return $user;
        } else {
            throw new ServerErrorHttpException('Failed to update the user for unknown reasons.');
        }
    }

    public function actionDelete($id)
    {
        $user = $this->findModel($id);
        $user->delete();
        \Yii::$app->response->statusCode = 204; 
    }

    protected function findModel($id)
    {
        $model = User::findOne($id);
        if (!$model) {
            throw new NotFoundHttpException("User not found.");
        }
        return $model;
    }
}
