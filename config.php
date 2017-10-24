<?php
// HTTP

define('HTTP_SERVER', 'https://127.0.0.1/');

// HTTPS
define('HTTPS_SERVER', 'https://127.0.0.1/');

// DIR
define('DIR_APPLICATION', 'D:/xampp/htdocs/op/oc-wish/catalog/');
define('DIR_SYSTEM', 'D:/xampp/htdocs/op/oc-wish/system/');
define('DIR_IMAGE', 'D:/xampp/htdocs/op/oc-wish/image/');
define('DIR_STORAGE', 'D:/xampp/htdocs/op/oc-wish/storage/');

define('DIR_LANGUAGE', DIR_APPLICATION . 'language/');
define('DIR_TEMPLATE', DIR_APPLICATION . 'view/theme/');
define('DIR_CONFIG', DIR_SYSTEM . 'config/');
define('DIR_CACHE', DIR_STORAGE . 'cache/');
define('DIR_DOWNLOAD', DIR_STORAGE . 'download/');
define('DIR_LOGS', DIR_STORAGE . 'logs/');
define('DIR_MODIFICATION', DIR_STORAGE . 'modification/');
define('DIR_SESSION', DIR_STORAGE . 'session/');
define('DIR_UPLOAD', DIR_STORAGE . 'upload/');

// DB
define('DB_DRIVER', 'mysqli');
define('DB_HOSTNAME', 'localhost');

define('DB_USERNAME', 'root');
define('DB_PASSWORD', '');
define('DB_DATABASE', 'opcart_wish');

define('DB_PORT', '3306');
define('DB_PREFIX', 'oc_');

//shopify
define('REDIRECTION_URL', HTTPS_SERVER.'index.php?route=shopify/connect');
define('SHOPIFY_APP_API_KEY', 'b88d44a8a3808e07ac0d4ee572fed9e8');
define('SHOPIFY_APP_SHARED_SECRET', 'caef0591baf13066c9c76b178a4471eb');
