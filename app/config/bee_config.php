<?php

// Saber si estamos trabajando de forma local o remota
define('IS_LOCAL'   , in_array($_SERVER['REMOTE_ADDR'], ['127.0.0.1', '::1']));
define('PREPROS'    , true); // Activar en caso de trabajar el desarrollo en prepros como servidor local
define('IS_DEMO'    , false); // establece si el sistema es un sitio demo o no para limitar funcionalidades

// Definir el uso horario o timezone del sistema
date_default_timezone_set('America/Mexico_City');

// Lenguaje
define('SITE_LANG'   , $this->lng);

// Versión de la aplicación
define('BEE_NAME'    , $this->framework); // Viene desde Bee.php
define('BEE_VERSION' , $this->version);   // Viene desde Bee.php
define('SITE_NAME'   , 'Ordify');    // Configurado desde Bee.php
define('SITE_VERSION', '1.3.0');          // Configurado desde Bee.php

// Ruta base de nuestro proyecto
// Configurar según sea necesario en tu proyecto
define('BASEPATH'   , IS_LOCAL ? '/sistemas/ordify/' : '/');

// Sal del sistema
define('AUTH_SALT'  , 'BeeFramework<3');

// Puerto y la URL del sitio
define('PORT'       , '8848');
define('PROTOCOL'   , isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] === 'on' ? "https" : "http"); // Detectar si está en HTTPS o HTTP
define('HOST'       , $_SERVER['HTTP_HOST'] === 'localhost' ? (PREPROS ? 'localhost:'.PORT : 'localhost') : $_SERVER['HTTP_HOST']); // Dominio o host localhost.com tudominio.com
define('REQUEST_URI', $_SERVER["REQUEST_URI"]); // Parametros y ruta requerida
define('URL'        , PROTOCOL.'://'.HOST.BASEPATH); // URL del sitio
define('CUR_PAGE'   , PROTOCOL.'://'.HOST.REQUEST_URI); // URL actual incluyendo parametros get

// Las rutas de directorios y archivos
define('DS'         , DIRECTORY_SEPARATOR);
define('ROOT'       , getcwd().DS);

define('APP'        , ROOT.'app'.DS);
define('CLASSES'    , APP.'classes'.DS);
define('CONFIG'     , APP.'config'.DS);
define('CONTROLLERS', APP.'controllers'.DS);
define('FUNCTIONS'  , APP.'functions'.DS);
define('MODELS'     , APP.'models'.DS);
define('LOGS'       , APP.'logs'.DS);

define('TEMPLATES'  , ROOT.'templates'.DS);
define('INCLUDES'   , TEMPLATES.'includes'.DS);
define('MODULES'    , TEMPLATES.'modules'.DS);
define('VIEWS'      , TEMPLATES.'views'.DS);

// Rutas de recursos y assets absolutos
define('IMAGES_PATH', ROOT.'assets'.DS.'images'.DS);

// Rutas de archivos o assets con base URL
define('ASSETS'     , URL.'assets/');
define('CSS'        , ASSETS.'css/');
define('FAVICON'    , ASSETS.'favicon/');
define('FONTS'      , ASSETS.'fonts/');
define('IMAGES'     , ASSETS.'images/');
define('JS'         , ASSETS.'js/');
define('PLUGINS'    , ASSETS.'plugins/');
define('UPLOADS'    , ROOT.DS.'assets'.DS.'uploads'.DS);
define('UPLOADED'   , ASSETS.'uploads/');

// Credenciales de la base de datos
// Set para conexión local o de desarrollo
define('LDB_ENGINE' , 'mysql');
define('LDB_HOST'   , 'localhost');
define('LDB_NAME'   , 'db_ordify');
define('LDB_USER'   , 'root');
define('LDB_PASS'   , '');
define('LDB_CHARSET', 'utf8');

// Set para conexión en producción o servidor real
define('DB_ENGINE'  , 'mysql');
define('DB_HOST'    , 'localhost');
define('DB_NAME'    , '___NOMBRE DB EN PRODUCCIÓN___');
define('DB_USER'    , '___NOMBRE DB USER EN PRODUCCIÓN___');
define('DB_PASS'    , '___PASSWORD DB EN PRODUCCIÓN___');
define('DB_CHARSET' , 'utf8');

// El controlador por defecto / el método por defecto / y el controlador de errores por defecto
define('DEFAULT_CONTROLLER'      , 'home');
define('DEFAULT_ERROR_CONTROLLER', 'error');
define('DEFAULT_METHOD'          , 'index');
