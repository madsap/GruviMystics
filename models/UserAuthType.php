<?php

namespace app\models;

use \Yii;
use \yii\db\ActiveRecord;
use \yii\helpers\VarDumper;

/**
 * This is the model class for table "st_user_auth_type".
 *
 * @property integer $id
 * @property string $registrationType
 * @property integer $userId
 * @property string $socialNetworkId
 * @property string $email
 * @property string $password
 * @property string $username
 * @property string $lastUpdatedTime
 * @property string $createdAt
 */
class UserAuthType extends ActiveRecord
{
        
    /**
     * @param $attributes
     * @param $userId
     * @param $registrationType
     * @param $socialNetworkId
     */
    public static function create($attributes, $userId, $registrationType, $socialNetworkId,$apiKey = "")
    {
        $userAuthType = new UserAuthType;
        $userAuthType->setAttributes($attributes);
        $userAuthType->userId = $userId;
        $userAuthType->registrationType = $registrationType;
        $userAuthType->socialNetworkId = $socialNetworkId;
        $userAuthType->apiKey = $apiKey;
        if(!$userAuthType->save()) {
            Yii::info(VarDumper::dumpAsString($userAuthType->getErrors()), 'show');
        }
    }

    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return '{{%user_auth_type}}';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['registrationType'], 'required'],
            [['registrationType'], 'in', 'range' => User::$arraySocials],
            [['userId'], 'integer'],
            [['username'], 'default', 'value' => ''],
            [['lastUpdatedTime', 'createdAt'], 'safe'],
            [['socialNetworkId', 'email', 'username'], 'string', 'max' => 150],
            [['password'], 'string', 'max' => 32],
            [['socialNetworkId'], 'unique', 'targetAttribute' => ['registrationType', 'socialNetworkId']],
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'id'                => 'ID',
            'registrationType'  => 'Registration Type',
            'userId'            => 'User ID',
            'socialNetworkId'   => 'Social Network ID',
            'email'             => 'Email',
            'password'          => 'Password',
            'username'          => 'User Name',
            'lastUpdatedTime'   => 'Last Updated Time',
            'createdAt'         => 'Created At',
        ];
    }
}