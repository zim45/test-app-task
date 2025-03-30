<?php
return [
    'class' => 'yii\mongodb\Connection',
    'dsn' => 'mongodb://mongo:27017/taskmanager?readPreference=primaryPreferred',
    'options' => [
        'connectTimeoutMS' => 1000,
        'socketTimeoutMS' => 1000,
    ]
];