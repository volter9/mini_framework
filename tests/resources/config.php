<?php

/**
 * App's config
 */

return array(
    /**
     * Database configuration
     * 
     * - autoload - automatically connect on boot to database
     */
    'db' => array(
        'autoload' => true,
        'default'  => array(
            'dsn' => 'sqlite::memory:'
        )
    ),
    
    /**
     * Templates configuration
     * 
     * - directory - where templates are living
     * - template - which template is chosen
     * - layout - name of layout in current template
     */
    'view' => array(
        'directory' => app\base_path('resources/views'),
        'layout'    => 'layout'
    ),
    
    /**
     * Routing
     * 
     * - base_url - full http:// link to your website 
     *              (not really used, it used with `url` function)
     * - symbols - readable way to capture parameters in URL's
     */
    'router' => array(
        'base_url' => 'http://ffframework.dev/',
        'symbols' => array(
            '/:any' => '/?([\d\w\-_]+)',
            '/:num' => '/?(\d+)'
        )
    ),
    
    'autoload' => array(
        'modules' => array(
            'router', 'events', 'view', 'db', 'input', 'i18n', 
            'validation', 'pagination'
        )
    ),
    
    /**
     * i18n - Internationalization
     * 
     * - default - default language
     */
    'i18n' => array(
        'default' => 'ru_RU'
    ),
    
    /**
     * Validation
     * 
     * - validators - path to validators
     */
    'validation' => array(
        'validators' => 'resources/validators'
    )
);