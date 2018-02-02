<?php

namespace app\models;

use Yii;

/**
 * This is the model class for table "md_gruvi_bucks".
 *
 * @property integer $id
 * @property integer $userId
 * @property integer $creditCardId
 * @property string $stripeTransaction
 * @property string $amount
 * @property string $log
 * @property string $status
 * @property string $createAt
 *
 * @property UserCreditCard $creditCard
 * @property User $user
 */
class GruviBucks extends \yii\db\ActiveRecord
{
    const STATUS_APPROVED = 'approved';
    const STATUS_DECLINED = 'declined';
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'md_gruvi_bucks';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['userId'], 'required'],
            [['userId', 'creditCardId'], 'integer'],
            [['amount'], 'number'],
            [['log', 'status'], 'string'],
            [['createAt'], 'safe'],
            [['stripeTransaction'], 'string', 'max' => 64],
            [['creditCardId'], 'exist', 'skipOnError' => true, 'targetClass' => UserCreditCard::className(), 'targetAttribute' => ['creditCardId' => 'id']],
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
            'creditCardId' => 'Credit Card ID',
            'stripeTransaction' => 'Stripe Transaction',
            'paypalTransaction' => 'Paypal Transaction',
            'amount' => 'Amount',
            'log' => 'Log',
            'status' => 'Status',
            'createAt' => 'Create At',
        ];
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getCreditCard()
    {
        return $this->hasOne(UserCreditCard::className(), ['id' => 'creditCardId']);
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
    public static function getUserBalance($userId)
    {
        
        $sql = "SELECT SUM(`amount`) as gruvi_bucks FROM `md_gruvi_bucks` WHERE `userId` = :userId AND `status` = :status";
        $connection = Yii::$app->getDb();
        $command = $connection->createCommand($sql, [':userId' => $userId, ':status' => self::STATUS_APPROVED]);
        $result = $command->queryOne();
        $so = (!empty($result['gruvi_bucks']))?$result['gruvi_bucks']:"0.00";
        return $so;
    }
    
    /**
     * @return \yii\db\ActiveQuery
     */
    public function addGruviBucks()
    {
        $this->save(false);
    }
    
    /**
     * @return \yii\db\ActiveQuery
     */
    public function charge($even_if_negative = false, $charge_as_much_as_possible = false)
    {
        if($this->amount > 0)$this->amount = 0 - $this->amount;
        
        $incomplete_charge = false;
        if(!$even_if_negative){
            $gruvi_bucks = (float)GruviBucks::getUserBalance($this->userId);
            if($gruvi_bucks <= 0)return false;
            
            if(abs($this->amount) > $gruvi_bucks){
                if(!$charge_as_much_as_possible)return false;
                $this->amount = 0 - $gruvi_bucks;
                $incomplete_charge = true;
            }
        }
        
        
        $so = $this->save(false);
        if($incomplete_charge)return false;
        return $so;
    }
    
    /**
     * @return \yii\db\ActiveQuery
     */
    public static function addBonusBucks($userId)
    {
        $bucks = new GruviBucks();
        $bucks->userId = $userId;
        $bucks->amount = 10.00;
        $bucks->log = "Registration Bonus";
        $bucks->save(false);
    }
    
    
}
