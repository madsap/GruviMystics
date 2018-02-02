<?php

namespace app\components;

use \yii\base\Component;
use \yii\helpers\Json;

/**
 * Class StringHelper
 * @package app\components
 * @author Aleksandr Mokhonko
 * Date: 30.12.15
 */
class StringHelper extends Component
{
    /**
     * Convert controller|action name to route
     * @param $name
     * @return string
     */
    public static function route($name)
    {
        preg_match_all('/((?:^|[A-Z])[a-z]+)/', $name, $matches);
        $result = [];
        array_walk($matches[0], function($item) use (&$result) {
            $result[] = strtolower($item);
        });

        return implode('-', $result);
    }

    /**
     * @param $attributes
     * @param $default
     * @return void
     */
    public static function removeNull(&$attributes, $default = [])
    {
        foreach ($attributes as $attribute => &$value) {
            if ($value === null) {
                if (isset($default[$attribute])) {
                    $value = $default[$attribute];
                } else {
                    $value = '';
                }
            }
        }
    }

    /**
     * Convert array to JSON string
     * @param $value
     * @return string
     */
    public static function convertArray($value)
    {
        return  is_array($value) ? Json::encode($value) : $value;
    }
}