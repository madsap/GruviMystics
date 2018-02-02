<?php

namespace app\models;

use Yii;

/**
 * This is the model class for table "md_user_credit_card".
 *
 * @property integer $id
 * @property integer $userId
 * @property string $token
 * @property integer $last4
 * @property string $expiration
 * @property string $status
 * @property string $createAt
 *
 * @property User $user
 */
class UserCreditCard extends \yii\db\ActiveRecord
{
    const STATUS_ACTIVE = "active";
    const STATUS_REMOVED = "removed";
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'md_user_credit_card';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['userId', 'last4'], 'integer'],
            [['expiration', 'createAt'], 'safe'],
            [['status'], 'string'],
            [['token'], 'string', 'max' => 128],
            [['userId'], 'exist', 'skipOnError' => true, 'targetClass' => User::className(), 'targetAttribute' => ['userId' => 'id']],
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'userId' => 'User ID',
            'token' => 'Token',
            'last4' => 'Last4',
            'expiration' => 'Expiration',
            'status' => 'Status',
            'createAt' => 'Create At',
        ];
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getUser()
    {
        return $this->hasOne(User::className(), ['id' => 'userId']);
    }
    
    
    /**
     * @return \yii\db\ActiveQuery
     */
    public static function getUserCreditCards($userId)
    {
        return self::findAll(['userId' => $userId, 'status' => self::STATUS_ACTIVE]);
    }
    
    /**
     * @return \yii\db\ActiveQuery
     */
    public static function getUserDefaultCreditCard($userId)
    {
        //TODO: add default = 1
        return self::findOne(['userId' => $userId, 'status' => self::STATUS_ACTIVE]);
    }
}
