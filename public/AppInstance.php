<?php

namespace AppInstance;

require __DIR__ . '/../vendor/autoload.php';

class AppInstance{
    // Instantiate the app
    private $settings;
    private $app;
    private static $instance;
    // = require __DIR__ . '/../src/settings.php';
    // = new \Slim\App($settings);

    private function __construct(){
      // The expensive process (e.g.,db connection) goes here.
        $this->settings = require __DIR__ . '/../src/settings.php';
        $this->app = new \Slim\App($this->settings);
    }

    public static function get_app_instance(){
        if (self::$instance == null){
            self::$instance = new AppInstance();
        }
        return self::$instance->app;
    }
}

// $test_instance = AppInstance::get_app_instance();
// var_dump($test_instance);
