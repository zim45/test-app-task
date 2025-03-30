<?php
namespace app\controllers;

use yii\rest\ActiveController;
use yii\data\ActiveDataProvider;
use yii\web\NotFoundHttpException;
use yii\web\ServerErrorHttpException;
use app\models\Task;
use MongoDB\BSON\ObjectId;

class TaskController extends ActiveController
{
    public $modelClass = Task::class;

    public function actions()
    {
        $actions = parent::actions();
        $actions['index']['prepareDataProvider'] = [$this, 'prepareDataProvider'];
        unset($actions['delete']);

        return $actions;
    }

    public function actionTaskCreate()
    {
        $task = new Task();
        $task->attributes = \Yii::$app->request->post();
        $task->scenario = 'task-create';

        if ($task->validate() && $task->save()) {
            \Yii::$app->response->statusCode = 201; 
            return $task;  
        } else {
            throw new ServerErrorHttpException('Failed to create the task for unknown reasons.');
        }
    }

    public function beforeAction($action)
    {
        if ($action->id === 'task-create') {

            $userId = \Yii::$app->request->get('id'); 
            if ($userId) {
                \Yii::$app->request->setBodyParams(array_merge(\Yii::$app->request->getBodyParams(), ['user_id' => $userId]));
            }
        }
        
        return parent::beforeAction($action);
    }

    public function actionViewUserTask($id, $task_id)
    {    
        $taskId = new ObjectId($task_id);
        $userId = (string)$id;

        $user = \app\models\User::findOne($userId);
    
        if (!$user) {
            \Yii::error("User with ID $id not found.");
            throw new NotFoundHttpException("User not found with ID: $id.");
        }

        $task = Task::findOne(['_id' => $taskId, 'user_id' => $userId]);
    
        if (!$task) {
            \Yii::error("Task with ID $task_id not found for user with ID: $id.");
            throw new NotFoundHttpException("Task with ID: $task_id not found for user with ID: $id.");
        }

        return $task;
    }

    public function actionUpdateUserTask($id, $task_id)
    {
        $task = Task::findOne($task_id);
    
        if (!$task) {
            \Yii::error("Task with ID $task_id not found for user with ID: $id.");
            throw new NotFoundHttpException("Task with ID: $task_id not found for user with ID: $id.");
        }
    
        $task->load(\Yii::$app->request->getBodyParams(), '');
   
        if (!$task->save()) {
            \Yii::error("Failed to update task with ID: $task_id for user with ID: $id. Errors: " . json_encode($task->errors));
            throw new \yii\web\ServerErrorHttpException('Failed to update task due to a server error.');
        }

    
        return $task;
    }

    public function prepareDataProvider()
    {
        $userId = \Yii::$app->request->get('id');
        $query = Task::find()->where(['user_id' => $userId]);
        
        $request = \Yii::$app->request;
        
        $pageParam = 'page';
        $pageSize = 10;
        $currentPage = max(1, (int) $request->get($pageParam, 1)); 
        
        $dataProvider = new ActiveDataProvider([
            'query' => $query,
            'pagination' => [
                'pageSize' => $pageSize,
                'page' => $currentPage - 1, // zero-indexed page
                'pageParam' => $pageParam,
            ],
            'sort' => ['attributes' => ['title', 'status']],
        ]);
        
        $pagination = $dataProvider->getPagination();
        $totalCount = $dataProvider->getTotalCount();
        
        $baseUrl = $request->hostInfo . '/' . $request->pathInfo; 
        
        $queryParams = $request->getQueryParams();
        unset($queryParams[$pageParam], $queryParams['id']); 
        
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

    public function actionDelete($id, $task_id)
    {
        $task = $this->findModel($task_id);
        if ($task->status !== Task::STATUS_NEW) {
            throw new \yii\web\ForbiddenHttpException('Only tasks with status New can be deleted.');
        }
        $task->delete();
        \Yii::$app->response->statusCode = 204;
    }

    public function actionDeleteAll($id)
    {
        Task::deleteAll(['user_id' => $id, 'status' => Task::STATUS_NEW]);
        \Yii::$app->response->statusCode = 204;
    }

    public function actionStats($id)
    {
        $mongodb = \Yii::$app->get('mongodb');
        $pipeline = [
            [
                '$match' => ['user_id' => $id],
            ],
            [
                '$group' => [
                    '_id' => '$status',
                    'count' => ['$sum' => 1], 
                ]
            ],
            [
                '$sort' => ['_id' => 1], 
            ]
        ];
    
        $stats = $mongodb->getCollection('task')->aggregate($pipeline);
    
        return array_map(function($item) {
            return [
                'status' => $item['_id'],
                'count' => $item['count']
            ];
        }, $stats);
    }

    public function actionGlobalStats()
    {
        $mongodb = \Yii::$app->mongodb;
        $pipeline = [
            [
                '$group' => [
                    '_id' => '$status',
                    'count' => ['$sum' => 1]
                ]
            ],
            [
                '$sort' => ['_id' => 1]
            ]
        ];
    
        $stats = $mongodb->getCollection('task')->aggregate($pipeline);
    
        return array_map(function ($item) {
            return [
                'status' => $item['_id'],
                'count' => $item['count']
            ];
        }, $stats);
    }

    protected function findModel($task_id)
    {
        $model = Task::findOne($task_id);
        if (!$model) {
            throw new NotFoundHttpException("Task not found.");
        }
        return $model;
    }
}
