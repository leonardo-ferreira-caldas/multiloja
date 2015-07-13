<?php

require 'application/core/Autoload.php';
require 'application/core/Config.php';

date_default_timezone_set("America/Sao_Paulo");

error_reporting(-1);

ini_set("display_errors", "On");

Config::load("app.config.json");

define("DS", DIRECTORY_SEPARATOR);
define("APP_PATH", Config::getBasePath() . DS . Config::getAppPath());
define("BASE_PATH", Config::getBasePath());
define("BASE_URL", Config::getBaseUrl());

Autoload::init();

Router::call();