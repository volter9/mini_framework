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
    'database' => array(
        'autoload' => true,
        'default'  => array(
            'name'     => '',
            'host'     => 'localhost',
            'user'     => 'root',
            'password' => '',
            'charset'  => 'utf8'
        )
    ),
    
    /**
     * Templates configuration
     * 
     * - directory - where templates are living
     * - template - which template is chosen
     * - layout - name of layout in current template
     */
    'templates' => array(
        'directory' => base_path('views/'),
        'template'  => 'default',
        'layout'    => 'main'
    ),
    
    /**
     * Routing
     * 
     * - base_url - full http:// link to your website 
     *              (not really used, it used with `url` function)
     * - symbols - readable way to capture parameters in URL's
     */
    'routing' => array(
        'base_url' => 'http://ffframework.dev/',
        'symbols' => array(
            '/:any' => '/?([\d\w\-_]+)',
            '/:num' => '/?(\d+)'
        )
    ),
    
    /**
     * Hooks - they're are loaded before routing and after autoload 
     *         (autoload.files)
     */
    'hooks' => array(
        app_path('bootstrap'),
        app_path('routes')
    ),
    
    /**
     * Autoload resources
     * 
     * - models - autoload models
     * - files - autoload any PHP files
     */
    'autoload' => array(
        'models' => array(),
        'files'  => array()
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
        'validators' => 'validators'
    )
);