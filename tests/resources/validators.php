<?php

return array(
    /**
     * Require the field to be filled
     */
    'required' => function ($value) {
        return !!$value;
    },
    
    /**
     * Limit maximum length of input string
     */
    'max_length' => function ($value, $array, $length) {
        return mb_strlen($value) <= $length;
    },
    
    /**
     * Limit minimum length of input string
     */
    'min_length' => function ($value, $array, $length) {
        return mb_strlen($value) >= $length;
    },
    
    /**
     * Compare if two fields equivalent
     */
    'compare' => function ($value, $array, $to) {
        return isset($array[$to]) && $value === $array[$to];
    },
    
    /**
     * Valid if is a valid email adress
     */
    'valid_mail' => function ($value) {
        return filter_var($value, FILTER_VALIDATE_EMAIL);
    },
    
    /**
     * Valid if field's value is alpha-dash 
     * (latin characters, numbers, unserscore and hyphen)
     */
    'alpha_dash' => function ($value) {
        return preg_match('/^[\w\d\-\_]+$/i', $value);
    },
    
    /**
     * Valid if field's value is numeric
     */
    'is_numeric' => function ($value) {
        return is_numeric($value);
    }
);