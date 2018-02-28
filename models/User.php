<?php

namespace app\models;

use \Yii;
use \yii\db\ActiveRecord;
use \yii\db\Expression;
use \yii\web\IdentityInterface;
use \app\components\StringHelper;
use \app\models\query\User as UserQuery;
use \app\models\GruviBucks;
use \app\models\UserCreditCard;
use app\models\search\Message as MessageSearch;

/**
 * This is the model class for table "{{%user}}".
 *
 * @property integer        $id
 * @property string         $role
 * @property string         $registrationType
 * @property string         $email
 * @property string         $firstName
 * @property string         $lastName
 * @property string         $dob
 * @property string         $status
 * @property string         $createAt
 *
 * @property string         $authKey
 * @property string         $accessToken
 * @property string         $social
 * @property string         $photo
 * @property string         $files
 * @property string         $password
 * @property string         $confirmPassword
 * @property integer        $myId
 * @property string         $socialCredentials
 * @property boolean        $rememberMe
 *
 * @property UserAuthType[] $authType
 * @property array          $_updateColumn
 */
class User extends ActiveRecord implements IdentityInterface {

    const MAIN_CATEGORY_LOGO = 'logo';
    const TABLE_NAME = 'User';
    const FILENAME = 'photo';
    const ROLE_USER = 'user';
    const ROLE_READER = 'reader';
    const ROLE_ADMIN = 'admin';
    const SOCIAL_FACEBOOK = 'facebook';
    const SOCIAL_TWITTER = 'twitter';
    const SOCIAL_EMAIL = 'email';
    const STATUS_ACTIVE = 'active';
    const STATUS_INACTIVE = 'inactive';
    const STATUS_DELETED = 'deleted';
    const STATUS_BANNED = 'banned';
    const ACTIVITY_OFFLINE = 'Offline';
    const ACTIVITY_ONLINE = 'Online';
    const ACTIVITY_SESSION = 'Session';
    const ACTIVITY_DISABLED = 'Disabled';

    public $authKey;
    public $accessToken;
    public $social = '';
    public $photo;
    public $files;
    public $password;
    public $confirmPassword;
    public $myId;
    public $socialCredentials;
    public $rememberMe;
    public $phone;
    public $testPhone;
    public $specialties;
    private $_hashingPassword = false;
    private $_updateColumn = [];
    private $_gruviBucksAmount;
    private $_profilePicUrl = [];
    public static $arrayRoles = [
        self::ROLE_USER => self::ROLE_USER,
        self::ROLE_READER => self::ROLE_READER,
        self::ROLE_ADMIN => self::ROLE_ADMIN,
    ];
    public static $arrayActivities = [
        self::ACTIVITY_OFFLINE => self::ACTIVITY_OFFLINE,
        self::ACTIVITY_ONLINE => self::ACTIVITY_ONLINE,
        self::ACTIVITY_SESSION => self::ACTIVITY_SESSION,
        self::ACTIVITY_DISABLED => self::ACTIVITY_DISABLED,
    ];
    public static $arraySocials = [
        self::SOCIAL_EMAIL => self::SOCIAL_EMAIL,
        self::SOCIAL_FACEBOOK => self::SOCIAL_FACEBOOK,
        self::SOCIAL_TWITTER => self::SOCIAL_TWITTER
    ];
    public static $arrayStatuses = [
        self::STATUS_ACTIVE => self::STATUS_ACTIVE,
        self::STATUS_INACTIVE => self::STATUS_INACTIVE,
        self::STATUS_DELETED => self::STATUS_DELETED,
        self::STATUS_BANNED => self::STATUS_BANNED
    ];

    /**
     * @param       $users
     * @param null  $names
     * @param array $except
     * @return array
     */
    public function getAllPublicAttributes($users, $names = null, $except = []) {
        $result = [];

        /** @var User $user */
        foreach ($users as $user) {
            $user->myId = $this->myId;
            $result[] = $user->getPublicAttributes($names, $except);
        }

        return $result;
    }

    public function getNameForTwilio() {
        $hash = substr(md5($this->id . '_secret_xcode'), 0, 16);
        return 'u' . $this->id . '_' . $hash;
    }

    public function renderFullname() {
        $name = '';
        if ( !empty($this->firstName) ) {
            $name .= $this->firstName;
        } 
        if ( !empty($this->lastName) ) {
            $name .= $this->lastName;
        } 
        if ( empty($name) ) {
            $name = $this->renderDisplayName();
        }
        return $name;
    }

    public function renderDisplayName() {
        if ( !empty($this->displayname) ) {
            $name = $this->displayname;
        } else {
            $name = $this->firstName;
        }
        return $name;
    }

    /**
     * @param null  $names
     * @param array $except
     * @return array
     */
    public function getPublicAttributes($names = null, $except = []) {
        if (empty($except)) {
            $except = ['role', 'registrationType', 'createAt'];
        }

        $attributes = parent::getAttributes($names, $except);

        $attributes['phone'] = $this->getValue($this->authType, [0, 'socialNetworkId']);

        $attributes['ProfilePicUrl'] = $this->getProfilePicUrl();

        StringHelper::removeNull($attributes);

        return $attributes;
    }

    public function getProfilePicUrl($size = "middle") {

        if (empty($this->_profilePicUrl[$size])) {
            $attributes['photo'] = (new File)->getFilesByObject(self::TABLE_NAME, $this->id);

            if (!empty($attributes['photo'][0])) {
                $photo = [];
                foreach ($attributes['photo'][0] as $files) {
                    $photo[$files['type']] = $files['url'];
                }
                $this->_profilePicUrl = $photo;
            }
        }

        return (!empty($this->_profilePicUrl[$size])) ? $this->_profilePicUrl[$size] : "https://cdn3.iconfinder.com/data/icons/abstract-1/512/no_image-512.png";
    }

    /**
     * @inheritdoc
     */
    public static function findIdentity($id) {
        return static::findOne($id);
    }

    /**
     * @inheritdoc
     */
    public static function findIdentityByAccessToken($token, $type = null) {
        $user = self::find()->where(['token' => $token])->one();
        if ($user->accessToken === $token) {
            return $user;
        }

        return null;
    }

    /**
     * @inheritdoc
     */
    public function getId() {
        return $this->id;
    }

    /**
     * Finds user by email
     *
     * @param string $email
     * @return static|null
     */
    public static function findByEmail($email) {
        return static::findOne(['email' => $email, 'status' => self::STATUS_ACTIVE]);
    }

    /**
     * @inheritdoc
     */
    public function getAuthKey() {
        return $this->authKey;
    }

    /**
     * @inheritdoc
     */
    public function validateAuthKey($authKey) {
        return $this->authKey === $authKey;
    }

    /**
     * Validates password
     *
     * @param string $password password to validate
     * @return bool if password provided is valid for current user
     */
    public function validatePassword($password) {
        return $this->password === $password;
    }

    /**
     * @param string  $password
     * @param boolean $changeHashingPass
     * @return string
     */
    public function hashPassword($password = null, $changeHashingPass = true) {
        $password = $password == null ? Yii::$app->getSecurity()->generateRandomString() : $password;

        if (null !== $changeHashingPass) {
            $this->_hashingPassword = $changeHashingPass;
        }

        return md5(PASS_SALT . $password);
    }

    /**
     * @param bool $runValidation
     * @return false|int
     */
    public function updateInfo($runValidation = true) {
        $updateMail = $this->isAttributeChanged('email');

        if (true === $updateMail) {
            User::updateAll(
                    ['status' => self::STATUS_INACTIVE], ['id' => $this->id, 'status' => self::STATUS_ACTIVE]
            );
        }

        $result = $this->update($runValidation, $this->_updateColumn);

        return $result;
    }

    /**
     * @return bool
     */
    public function changeStatus() {
        $this->status = $this->status == self::STATUS_INACTIVE ? self::STATUS_ACTIVE : self::STATUS_INACTIVE;

        if ($this->update(true, ['status'])) {
            return true;
        } else {
            return false;
        }
    }

    /**
     * Generates password hash from password and sets it to the model
     * Original yii method
     * @param string $password
     */
    public function setPassword($password) {
        $this->_updatePassword($password);
    }

    /**
     * Alex custom method
     * @return int
     */
    public function setNewPassword() {
        return $this->_updatePassword($this->password);
    }

    /**
     * @param $socialInfo
     * @param $check
     * @return bool
     */
    public function create($socialInfo, $check = false, $isApi = false) {


        $user = new User();
        $user->setScenario('save');
        $user->role = $this->role;
        $user->attributes = $socialInfo;

        if ($this->social === self::SOCIAL_FACEBOOK) {
            $user->attributes = $this->getAttributes();
        }

        if ($this->social === self::SOCIAL_TWITTER) {
            $user->attributes = $this->getAttributes();
        }

        $user->registrationType = $this->social;
        $user->apiKey = $this->apiKey;
        $file = new File();
        $file->saveImage($user, User::FILENAME, $user->id, $user->id, User::MAIN_CATEGORY_LOGO, $this->files);
        if ($user->save()) {
            $file = new File();
            $file->saveImage(
                    $user, User::FILENAME, $user->id, $user->id, User::MAIN_CATEGORY_LOGO, $this->files, File::ACTION_SAVE
            );

            UserAuthType::create($socialInfo, $user->id, $this->social, $socialInfo['socialId'], $socialInfo['apiKey']);

            $userModel = User::find()->where(['id' => $user->id])->asArray()->one();

            $this->setAttributes($userModel);
            $this->id = $userModel['id'];

            if (false === $check && $isApi === false) {
                $this->_login();
            }
            if ($isApi == true) {
                return $user;
            }
            return true;
        } else {
            $this->addErrors($user->getErrors());
            return false;
        }
    }

    /**
     * @param bool  $registration
     * @param bool  $check
     * @param array $socialInfo
     * @return bool
     */
    public function socialLogin($registration = true, $check = false, $socialInfo = [], $isApi = false, $method = 'Login') {
        if (empty($socialInfo['socialId'])) {
            $socialInfo = $this->getSocialInfo();
        }

        if (isset($socialInfo['socialId'])) {
            /** @var User $user */
            $user = $this->findSocialId($socialInfo['socialId'], null);
            if (null === $user && true === $registration) {
                $dob = date_create($socialInfo['dob']);
                $timenow = date_create(date('Y-m-d'));
                $diff=date_diff($dob,$timenow); 
                if($diff->y < 18){
                    $this->addError('dob', 'You must be 18 years or older');
                    return false;
                }
                $userStatus = $this->create($socialInfo, $check, $isApi);
                if (!empty($userStatus) && $isApi == true) {
                    if ($method == 'Login') {
                        $userStatus->opt_voice = 1;
                        $userStatus->isMobile = 1;
                        //$userStatus->activity = User::ACTIVITY_ONLINE;
                        //$userStatus->activity_update = date('Y-m-d H:i:s');
                        $userStatus->save();
                        return $userStatus;
                    }
                    return true;
                } else {
                    return true;
                }
                //return $this->create($socialInfo, $check,$isApi);
            } elseif (false === $registration && true === $check) {
                if (null !== $user) {
                    $this->addError('token', 'You are already sign up');
                    return false;
                }
                return true;
            } elseif (true === $registration && true === $check && null !== $user) {
                $this->addError('token', 'You are already sign up');
                return false;
            } elseif (null === $user) {
                $this->addError('token', 'Please sign up');
                return false;
            } else {
                if ($user->status == self::STATUS_ACTIVE) {
                    if ($this->social !== self::SOCIAL_EMAIL) {
                        if ($isApi == TRUE && $method == 'Login') {
                            $this->id = $user->id;
                            $user->opt_voice = 1;
                            $user->isMobile = 1;
                            //$user->activity = User::ACTIVITY_ONLINE;
                            //$user->activity_update = date('Y-m-d H:i:s');
                            $user->save();
                            return $user->getAttributes();
                        } else {
                            $this->setAttributes($user->getAttributes());
                            $this->id = $user->id;
                            if (false === $check && $isApi == FALSE) {
                                $this->_login();
                            }
                            return true;
                        }
                    } else {
                        $registration = isset($user->authType[0]) ? $user->authType[0] : null;
                        $hashPass = $this->hashPassword($this->password);
                        if ($registration instanceof UserAuthType && $hashPass === $registration->password && $method == 'Login') {

                            if ($isApi == TRUE && $method == 'Login') {
                                $this->id = $user->id;
                                $user->opt_voice = 1;
                                $user->isMobile = 1;
                                //$user->activity = User::ACTIVITY_ONLINE;
                                //$user->activity_update = date('Y-m-d H:i:s');
                                $user->save();
                                return $user->getAttributes();
                            } else {
                                $this->setAttributes($user->getAttributes());
                                $this->id = $user->id;
                                if (false === $check && $isApi == FALSE) {
                                    $this->_login();
                                }
                                return true;
                            }
                        } else {
                            if (!empty($user) && $isApi == true && $method != 'Login') {
                                $this->id = $user->id;
                                $user->opt_voice = 1;
                                $user->isMobile = 1;
                                //$user->activity = User::ACTIVITY_ONLINE;
                                //$user->activity_update = date('Y-m-d H:i:s');
                                $user->save();
                                return $user->getAttributes();
                            }
                            $this->addError('password', 'Email or Password is incorrect');

                            return false;
                        }
                    }
                } else {
                    $this->addError('token', 'User ' . $user->status);

                    return false;
                }
            }
        } else {
            $this->addError('token', 'Social token fail');

            return false;
        }
    }

    /**
     * @return array|bool
     */
    public function getSocialInfo() {
        $socInfo = [];

        if ($this->social == self::SOCIAL_FACEBOOK) {
            $socInfo['socialId'] = $this->getDigitsInfo();
        } elseif ($this->social == self::SOCIAL_TWITTER) {
            $socInfo['socialId'] = $this->getDigitsInfo();
        } elseif ($this->social == self::SOCIAL_EMAIL) {
            $socInfo['firstName'] = $this->firstName;
            $socInfo['lastName'] = $this->lastName;
            $socInfo['socialId'] = $this->email;
            $socInfo['email'] = $this->email;
            $socInfo['username'] = $this->username;
            $socInfo['password'] = empty($this->password) ? '' : $this->hashPassword($this->password);
            $socInfo['dob'] = date("Y-m-d", strtotime($this->dob));
            $socInfo['apiKey'] = $this->apiKey;
            $socInfo['telephone'] = (isset($this->telephone) && (!empty($this->telephone))) ? $this->telephone : '';
        }

        return $socInfo;
    }

    /**
     * @param        $socialId
     * @param string $status
     * @return mixed
     */
    public function findSocialId($socialId, $status = self::STATUS_ACTIVE) {

        $method = 'find' . ucfirst($this->social) . 'Id';
        //print_r(User::find()->$method($socialId, $status));exit;
        return User::find()->$method($socialId, $status);
    }

    /**
     * @param $apiKey
     * @param string $status
     * @return mixed
     */
    public function findByUserId($id, $status = self::STATUS_ACTIVE) {
        return User::find()->findByUserId($id, $status);
    }

    /**
     * @param $apiKey
     * @param string $status
     * @return mixed
     */
    public function findUserByApiKey($apiKey, $status = self::STATUS_ACTIVE) {
        return User::find()->findUserByApiKey($apiKey, $status);
    }

    /**
     * @return int
     */
    public function activate() {
        return User::updateAll(
                        ['status' => self::STATUS_ACTIVE], ['id' => $this->id, 'status' => self::STATUS_INACTIVE]
        );
    }

    /**
     * @inheritdoc
     */
    public static function tableName() {
        return '{{%user}}';
    }

    /**
     * @inheritdoc
     */
    public function rules() {
        return [
            [['role', 'registrationType'], 'required', 'on' => ['create', 'save']],
            [['email', 'social', 'role', 'firstName'], 'required', 'on' => ['facebookSignUp']],
            [['email', 'password', 'role', 'confirmPassword'], 'required', 'on' => ['emailSignUp', 'addReader']],
            [['password'], 'compare', 'compareAttribute' => 'confirmPassword', 'on' => ['emailSignUp', 'addReader', 'update', 'setPassword']],
            [['email'], 'email'],
            [['createAt', 'testPhone', 'telephone', 'description', 'specialties'], 'safe'],
            [['dob'], 'safe'], //, 'date', 'format' => 'php:d-M-Y'
            [['rate'], 'match', 'pattern' => '/^[0-9]{1,12}(\.[0-9]{0,4})?$/'],
            [['email', 'firstName', 'lastName'], 'string', 'max' => 250],
            [['tagLine'], 'string', 'max' => 140],
            [['email', 'username'], 'unique', 'except' => ['login']],
            [['role'], 'in', 'range' => self::$arrayRoles],
            [['registrationType', 'social'], 'in', 'range' => self::$arraySocials],
            [['status'], 'default', 'value' => self::STATUS_ACTIVE, 'on' => ['save']],
            [['username'], 'default', 'value' => NULL, 'on' => ['update']],
            [['displayname'], 'default', 'value' => NULL, 'on' => ['addReader','update']],
            [['rate'], 'default', 'value' => '0.00'],
            [['tagLine', 'telephone'], 'default', 'value' => ''],
            [['status'], 'in', 'range' => self::$arrayStatuses],
            /* update */
            [['username', 'email', 'firstName', 'lastName'], 'required', 'on' => ['addReader']],
            [['password', 'confirmPassword'], 'safe', 'on' => ['update', 'addReader']],
            [['password'], 'string', 'min' => 8, 'on' => ['emailSignUp', 'update', 'setPassword', 'addReader']],
            [[User::FILENAME], 'file', 'skipOnEmpty' => true, 'extensions' => 'png, jpg, gif', 'maxFiles' => 1],
        ];
    }

    /**
     * @param $attribute
     * @return null
     */
    public function isEmpty($attribute) {
        if ($this->$attribute !== null && $this->$attribute !== '') {
            if (!in_array($attribute, $this->_updateColumn)) {
                if ($attribute === 'password') {
                    if (false === $this->_hashingPassword) {
                        $this->_updatePassword($this->password);
                    }
                } else {
                    if ($attribute === 'email') {
                        UserAuthType::updateAll(
                                ['email' => $this->email], ['userId' => $this->id, 'registrationType' => self::SOCIAL_EMAIL]
                        );
                    }

                    $this->_updateColumn[] = $attribute;
                }
            }
        } else {
            $this->$attribute = isset($this->oldAttributes[$attribute]) ? $this->oldAttributes[$attribute] : null;
        }
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels() {
        return [
            'id' => 'ID',
            'role' => 'Role',
            'registrationType' => 'Registration Type',
            'email' => 'Email',
            'firstName' => 'First Name',
            'lastName' => 'Last Name',
            'dob' => 'Date of Birth',
            'status' => 'Status',
            'createAt' => 'Create At',
        ];
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getAuthType() {
        return $this->hasMany(UserAuthType::className(), ['userId' => 'id']);
    }

    /**
     * @return int
     */
    public function updateAuthFields() {
        //TODO add WHERE type = email
        //also check if exists, if no - create

        $count = UserAuthType::findOne(['userId' => $this->id, 'registrationType' => 'email']);
        if (empty($count->id))
            return false;

        return UserAuthType::updateAll(
                        ['email' => $this->email, 'socialNetworkId' => $this->email], ['userId' => $this->id, 'registrationType' => 'email']
        );
    }

    public function UpdateOpt($option, $status) {
        return User::updateAll(['opt_' . $option => $status], ['id' => $this->id]);
    }

    /**
     * @return int
     */
    public function getSpecialties($as_array = false) {
        $connection = Yii::$app->getDb();
        $data = $connection->createCommand("SELECT `specialty` FROM `md_user_specialty` WHERE `userId` = :user_id", [':user_id' => $this->id])->queryAll();
        $aRet = [];
        if (!empty($data)) {
            foreach ($data as $item) {
                $aRet[] = $item['specialty'];
            }
        }



        return ($as_array) ? $aRet : implode(",", $aRet);
    }

    public function getSpecialtie($id) {
        $connection = Yii::$app->getDb();
        $data = $connection->createCommand("SELECT `specialty` FROM `md_user_specialty` WHERE `userId` = :user_id", [':user_id' => $id])->queryAll();
        $aRet = [];
        if (!empty($data)) {
            foreach ($data as $item) {
                $aRet[] = $item['specialty'];
            }
        }
        return implode(",", $aRet);
    }

    /**
     * @return int
     */
    public function saveSpecialties() {
        $connection = Yii::$app->getDb();
        $connection->createCommand("DELETE FROM `md_user_specialty` WHERE `userId` = :user_id", [':user_id' => $this->id])->execute();

        $aSpecialties = explode(",", $this->specialties);
        if (count($aSpecialties)) {
            foreach ($aSpecialties as $specialty) {
                $specialty = trim($specialty);
                if (empty($specialty))
                    continue;
                $connection->createCommand("INSERT INTO `md_user_specialty` (`userId`, `specialty`) VALUES (:user_id, :specialty);", [':user_id' => $this->id, ':specialty' => $specialty])->execute();
            }
        }
    }

    /**
     * @inheritdoc
     * @return \app\models\query\User the active query used by this AR class.
     */
    public static function find() {
        return new UserQuery(get_called_class());
    }

    /**
     * @param object|array $object
     * @param string|array $attributes
     * @param mixed        $default
     * @return mixed
     */
    public static function getValue($object, $attributes, $default = '') {
        if (is_array($attributes) && !empty($attributes)) {
            $localObject = $object;
            foreach ($attributes as $attribute) {
                if (is_int($attribute) && !empty($localObject[$attribute])) {
                    $localObject = $localObject[$attribute];
                } elseif (is_string($attribute) && !empty($localObject->$attribute)) {
                    $localObject = $localObject->$attribute;
                } else {
                    return $default;
                }
            }

            return $localObject;
        } else {
            if (is_int($attributes) && isset($object[$attributes])) {
                return $object[$attributes];
            } elseif ($attributes === null) {
                return $object;
            } else {
                if (isset($object->$attributes)) {
                    return $object->$attributes;
                } else {
                    return $default;
                }
            }
        }
    }

    /**
     * @param $newPassword
     * @return int
     */
    private function _updatePassword($newPassword) {
        return UserAuthType::updateAll(
                        ['password' => $this->hashPassword($newPassword)], ['userId' => $this->id]
        );
    }

    /**
     * @param int $duration 3600 * 24 * 30 = 30 days
     * @param int $default
     * @return bool
     */
    private function _login($duration = 2592000, $default = 0) {
        return Yii::$app->user->login($this, $this->rememberMe ? $duration : $default);
    }

    public static function isAdmin() {

        if (Yii::$app->user->isGuest)
            return false;
        return (self::ROLE_ADMIN == Yii::$app->user->identity->getAttribute('role'));
    }

    public static function isReader() {

        if (Yii::$app->user->isGuest)
            return false;
        return (self::ROLE_READER == Yii::$app->user->identity->getAttribute('role'));
    }

    public function isAvailableForCalls() {
        return ($this->activity != self::ACTIVITY_DISABLED && $this->opt_voice);
    }

    public static function isUser() {

        if (Yii::$app->user->isGuest)
            return false;
        return (self::ROLE_USER == Yii::$app->user->identity->getAttribute('role'));
    }

    public static function ChargeDuringCall() {

        $leftJoin = [];
        $leftJoin[] = "LEFT JOIN `md_gruvi_bucks` gb ON gb.`callId` = c.`id` AND gb.`status` = :gruvi_bucks_status";
        $leftJoin[] = "LEFT JOIN `md_user` as r on r.`id` = c.`readerId`";

        $select = [];
        $select[] = "c.`id`";
        $select[] = "c.`twilioCallId`";
        $select[] = "c.`customerId`";
        $select[] = "c.`readerId`";
        $select[] = "r.`rate`";
        $select[] = "TIMESTAMPDIFF(SECOND, c.`callAnswerTime`,NOW()) as estimatedDuration";
        $select[] = "IF(SUM(gb.`amount`) IS NULL,0,SUM(gb.`amount`)) as paid";
        $select[] = "(CEIL(TIMESTAMPDIFF(SECOND, c.`callAnswerTime`,NOW()) / 60)*r.`rate`) as haveToPay";

        $sql = "SELECT " . implode(",", $select) . " FROM `md_call` as c " . implode(" ", $leftJoin) . " WHERE c.`status` = :call_status GROUP BY c.`id` HAVING (paid+haveToPay > 0)";
        //echo $sql;exit;
        $connection = Yii::$app->getDb();
        $command = $connection->createCommand($sql, ['call_status' => Call::STATUS_CONVERSATION, 'gruvi_bucks_status' => GruviBucks::STATUS_APPROVED]);
        $activeCalls = $command->queryAll();

        if (!empty($activeCalls)) {
            foreach ($activeCalls as $call) {
                $gruvi_bucks = new GruviBucks();
                $gruvi_bucks->callId = $call['id'];
                $gruvi_bucks->userId = $call['customerId'];
                $gruvi_bucks->amount = $call['haveToPay'] + $call['paid'];
                $gruvi_bucks->log = 'auto charge during call';
                //ECHO '**'.$gruvi_bucks->charge(TRUE).'**';EXIT;
                if (!$gruvi_bucks->charge(false, true)) {
                    $call = Call::findOne(['id' => $call['id']]);
                    $call->abortActiveCall();
                }
            }
        }
    }

    public static function OfflineByTimeout() {
        $expression = new Expression('NOW()');
        $aUpd = ['activity' => User::ACTIVITY_OFFLINE, 'activity_update' => $expression];
        $cond = "(((`activity` = :activity1 AND (`activity_update` + INTERVAL 10 SECOND) < NOW()) OR (`activity` = :activity2 AND (`activity_update` + INTERVAL 15 SECOND) < NOW())) AND (`isMobile` = :isMobile))";
        $so = self::updateAll($aUpd, $cond, [':activity1' => User::ACTIVITY_ONLINE, ':activity2' => User::ACTIVITY_SESSION,':isMobile' => 0]);
        return $so;
    }

    public function UpdateRate($rate = 0.00) {


        $this->rate = $rate;

        if ($this->update(true, ['rate'])) {
            return true;
        }

        return false;
    }

    public function UpdateActivity($new_activity = self::ACTIVITY_ONLINE) {


        $this->activity = $new_activity;
        $this->activity_update = date('Y-m-d H:i:s');

        if ($this->update(true, ['activity', 'activity_update'])) {
            return true;
        }

        return false;
    }

    public function getGruviBucksAmount() {

        if (empty($this->_gruviBucksAmount)) {//cache
            $this->_gruviBucksAmount = GruviBucks::getUserBalance($this->id);
        }

        return $this->_gruviBucksAmount;
    }

    public function getCreditCards() {
        return UserCreditCard::getUserCreditCards($this->id);
    }

    public function getCreditCardCount() {
        return UserCreditCard::find()->where(['userId' => $this->id, 'status' => UserCreditCard::STATUS_ACTIVE])->count();
    }

    public function getDefaultCreditCard() {
        return UserCreditCard::getUserDefaultCreditCard($this->id);
    }

    public function getCallsReaders() {
        return $this->hasMany(Call::className(), ['readerId' => 'id']);
    }

    /**
     * Finds user by password reset token
     *
     * @param string $token password reset token
     * @return static|null
     */
    public static function findByPasswordResetToken($token) {
        if (!static::isPasswordResetTokenValid($token)) {
            return null;
        }

        return static::findOne([
                    'password_reset_token' => $token,
                    'status' => self::STATUS_ACTIVE,
        ]);
    }

    /**
     * Finds out if password reset token is valid
     *
     * @param string $token password reset token
     * @return boolean
     */
    public static function isPasswordResetTokenValid($token) {
        if (empty($token)) {
            return false;
        }

        $timestamp = (int) substr($token, strrpos($token, '_') + 1);
        $expire = Yii::$app->params['user.passwordResetTokenExpire'];
        return $timestamp + $expire >= time();
    }

    /**
     * Generates new password reset token
     */
    public function generatePasswordResetToken() {
        $this->password_reset_token = Yii::$app->security->generateRandomString() . '_' . time();
    }

    /**
     * Removes password reset token
     */
    public function removePasswordResetToken() {
        $this->password_reset_token = null;
    }

    public function canChat() {
        return ($this->opt_chat && $this->activity == User::ACTIVITY_ONLINE);
    }

    public function canCall() {
        return ($this->opt_voice && $this->activity == User::ACTIVITY_ONLINE);
    }

    /**
     * Finds out if password reset token is valid
     *
     * @param int $minMessageId return messages where id > minMessageId
     * @param int $myId session user
     * @param int $maxMessageId return messages where id < maxMessageId
     * @return array
     */
    public function renderChat($minMessageId = 0, $myId = 0, $maxMessageId = 0) {

        if (empty($myId) && !Yii::$app->user->isGuest)
            $myId = Yii::$app->user->identity->id;
        if ($this->role != User::ROLE_READER)
            return [];

        $limit = 51; //50+1 (next page indicator)
        $nextPageId = 0;

        $messagesSearch = new MessageSearch();
        $messagesShow = $messagesSearch->chatList($this->id, $minMessageId, $maxMessageId, $limit);
        $messagesClear = $messagesSearch->clearList($this->id);

        if (empty($minMessageId) && count($messagesShow) == $limit) {
            $nextItem = array_shift($messagesShow);
            $nextPageId = $nextItem->id - 1;
        }

        $pass = ['messages' => $messagesShow, 'myId' => $myId, 'readerId' => $this->id, 'nextPageId' => $nextPageId];
        //hh($pass);
        $html = Yii::$app->controller->renderPartial('@app/views/message/list', $pass);

        $minMessageId = count($messagesShow) ? $messagesShow[count($messagesShow) - 1]->id : "0";

        return ['html' => $html, 'minMessageId' => $minMessageId, 'messagesClear' => $messagesClear['ids']]; // %PSG: '$chat' in view
    }

    public function getChat($minMessageId = 0, $myId = 0, $maxMessageId = 0) {

        if ($this->role != User::ROLE_READER)
            return [];

        $limit = 51; //50+1 (next page indicator)
        $nextPageId = 0;

        $messagesSearch = new MessageSearch();
        $messagesShow = $messagesSearch->chatList($this->id, $minMessageId, $maxMessageId, $limit);
        $messagesClear = $messagesSearch->clearList($this->id);

        if (empty($minMessageId) && count($messagesShow) == $limit) {
            $nextItem = array_shift($messagesShow);
            $nextPageId = $nextItem->id - 1;
        }
        return $messagesShow;
//        $pass = ['messages' => $messagesShow, 'myId' => $myId, 'readerId' => $this->id, 'nextPageId' => $nextPageId];
//        $html = Yii::$app->controller->renderPartial('@app/views/message/list', $pass);
//
//        $minMessageId = count($messagesShow) ? $messagesShow[count($messagesShow) - 1]->id : "0";
//
//
//
//        return ['html' => $html, 'minMessageId' => $minMessageId, 'messagesClear' => $messagesClear['ids']];
    }

    static function getArrayForSelect() {
        $db_expr = new \yii\db\Expression("CONCAT('#',`id`, ' ', COALESCE(`firstName`, ''), ' ', COALESCE(`lastName`, '')) as flname");
        return yii\helpers\ArrayHelper::map(User::find()->select(['*', $db_expr])->asArray()->all(), 'id', 'flname');
    }

    /**
     * @param $email
     * @param $userId
     * @return bool
     */
    public function isExists($email, $userId) {
        return (bool) $this->where(
                                'email = :e AND userId != :u', [':e' => $email, ':u' => $userId]
                        )
                        ->one();
    }

}
