<?php
namespace app\models;

use yii\mongodb\ActiveRecord;
use Yii;

class User extends ActiveRecord
{
    public static function collectionName()
    {
        return 'user';
    }

    public function attributes()
    {
        return ['_id', 'login', 'password', 'first_name', 'last_name', 'email', 'registration_date'];
    }

    public function rules()
    {
        return [
            [['login', 'password', 'first_name', 'last_name', 'email'], 'required'],
            ['login', 'unique'],
            ['login', 'string', 'min' => 4],
            ['password', 'match', 'pattern' => '/^(?=.*[A-Za-z])(?=.*\d)(?=.*[_\-,.])[A-Za-z\d_\-,.]{6,}$/'],
            ['first_name', 'match', 'pattern' => '/^[A-Z][a-z]*$/'],
            ['last_name', 'match', 'pattern' => '/^[A-Z][a-z]*$/'],
            ['email', 'email'],
            ['email', 'unique'],
            ['registration_date', 'default', 'value' => new \MongoDB\BSON\UTCDateTime()],
        ];
    }

    public function init()
    {
        parent::init();
        $this->ensureIndexes();
    }

    public function ensureIndexes()
    {
        $collection = static::getCollection();
        $collection->createIndex(['login' => 1], ['unique' => true]);
        $collection->createIndex(['email' => 1], ['unique' => true]);
    }

    public function fields()
    {
        $fields = parent::fields();
        $fields['registration_date'] = function ($model) {
            if ($model->registration_date instanceof \MongoDB\BSON\UTCDateTime) {
                return Yii::$app->formatter->asDatetime($model->registration_date->toDateTime(), 'php:d-m-Y H:i');
            }
            return $model->registration_date; 
        };
        unset($fields['password']);
        return $fields;
    }
    
    public function getTasks()
    {
        return $this->hasMany(Task::class, ['user_id' => '_id']);
    }
}