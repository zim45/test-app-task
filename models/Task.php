<?php
namespace app\models;

use yii\mongodb\ActiveRecord;
use Yii;

class Task extends ActiveRecord
{
    const STATUS_NEW = 0;
    const STATUS_IN_PROGRESS = 1;
    const STATUS_FINISHED = 2;
    const STATUS_FAILED = 3;

    public static function collectionName()
    {
        return 'task';
    }

    public function attributes() 
    {
        return ['_id', 'title', 'description', 'status', 'start_date', 'user_id'];
    }

    public function rules()
    {
        return [
            [['title', 'description', 'status', 'start_date'], 'required'],
            ['title', 'string', 'min' => 1],
            ['description', 'string', 'min' => 1],
            ['status', 'in', 'range' => array_keys(self::getStatusLabels())],
            ['start_date', 'date', 'format' => 'php:d-m-Y H:i'], 
            ['user_id', 'required'], 
            ['status', 'validateStatusOnCreate', 'on' => 'task-create'],
        ];
    }

    public static function getStatusLabels()
    {
        return [
            self::STATUS_NEW => 'New',
            self::STATUS_IN_PROGRESS => 'In Progress',
            self::STATUS_FINISHED => 'Finished',
            self::STATUS_FAILED => 'Failed',
        ];
    }

    public function getStatusLabel()
    {
        $labels = self::getStatusLabels();
        return $labels[$this->status] ?? 'Unknown';
    }

    public function validateStatusOnCreate($attribute, $params)
    {
        if ($this->isNewRecord && $this->status !== self::STATUS_NEW) {
            $this->addError($attribute, 'New tasks must have status "New".');
        }
    }

    public function beforeSave($insert)
    {
        if (!parent::beforeSave($insert)) {
            return false;
        }

        if (!$insert) {
            $oldStatus = self::findOne($this->_id)->status;  
            $newStatus = $this->status;  
    
            if ($oldStatus === $newStatus) {
                return true;
            }
     
            $allowedTransitions = [
                self::STATUS_NEW => [self::STATUS_IN_PROGRESS],
                self::STATUS_IN_PROGRESS => [self::STATUS_FINISHED, self::STATUS_FAILED],
            ];
    
            if (isset($allowedTransitions[$oldStatus]) && in_array($newStatus, $allowedTransitions[$oldStatus])) {
                return true;  
            } elseif (!isset($allowedTransitions[$oldStatus])) {
                return true;  
            } else {
                $this->addError('status', 'Invalid status transition.');
                return false;  
            }
        }
    
    
        return true;
    }

    public function fields()
    {
        $fields = parent::fields();
    
        $fields['start_date'] = function ($model) {
            if (is_string($model->start_date)) {
                $model->start_date = new \DateTime($model->start_date); 
            }
            return Yii::$app->formatter->asDatetime($model->start_date, 'php:d-m-Y H:i');
        };
        
        $fields['status_label'] = function ($model) {
            return $model->getStatusLabel();
        };
        
        return $fields;
    }

    public function getUser()
    {
        return $this->hasOne(User::class, ['_id' => 'user_id']);
    }
}
