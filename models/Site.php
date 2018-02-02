<?php

namespace app\models;


class Site extends \yii\db\ActiveRecord
{
   public static function done_json($data = [], $status = "ok", $message = ""){
        \Yii::$app->response->format = \yii\web\Response::FORMAT_JSON;
        return ['data' => $data, 'status' => $status, 'message' => $message];
   }
   
   public static function get_error_summary($errors){
        $message = "";
        foreach ($errors as $error){
            $message .= implode(", ", $error).'; ';
        }
        return $message;
   }
   
   public static function is_ajax_request(){
        return (!empty($_REQUEST['ajax']));
   }
}
