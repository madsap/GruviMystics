<?php
/**
 * @author Aleksandr Mokhonko
 * Date: 03.07.15
 *
 * @uses Apache2 module that processes X-SENDFILE https://github.com/nmaier/mod_xsendfile
 * Install Ubuntu
 * sudo apt-get update
 * sudo apt-get install libapache2-mod-xsendfile
 * sudo service apache2 restart
 *
 * Example Virtual Host
 * <VirtualHost *:80>
 *		ServerName eleveight.loc
 *		DocumentRoot /var/www/coded-friends.com/
 *		XSendFile On
 *		XSendFilePath /var/www/coded-friends.com/app/
 * </VirtualHost>
 *
 */

namespace app\controllers;

use \Yii;
use \yii\web\NotFoundHttpException;
use \yii\web\Response;
use \app\components\MainController;
use \app\models\File;


/**
 * Class FileController
 * @package app\controllers
 */
class FileController extends MainController
{
	public function behaviors()
	{
		return [/*
			'authenticate' => [
				'class' => '\app\modules\api_v1\filters\AuthenticateFilter',
				'only' => ['remove'],
			]*/
		];
	}

	public function init()
	{
		$model = new File();
		$model->validate();
		parent::init();
	}

	/**
	 * Show thumbnail
	 * @param $id
	 */
	public function actionSmall($id)
	{
		$this->_showFile(File::CATEGORY_SMAll, $id);
	}

	/**
	 * @param $id
	 */
	public function actionLarge($id)
	{
		$this->_showFile(File::CATEGORY_LARGE, $id);
	}

	/**
	 * @param $id
	 */
	public function actionMiddle($id)
	{
		$this->_showFile(File::CATEGORY_MIDDLE, $id);
	}

	/**
	 * @param $id
	 */
	public function actionOriginal($id)
	{
		$this->_showFile(File::CATEGORY_ORIGINAL, $id);
	}

	/**
	 * @param $type
	 * @param $id
	 * @param string $status
	 * @throws NotFoundHttpException
	 * @throws \yii\base\ExitException
	 */
	private function _showFile($type, $id, $status = File::STATUS_ACTIVE)
	{
		$files = new File();
		$url = $files->getFilePathByKey($type, $id, $status);
		if(false === $url) {
			throw new NotFoundHttpException();
		}
		else {
			if(empty($files->url)) {
				if(file_exists($url)) {
                    
                    header("Pragma: public");
                    header("Expires: 0");
                    header("Cache-Control: must-revalidate, post-check=0, pre-check=0");
                    header("Cache-Control: private",false);
                    header("Content-Type: application/octet-stream");
                    header("Content-Disposition: attachment; filename=\"".basename($url)."\";" );
                    header("Content-Transfer-Encoding: binary");
                    readfile($url);exit;
                    
                    //doesn't work on live server
					Yii::$app->response->format = Response::FORMAT_RAW;
					// need Apache2 module that processes X-SENDFILE
					Yii::$app->response->xSendFile($url, basename($url), ['mimeType' => $files->getMimeType(), 'inline' => true]);
					Yii::$app->end();
				}
				else {
					throw new NotFoundHttpException();
				}
			}
			else {
				Yii::$app->response->redirect($url);
			}
		}
	}
}