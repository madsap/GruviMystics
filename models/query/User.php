<?php

namespace app\models\query;

use \yii\db\ActiveQuery;
use \app\models\User as UserModel;

/**
 * This is the ActiveQuery class for [[\app\models\User]].
 *
 * @see \app\models\User
 */
class User extends ActiveQuery
{
    /**
     * @param string $ApiKey
     * @param string $status
     * @return \app\models\User|array|null
     */
    public function findUserByApiKey($token, $status = UserModel::STATUS_ACTIVE)
    {
        $relationAttributes = ['apiKey' => $token];
        return $this->_findSocialId([], $relationAttributes, $status);
    }
    /**
     * @param string $token
     * @param string $status
     * @return \app\models\User|array|null
     */
    public function findTwitterId($token, $status = UserModel::STATUS_ACTIVE)
    {
        $relationAttributes = ['socialNetworkId' => $token, 'registrationType' => UserModel::SOCIAL_TWITTER];
        return $this->_findSocialId([], $relationAttributes, $status);
    }
    /**
     * @param string $token
     * @param string $status
     * @return \app\models\User|array|null
     */
    public function findFacebookId($token, $status = UserModel::STATUS_ACTIVE)
    {
        $relationAttributes = ['socialNetworkId' => $token, 'registrationType' => UserModel::SOCIAL_FACEBOOK];
        return $this->_findSocialId([], $relationAttributes, $status);
    }

    /**
     * @param string $email
     * @param string $status
     * @return \app\models\User|array|null
     */
    public function findEmailId($email, $status = UserModel::STATUS_ACTIVE)
    {
        $relationAttributes = ['socialNetworkId' => $email, 'registrationType' => UserModel::SOCIAL_EMAIL];
        return $this->_findSocialId([], $relationAttributes, $status);
    }
    public function findByUserId($id, $status = UserModel::STATUS_ACTIVE)
    {
        $relationAttributes = ['userId' => $id];
        return $this->_findSocialId([], $relationAttributes, $status);
    }

    /**
     * @inheritdoc
     * @return \app\models\User[]|array
     */
    public function all($db = null)
    {
        return parent::all($db);
    }

    /**
     * @inheritdoc
     * @return \app\models\User|array|null
     */
    public function one($db = null)
    {
        return parent::one($db);
    }

    /**
     * @param $attributes
     * @param $relationAttributes
     * @param $status
     * @return UserModel|array|null
     */
    private function _findSocialId($attributes, $relationAttributes, $status)
    {
        if(null !== $status) {
            $attributes['status'] = $status;
        }

        $relationAlias = 'reg';

        if(!empty($relationAttributes)) {
            foreach($relationAttributes as $key => $value) {
                $relationAttributes[$relationAlias . '.' . $key] = $value;
                unset($relationAttributes[$key]);
            }
        }

        return $this->where($attributes)
            ->joinWith(['authType' => function($query) use ($relationAttributes, $relationAlias) {
                /** @var \yii\db\ActiveQuery $query */
                return $query->andOnCondition($relationAttributes)->alias($relationAlias);
            }], true, 'JOIN')
            ->one();
    }
}