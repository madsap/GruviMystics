<?php

namespace app\components;

use \yii\web\Controller;

/**
 * Class MainController
 * @package app\components
 * @author Aleksandr Mokhonko
 * Date: 13.05.16
 *
 * @property array $_filesRequest
 */
class MainController extends Controller
{
    private $_filesRequest = [];

    /**
     * @param $key
     * @return void
     */
    protected function _convertFiles($key)
    {
        $files = isset($_FILES[$key]) ? $_FILES[$key] : null;
        if ($files !== null) {
            
            if(!is_array($files['name'])){
                $this->_filesRequest[$key][0] = [
                            'name'      => $files['name'],
                            'type'      => $files['type'],
                            'tmp_name'  => $files['tmp_name'],
                            'error'     => $files['error'],
                            'size'      => $files['size'],
                        ];
                return;
            }
            
            
            foreach($files['name'] as $key => $data) {
                $i = 0;
                if(is_array($data)) {
                    $countFiles = count($files['name'][$key]);
                    for (; $i < $countFiles; ++$i) {
                        $this->_filesRequest[$key][$i] = [
                            'name'      => $files['name'][$key][$i],
                            'type'      => $files['type'][$key][$i],
                            'tmp_name'  => $files['tmp_name'][$key][$i],
                            'error'     => $files['error'][$key][$i],
                            'size'      => $files['size'][$key][$i],
                        ];
                    }
                } else {
                    $this->_filesRequest[$key][$i] = [
                        'name'      => $files['name'][$key],
                        'type'      => $files['type'][$key],
                        'tmp_name'  => $files['tmp_name'][$key],
                        'error'     => $files['error'][$key],
                        'size'      => $files['size'][$key],
                    ];
                }
            }
        }
    }

    /**
     * @return array
     */
    protected function _getFiles($key = null)
    {
        $this->_convertFiles($key);
        return $this->_filesRequest;
    }
}