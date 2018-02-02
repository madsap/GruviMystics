<?php

namespace app\models;

use \Yii;
use \yii\db\ActiveRecord;
use \yii\helpers\VarDumper;
use \yii\web\UploadedFile;

/**
 * This is the model class for table "{{%files}}".
 *
 * @property integer $id
 * @property integer $userId
 * @property string $hashName
 * @property string $extension
 * @property string $tableName
 * @property integer $tableId
 * @property integer $order
 * @property string $typeFile
 * @property string $localName
 * @property string $hashFile
 * @property integer $size
 * @property string $mainCategoryName
 * @property string $categoryName
 * @property string $url
 * @property integer $width
 * @property integer $height
 * @property integer $length
 * @property string $status
 * @property string $createAt
 *
 * @property string $_filePath
 * @property string $_realFilePath
 * @property array $_options
 * @property string $_dirPath
 */
class File extends ActiveRecord {

    const STATUS_ACTIVE = 'active';
    const STATUS_INACTIVE = 'inactive';
    const STATUS_DELETED = 'deleted';
    const STATUS_UPLOADED = 'uploaded';
    const TYPE_IMAGE = 'image';
    const TYPE_VIDEO = 'video';
    const CATEGORY_ORIGINAL = 'original';
    const CATEGORY_SMAll = 'small';
    const CATEGORY_MIDDLE = 'middle';
    const CATEGORY_LARGE = 'large';
    const EXTENSION_NAME_VIDEO_THUMB = 'jpg';
    const PROPERTY_UPLOADED = 'uploadedFile';
    const EXTENSION_NAME_VIDEO = 'mp4';
    const HASH_LENGTH = 64;
    const DIR_FILES_NAME = 'f';
    const ACTION_VALIDATE = 'validate';
    const ACTION_SAVE = 'save';

    private $_filePath = '';
    private $_realFilePath = null;
    private $_options = [];
    private $_dirPath = '';
    public static $arrayType = [
        self::TYPE_IMAGE,
        self::TYPE_VIDEO
    ];
    private static $_arrayTableName = [
        User::TABLE_NAME,
        Message::TABLE_NAME,
    ];
    private static $_arrayMainCategoryName = [
        User::MAIN_CATEGORY_LOGO,
        Message::MAIN_CATEGORY_ATTACHMENT,
    ];
    private static $_arrayCategoryName = [
        self::CATEGORY_ORIGINAL,
        self::CATEGORY_SMAll,
        self::CATEGORY_MIDDLE,
        self::CATEGORY_LARGE,
    ];

    public function init() {
        $this->_filePath = DS . 'data' . DS . 'files' . DS;
        $this->_realFilePath = Yii::getAlias('@app') . $this->_filePath;
        parent::init();
    }

    /**
     * @param $file
     * @param string $size
     * @return string
     */
    public function getFileUrl($file, $size = self::CATEGORY_MIDDLE) {
        $filename = $file->hashName . ($file->extension ? '.' . $file->extension : '');
        return '//' . DOMAIN . '/file/' . $size . '/' . $filename;
    }

    /**
     * @return string
     */
    public function getMimeType() {
        $mimeType = '';
        if ($this->typeFile == self::TYPE_IMAGE) {
            $mimeType = 'image/jpg';
        } elseif ($this->typeFile == self::TYPE_VIDEO) {
            $mimeType = 'video/mp4';
        }

        return $mimeType;
    }

    /**
     * Uploading images
     * @param $file
     * @param $userId
     * @param $options
     * @return array
     */
    public function uploadImage($file, $userId, $options) {
        $options['tableId'] = $this->_keyExists($options, 'tableId', 0);
        $options['tableName'] = $this->_keyExists($options, 'tableName', '');

        switch ($options['tableName']) {
            case User::TABLE_NAME:
                User::checkPermission($userId, $options['tableId'], User::ACTION_UPLOAD_IMAGE);
                $upload = User::checkPermission($userId, $options['tableId'], User::ACTION_UPLOAD_IMAGE);
                break;
            default:
                return null;
        }

        if (true === $upload) {
            $this->setScenario('uploaded');
            return $this->saveFile($file, $userId, $options, self::STATUS_UPLOADED);
        } else {
            return null;
        }
    }

    /**
     * @param $userId
     * @param $hash
     * @param $type
     * @return bool
     */
    //    public function markAsMain($userId, $hash, $type)
    //    {
    //        /** @var Files $mainFile */
    //        $mainFile = Files::find()->where('userId = :u AND hashName = :h AND type = :t', [
    //                ':u' => $userId,
    //                ':h' => $hash,
    //                ':t' => $type
    //        ])->one();
    //
    //        if(null !== $mainFile) {
    //            self::updateAll(
    //                ['order' => 0],
    //                'userId = :u AND type = :t AND tableName = :tn AND tableId = :ti',
    //                array(
    //                    ':u' => $userId,
    //                    ':t' => $type,
    //                    ':tn' => $mainFile->tableName,
    //                    ':ti' => $mainFile->tableId
    //                )
    //            );
    //
    //            $update = self::model()->updateAll(
    //                array('order' => 1),
    //                's3_content.order = :or AND userId = :u AND type = :t AND tableName = :tn AND tableId = :ti AND gr_hash = :h',
    //                array(
    //                    ':u' => $userId,
    //                    ':t' => $type,
    //                    ':tn' => $mainFile->tableName,
    //                    ':ti' => $mainFile->tableId,
    //                    ':h' => $hash,
    //                    ':or' => 0
    //                )
    //            );
    //
    //            return (bool) $update;
    //        }
    //
    //        return false;
    //    }

    /**
     * @param $tableName
     * @param $tableId
     * @return int
     */
    public static function deleteByObject($tableName, $tableId) {
        return self::updateAll(['status' => self::STATUS_DELETED], ['tableName' => $tableName, 'tableId' => $tableId]);
    }

    /**
     * @return null|string
     */
    public function getRealFilePath() {
        return $this->_realFilePath;
    }

    /**
     * Update rows as active by array hashName
     * @param $arrayHashName
     * @param $tableId
     * @return void
     */
    public function setActivateArray($arrayHashName, $tableId) {
        if (is_array($arrayHashName)) {
            foreach ($arrayHashName as $hashName) {
                $this->setActive($hashName, $tableId);
            }
        }
    }

    /**
     * Update rows as active by HashName
     * @param $hashName
     * @param $tableId
     * @return integer
     */
    public function setActive($hashName, $tableId) {
        return self::updateAll(['status' => self::STATUS_ACTIVE, 'tableId' => $tableId], ['hashName' => $hashName]);
    }

    /**
     * @param $hashName
     * @param $tableId
     * @param $tableName
     * @param int $numberLastFile
     */
    public function activate($hashName, $tableId, $tableName, $numberLastFile = 0) {
        $activate = $this->setActive($hashName, $tableId);

        if ($activate > 0 && $numberLastFile > 0) {
            $files = File::find()
                    ->where(
                            'tableId = :ti AND tableName = :tn AND status = :s AND categoryName = :cn', [
                        ':ti' => $tableId,
                        ':tn' => $tableName,
                        ':s' => self::STATUS_ACTIVE,
                        ':cn' => self::CATEGORY_ORIGINAL
                            ]
                    )
                    ->orderBy(['id' => SORT_DESC])
                    ->all();

            /** @var File $file */
            foreach ($files as $file) {
                --$numberLastFile;
                if (0 > $numberLastFile) {
                    $this->setInactive($file->hashName);
                }
            }
        }
    }

    /**
     * @return int
     */
    public static function inactivateOldUploadImage() {
        return File::updateAll(
                        ['status' => File::STATUS_DELETED], 'status = :s AND createAt < :c', [':s' => File::STATUS_UPLOADED, ':c' => date('Y-m-d H:i:s', strtotime('-1 day'))]
        );
    }

    /**
     * Update rows as inactive by HashName
     * @param $hashName
     * @return void
     */
    public function setInactive($hashName) {
        self::updateAll(['status' => self::STATUS_INACTIVE], 'hashName = :g', [':g' => $hashName]);
    }

    /**
     * Update rows as deleted by HashName
     * @param $hashName
     * @return void
     */
    public function setDeleted($hashName) {
        self::updateAll(['status' => self::STATUS_DELETED], 'hashName = :g', [':g' => $hashName]);
    }

    /**
     * Deactivate all old content into array
     * @param $array
     * @param null $type
     * @throws \Exception
     * @return void
     */
    public function setDeletedArray($array, $type = null) {
        if (!empty($array)) {
            foreach ($array as $files) {
                $this->setDeletedObject($files, $type);
            }
        }
    }

    /**
     * Deactivate content
     * @param $files
     * @param null $type
     * @throws \Exception
     */
    public function setDeletedObject($files, $type = null) {
        if ($files instanceof File) {
            if ($files->type == $type || $type === null) {
                $files->status = self::STATUS_DELETED;
                $files->update(['status']);
            }
        }
    }

    /**
     * @return void
     */
    public function setDirPath() {
        $mainDir = $this->id % 100;
        $firstPart = floor($this->id / 100);
        $ids = str_split($firstPart);

        $this->_dirPath = $this->_realFilePath . implode(DS, $ids) . DS . self::DIR_FILES_NAME . $mainDir;
    }

    /**
     * @return string
     */
    public function getDirPath() {
        if ($this->_dirPath === '') {
            $this->setDirPath();
        }

        return $this->_dirPath;
    }

    /**
     * Remove files
     * @param $userId
     * @param $options
     * @return bool
     */
    public function remove($userId, $options) {
        $options['hashName'] = $this->_keyExists($options, 'hashName', 0);
        $options['tableName'] = $this->_keyExists($options, 'tableName', '');
        $options['tableId'] = $this->_keyExists($options, 'tableId', '');

        $file = self::find()->where(['hashName' => $options['hashName']])->one();
        if (null !== $file) {
            switch ($options['tableName']) {
                case User::TABLE_NAME:
                    $upload = User::checkPermission($userId, $options['tableId'], User::ACTION_REMOVE_IMAGE);
                    break;
                default:
                    $upload = false;
                    break;
            }

            if (true === $upload) {
                $this->setDeleted($options['hashName']);
                return true;
            }
        }

        return null;
    }

    /**
     * Get extension name
     * @param $hashName
     * @param null $type
     * @return string
     */
    public static function getExtensionName($hashName, $type = null) {
        $criteria = ['hashName' => $hashName];
        if (null !== $type) {
            $criteria['type'] = $type;
        }
        /** @var File $files */
        $files = self::find()->where($criteria)->one();
        if (null !== $files) {
            return substr(strrchr($files->localName, '.'), 1);
        }

        return '';
    }

    /**
     * Get absolute path
     * @param string $categoryName
     * @param string $id
     * @param string $status
     * @return bool|string
     */
    public function getFilePathByKey($categoryName, $id, $status = self::STATUS_ACTIVE) {
        $hashName = mb_substr($id, 0, strrpos($id, '.'));
        $hashName = $hashName ? : $id;
        $row = File::find()->where(['categoryName' => $categoryName, 'hashName' => $hashName, 'status' => $status])->one();
        if (null !== $row) {
            $this->setAttributes($row->getAttributes());
            $this->id = $row->id;
            if (empty($this->url)) {
                return $this->_getFilePath();
            } else {
                return $this->url;
            }
        }

        return false;
    }

    /**
     * @param $tableName
     * @param $tableId
     * @param string $status
     * @return array
     */
    public function getFilesByObject($tableName, $tableId, $status = self::STATUS_ACTIVE) {
        $condition = 'tableName = :tn AND tableId = :ti AND status = :s';
        $params = [':tn' => $tableName, ':ti' => $tableId, ':s' => $status];
        $images = File::find()->where($condition, $params)->all();
        $arrayImages = [];
        if (!empty($images)) {
            /** @var \app\models\File $file */
            foreach ($images as $file) {
                $arrayImages[$file->hashName][] = [
                    'url' => $file->getFileUrl($file, $file->categoryName),
                    'type' => $file->categoryName,
                    'width' => $file->width,
                    'height' => $file->height,
                    'order' => $file->order
                ];
            }
        }

        return array_values($arrayImages);
    }

    /**
     * @param $tableName
     * @param $tableId
     * @param $mainCategory
     * @param $category
     * @param string $src
     * @param string $status
     * @return string
     */
    public function getSrc($tableName, $tableId, $mainCategory, $category, $src = '#', $status = self::STATUS_ACTIVE) {
        $condition = 'tableName = :n AND tableId = :i AND mainCategoryName = :m AND categoryName = :c AND status = :s';
        $params = [':n' => $tableName, ':i' => $tableId, ':m' => $mainCategory, ':c' => $category, ':s' => $status];
        /** @var File $image */
        $image = File::find()->where($condition, $params)->one();

        if (empty($image)) {
            $condition = 'tableName = :tn AND tableId = :ti AND mainCategoryName = :m AND status = :s';
            $params = [':tn' => $tableName, ':ti' => $tableId, ':m' => $mainCategory, ':s' => $status];
            $image = File::find()->where($condition, $params)->one();

            if (empty($image)) {
                return $src;
            }
        }

        return $this->getFileUrl($image, $image->categoryName);
    }

    /**
     * Set common property and call save file and its info
     * @param $fileObject
     * @param int $userId
     * @param array $options
     * @param string $status
     * @return array|string
     */
    public function saveFile($fileObject, $userId, $options, $status = self::STATUS_ACTIVE) {
        if ($fileObject instanceof UploadedFile) {
            $this->_options = $options;

            $this->userId = $userId;
            $this->hashName = $this->generateHash();
            $this->mainCategoryName = $this->_optionsExist('mainCategoryName');
            $this->tableName = $this->_optionsExist('tableName');
            $this->tableId = $this->_optionsExist('tableId');
            $this->typeFile = $this->_optionsExist('typeFile');
            $this->order = $this->_optionsExist('order');
            $this->status = $status;

            if ($this->_optionsExist('typeFile') == self::TYPE_VIDEO) {
                $this->_saveVideoFile($fileObject);
            } elseif ($this->_optionsExist('typeFile') == self::TYPE_IMAGE) {
                $this->_saveImageFile($fileObject);
            }

            return $this->hashName;
        }

        return null;
    }

    /**
     * @param $url
     * @param $userId
     * @param $options
     * @param string $status
     * @return bool
     */
    public function saveUrl($url, $userId, $options, $status = self::STATUS_ACTIVE) {
        $this->_options = $options;

        $this->url = $url;
        $this->userId = $userId;
        $this->hashName = $this->generateHash();
        $this->mainCategoryName = $this->_optionsExist('mainCategoryName');
        $this->categoryName = $this->_optionsExist('categoryName');
        $this->tableName = $this->_optionsExist('tableName');
        $this->tableId = $this->_optionsExist('tableId');
        $this->typeFile = $this->_optionsExist('typeFile');
        $this->order = $this->_optionsExist('order');
        $this->status = $status;
        if ($this->save()) {
            return true;
        } else {
            Yii::info(VarDumper::dumpAsString($this->getErrors()), 'show');
            return false;
        }
    }

    /**
     * @param \yii\db\ActiveRecord $model
     * @param $attribute
     * @param $userId
     * @param $id
     * @param $category
     * @param $files
     * @param string $action
     * @return bool
     */
    public function saveImage($model, $attribute, $userId, $id, $category, $files, $action = self::ACTION_VALIDATE) {
        $filesArray = isset($files[$attribute]) && is_array($files[$attribute]) ? $files[$attribute] : null;

        if (null !== $filesArray) {
            foreach ($filesArray as $fileItem) {
                $model->$attribute = new UploadedFile();
                $model->$attribute->name = $fileItem['name'];
                $model->$attribute->type = $fileItem['type'];
                $model->$attribute->tempName = $fileItem['tmp_name'];
                $model->$attribute->size = $fileItem['size'];
                $model->$attribute->error = $fileItem['error'];

                if (!$model->validate()) {
                    $this->addErrors($model->getErrors());
                    return false;
                } elseif ($action == self::ACTION_SAVE) {
                    if (!empty($model->$attribute->tempName)) {
                        if (in_array($category, [User::MAIN_CATEGORY_LOGO])) {
                            File::updateAll(
                                    ['status' => File::STATUS_DELETED], 'tableId = :ti AND mainCategoryName = :m AND tableName = :t', [':ti' => $model->id, ':m' => $category, ':t' => $model::TABLE_NAME]
                            );
                        }

                        $options = [
                            'tableName' => $model::TABLE_NAME,
                            'tableId' => ($id ? : 0),
                            'typeFile' => File::TYPE_IMAGE,
                            'mainCategoryName' => $category
                        ];

                        $file = new File();
                        $file->setScenario('uploaded');
                        $file->saveFile($model->$attribute, $userId, $options, File::STATUS_ACTIVE);
                    }
                }
            }
        }

        return true;
    }

    public function savePhoto($model, $attribute, $userId, $id, $category, $files, $action = self::ACTION_VALIDATE) {
        $filesArray = isset($files[$attribute]) && is_array($files[$attribute]) ? $files[$attribute] : null;

        if (null !== $filesArray) {
            foreach ($filesArray as $fileItem) {
                $model->$attribute = new UploadedFile();
                $model->$attribute->name = $fileItem['name'];
                $model->$attribute->type = $fileItem['type'];
                $model->$attribute->tempName = $fileItem['tmp_name'];
                $model->$attribute->size = $fileItem['size'];
                $model->$attribute->error = $fileItem['error'];

                if ($action == self::ACTION_SAVE) {
                    if (!empty($model->$attribute->tempName)) {
                        if (in_array($category, [User::MAIN_CATEGORY_LOGO])) {
                            File::updateAll(
                                    ['status' => File::STATUS_DELETED], 'tableId = :ti AND mainCategoryName = :m AND tableName = :t', [':ti' => $model->id, ':m' => $category, ':t' => $model::TABLE_NAME]
                            );
                        }

                        $options = [
                            'tableName' => $model::TABLE_NAME,
                            'tableId' => ($id ? : 0),
                            'typeFile' => File::TYPE_IMAGE,
                            'mainCategoryName' => $category
                        ];

                        $file = new File();
                        $file->setScenario('uploaded');
                        $file->saveFile($model->$attribute, $userId, $options, File::STATUS_ACTIVE);
                    }
                }
            }
        }

        return true;
    }

    public function saveBase64Image($base64String, $userId, $category) {

        if ($base64String != "") {

            if (in_array($category, [User::MAIN_CATEGORY_LOGO])) {
                File::updateAll(
                        ['status' => File::STATUS_DELETED], 'tableId = :ti AND mainCategoryName = :m AND tableName = :t', [':ti' => $userId, ':m' => $category, ':t' => User::TABLE_NAME]
                );
            }

            $options = [
                'tableName' => User::TABLE_NAME,
                'tableId' => ($userId ? : 0),
                'typeFile' => File::TYPE_IMAGE,
                'mainCategoryName' => $category
            ];

            $file = new File();
            $file->setScenario('uploaded');
            $response = $file->saveBase64File($base64String, $userId, $options, File::STATUS_ACTIVE);
            if (!$response) {
                return false;
            }
            //return $response;
        }

        return true;
    }

    public function saveBase64File($base64String, $userId, $options, $status = self::STATUS_ACTIVE) {
        if ($base64String != "") {
            $this->_options = $options;

            $this->userId = $userId;
            $this->hashName = $this->generateHash();
            $this->mainCategoryName = $this->_optionsExist('mainCategoryName');
            $this->tableName = $this->_optionsExist('tableName');
            $this->tableId = $this->_optionsExist('tableId');
            $this->typeFile = $this->_optionsExist('typeFile');
            $this->order = $this->_optionsExist('order');
            $this->status = $status;

            if ($this->_optionsExist('typeFile') == self::TYPE_IMAGE) {
                $response = $this->_saveBase64ImageFile($base64String);
                if (!$response) {
                    return null;
                }
                //return $response;
            }

            return $this->hashName;
        }

        return false;
    }

    /**
     * Save image file
     * @param UploadedBae64File $base64String
     */
    private function _saveBase64ImageFile($base64String) {
        $this->save();

        $path = $this->getDirPath() . DS . $this->id . '.' . 'jpeg';
        //return $path;
        //echo $path;exit;
        if (!file_exists($this->getDirPath() . DS . $this->id)) {
            mkdir($this->getDirPath() . DS . $this->id, 0777, true);
        }

        $this->createFile($path,$base64String);

        $this->_saveInfoFile($path, self::CATEGORY_ORIGINAL);

        /** @var \app\components\ImageResizeHelper $imageResize */
        $imageResize = Yii::$app->imageResize->setParams($path);
        foreach (self::$_arrayCategoryName as $category) {
            if ($category !== self::CATEGORY_ORIGINAL) {
                $this->id = null;
                $this->setIsNewRecord(true);
                if ($this->save()) {
                    $this->setDirPath();
                    $pathCategoryFile = $this->getDirPath() . DS . $this->id . '.' . 'jpeg';
                    if (true === $this->_mkDir()) {
                        $method = 'create' . ucfirst($category) . 'Image';
                        if (method_exists($imageResize, $method)) {
                            //$this->createFile($pathCategoryFile,$base64String);
                            $create = $imageResize->$method($pathCategoryFile);
                            if (true !== $create) {
                                return false;
                            } else {
                                $this->_saveInfoFile($pathCategoryFile, $category);
                            }
                        } else {
                            return false;
                        }
                    }
                } else {
                    return false;
                }
            }
        }
        return true;
    }

    /**
     * @param \yii\db\ActiveRecord $model
     * @param $attribute
     * @param $userId
     * @param $id
     * @param $mainCategoryName
     * @param $requestFiles
     * @param string $action
     * @return bool
     */
    public function saveVideo($model, $attribute, $userId, $id, $mainCategoryName, $requestFiles, $action = self::ACTION_VALIDATE) {
        $files = isset($requestFiles[$attribute]) && is_array($requestFiles[$attribute]) ? $requestFiles[$attribute] : null;

        if (null !== $files) {
            foreach ($files as $file) {
                $model->$attribute = new UploadedFile();
                $model->$attribute->name = $file['name'];
                $model->$attribute->type = $file['type'];
                $model->$attribute->tempName = $file['tmp_name'];
                $model->$attribute->size = $file['size'];
                $model->$attribute->error = $file['error'];

                if (!$model->validate([$attribute], false)) {
                    $this->addErrors($model->getErrors());
                    return false;
                } elseif ($action == self::ACTION_SAVE) {
                    if (!empty($model->$attribute->tempName)) {
                        $options = [
                            'tableName' => $model::TABLE_NAME,
                            'tableId' => ($id ? : 0),
                            'typeFile' => File::TYPE_VIDEO,
                            'mainCategoryName' => $mainCategoryName
                        ];

                        $files = new File();
                        $files->setScenario('uploaded');
                        $files->saveFile($model->$attribute, $userId, $options, File::STATUS_ACTIVE);
                    }
                }
            }
        }

        return true;
    }

    /**
     * Generate GR hash for file
     * @param int $length
     * @return string
     * @throws \yii\base\Exception
     */
    public function generateHash($length = self::HASH_LENGTH) {
        return strtr(substr(
                        base64_encode($bytes = Yii::$app->getSecurity()->generateRandomKey($length)), 0, $length), '+/_-', 'bFkT'
        );
    }

    /**
     * Check images directory
     * @return bool
     */
    public function beforeValidate() {
        if (!is_writable($this->_realFilePath)) {
            $this->addError('localName', $this->_realFilePath . ' - Permission denied');
            Yii::warning(VarDumper::dumpAsString($this->_realFilePath . ' - Permission denied'), 'warning');
            return false;
        }
        /* //due to klean
          $apacheModules = apache_get_modules();
          if(!in_array('mod_xsendfile', $apacheModules)) {
          $this->addError('localName', 'Error: Need install Apache2 module that processes X-SENDFILE');
          return false;
          } */

        return true;
    }

    /**
     * Get table name for $tableName
     * @param $id
     * @return string
     */
    public static function getTableName($id) {
        $tableName = isset(self::$_arrayTableName[$id]) ? self::$_arrayTableName[$id] : null;
        return null !== $tableName ? $tableName::tableName() : '';
    }

    /**
     * Generate unique name
     * @return string
     */
    public function getUniqueFileName() {
        return $this->generateHash();
    }

    /**
     * @inheritdoc
     */
    public static function tableName() {
        return '{{%file}}';
    }

    /**
     * @inheritdoc
     */
    public function rules() {
        return [
            [['order', 'length'], 'default', 'value' => 0],
            [
                [
                    'userId', 'hashName', 'extension', 'tableName', 'tableId', 'order', 'typeFile', 'localName',
                    'hashFile', 'size', 'mainCategoryName', 'categoryName'
                ],
                'required', 'except' => ['uploaded', 'saveUrl']
            ],
            [['userId', 'tableId', 'order', 'size', 'width', 'height', 'length'], 'integer'],
            [['typeFile', 'categoryName', 'url'], 'string'],
            [['createAt'], 'safe'],
            [['hashName'], 'string', 'max' => 100],
            [['hashFile'], 'string', 'max' => 32],
            [['extension'], 'string', 'max' => 10],
            [['localName'], 'string', 'max' => 200],
            [['categoryName'], 'in', 'range' => self::$_arrayCategoryName],
            [['mainCategoryName'], 'in', 'range' => self::$_arrayMainCategoryName],
            [['tableName'], 'in', 'range' => self::$_arrayTableName],
            [
                ['status'],
                'in',
                'range' => [self::STATUS_ACTIVE, self::STATUS_INACTIVE, self::STATUS_DELETED, self::STATUS_UPLOADED]
            ],
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels() {
        return [
            'id' => Yii::t('app', 'ID'),
            'userId' => Yii::t('app', 'User ID'),
            'hashName' => Yii::t('app', 'Hash Name'),
            'extension' => Yii::t('app', 'Extension'),
            'tableName' => Yii::t('app', 'Table Name'),
            'tableId' => Yii::t('app', 'Table ID'),
            'order' => Yii::t('app', 'Order'),
            'typeFile' => Yii::t('app', 'Type File'),
            'localName' => Yii::t('app', 'Local Name'),
            'hashFile' => Yii::t('app', 'Hash File'),
            'size' => Yii::t('app', 'Size'),
            'mainCategoryName' => Yii::t('app', 'Main Category Name'),
            'categoryName' => Yii::t('app', 'Category Name'),
            'url' => Yii::t('app', 'Url'),
            'width' => Yii::t('app', 'Width'),
            'height' => Yii::t('app', 'Height'),
            'length' => Yii::t('app', 'Length'),
            'status' => Yii::t('app', 'Status'),
            'createAt' => Yii::t('app', 'Create At'),
        ];
    }

    /**
     * Save video file
     * @param UploadedFile $fileObject
     */
    private function _saveVideoFile(UploadedFile $fileObject) {
        if ($this->validate()) {

            $this->save();

            $path = $this->getDirPath() . DS . $this->id . '.' . $fileObject->extension;

            if (true === $this->_mkDir() && false !== $fileObject->saveAs($path)) {
                $this->_saveInfoFile($path, self::CATEGORY_ORIGINAL);
            }
        } else {
            Yii::warning(VarDumper::dumpAsString($this->getErrors()), 'warning');
        }
    }

    /**
     * Convert to MP4
     * @param $pathOriginal
     * @param $pathVideo
     */
    private function _convertToMp4($pathOriginal, $pathVideo) {
        $command = 'ffmpeg -i ' . $pathOriginal . ' -vcodec libx264 -pix_fmt yuv420p -profile:v baseline -preset slow -crf 22 -movflags +faststart ' . $pathVideo;
        shell_exec($command);
    }

    /**
     * @return bool
     */
    private function _mkDir() {
        if (!is_dir($this->_dirPath) || !is_writable($this->_dirPath)) {
            if (false === @mkdir($this->_dirPath, 0777, true)) {
                Yii::warning(VarDumper::dumpAsString($this->_dirPath . ' - Permission denied'), 'warning');
                return false;
            }
        }

        return true;
    }

    /**
     * Save image file
     * @param UploadedFile $fileObject
     */
    private function _saveImageFile(UploadedFile $fileObject) {
        if ($this->validate()) {

            $this->save();

            $path = $this->getDirPath() . DS . $this->id . '.' . $fileObject->extension;
            //echo $path;exit;

            if (true === $this->_mkDir() && false !== $fileObject->saveAs($path)) {
                $this->_saveInfoFile($path, self::CATEGORY_ORIGINAL);

                /** @var \app\components\ImageResizeHelper $imageResize */
                $imageResize = Yii::$app->imageResize->setParams($path);

                foreach (self::$_arrayCategoryName as $category) {
                    if ($category !== self::CATEGORY_ORIGINAL) {
                        $this->id = null;
                        $this->setIsNewRecord(true);
                        if ($this->save()) {
                            $this->setDirPath();
                            $pathCategoryFile = $this->getDirPath() . DS . $this->id . '.' . $fileObject->extension;
                            if (true === $this->_mkDir()) {
                                $method = 'create' . ucfirst($category) . 'Image';
                                if (method_exists($imageResize, $method)) {
                                    $create = $imageResize->$method($pathCategoryFile);
                                    if (true !== $create) {
                                        break;
                                    } else {
                                        $this->_saveInfoFile($pathCategoryFile, $category);
                                    }
                                } else {
                                    Yii::warning(
                                            VarDumper::dumpAsString('function ' . $method . ' not found'), 'warning'
                                    );
                                }
                            }
                        } else {
                            Yii::warning(VarDumper::dumpAsString($this->getErrors()), 'warning');
                        }
                    }
                }
            }
        } else {
            Yii::warning(VarDumper::dumpAsString($this->getErrors()), 'warning');
        }
    }

    /**
     * Check needed format
     * @param $mainKey
     * @param $format
     * @return bool
     */
    private function _formatNeeded($mainKey, $format) {
        if (in_array($mainKey, self::$_arrayMainCategoryName)) {
            return true;
        }

        return false;
    }

    /**
     * Save file info
     * @param $pathFile
     * @param $categoryName
     * @param null $type
     */
    private function _saveInfoFile($pathFile, $categoryName, $type = null) {
        $file = File::findOne($this->id);
        $file->setScenario(self::SCENARIO_DEFAULT);

        $info = new \SplFileInfo($pathFile);

        //common property
        $file->userId = $this->userId;
        $file->hashName = $this->hashName;
        $file->tableName = $this->tableName;
        $file->tableId = $this->tableId;
        $file->typeFile = $type ? $type : $this->typeFile;
        $file->mainCategoryName = $this->mainCategoryName;
        $file->status = $this->status;

        //private property
        $file->localName = $info->getBasename();
        $file->extension = $info->getExtension();
        $file->categoryName = $categoryName;
        $file->size = $info->getSize();

        if ($this->_optionsExist('typeFile') == self::TYPE_IMAGE) {
            $imageInfo = getimagesize($pathFile);
            $file->width = ($this->_keyExists($imageInfo, 0) != "") ? $this->_keyExists($imageInfo, 0) : 0;
            $file->height = ($this->_keyExists($imageInfo, 1) != "") ? $this->_keyExists($imageInfo, 1) : 0;
        }

        $file->length = '';
        $file->hashFile = md5_file($pathFile);

        if (!$file->save()) {
            Yii::info(VarDumper::dumpAsString($file->getErrors()), 'show');
        }
    }

    /**
     * @param $key
     * @param null $default
     * @return null
     */
    private function _optionsExist($key, $default = null) {
        return $this->_keyExists($this->_options, $key, $default);
    }

    /**
     * @param $array
     * @param $key
     * @param null $default
     * @return null
     */
    private function _keyExists($array, $key, $default = null) {
        return isset($array[$key]) ? $array[$key] : $default;
    }

    /**
     * @return string
     */
    private function _getFilePath() {
        return $this->getDirPath() . DS . $this->localName;
    }

    private function createFile($path,$base64String) {
        $binary = base64_decode($base64String);
        header('Content-Type: image/jpeg');
        $file = fopen($path, 'w');
        if ($file) {
            fwrite($file, $binary);
        } else {
            return null;
        }
        fclose($file);
        return true;
    }

}
