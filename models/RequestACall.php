<?php

namespace app\models;

use Yii;

/**
 * This is the model class for table "md_request_a_call".
 *
 * @property integer $id
 * @property integer $customerId
 * @property integer $readerId
 * @property string $phone
 * @property string $status
 * @property string $createAt
 *
 * @property User $customer
 * @property User $reader
 */
class RequestACall extends \yii\db\ActiveRecord
{
    const STATUS_NEW                  = 'New';
    const STATUS_ACCEPTED             = 'Accepted';
    const STATUS_DECLINED             = 'Declined';
    
    public static $arrayStatus	= [
        self::STATUS_NEW    => self::STATUS_NEW,
        self::STATUS_ACCEPTED     => self::STATUS_ACCEPTED,
        self::STATUS_DECLINED     => self::STATUS_DECLINED
    ];
    
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'md_request_a_call';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['customerId', 'readerId'], 'integer'],
            [['status'], 'in', 'range' => self::$arrayStatus],
            [['createAt'], 'safe'],
            [['phone'], 'string', 'max' => 32],
            [['phone', 'customerId', 'readerId'], 'required'],
            [['customerId'], 'exist', 'skipOnError' => false, 'targetClass' => User::className(), 'targetAttribute' => ['customerId' => 'id']],
            [['readerId'], 'exist', 'skipOnError' => false, 'targetClass' => User::className(), 'targetAttribute' => ['readerId' => 'id']],
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'customerId' => 'Customer ID',
            'readerId' => 'Reader ID',
            'phone' => 'Phone',
            'status' => 'Status',
            'createAt' => 'Create At',
        ];
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getCustomer()
    {
        return $this->hasOne(User::className(), ['id' => 'customerId']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getReader()
    {
        return $this->hasOne(User::className(), ['id' => 'readerId']);
    }
}
