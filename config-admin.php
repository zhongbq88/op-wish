<?php
// HTTP

define('HTTP_SERVER', 'http://52.38.200.51/admin/');
define('HTTP_CATALOG', 'http://52.38.200.51/');

// HTTPS
define('HTTPS_SERVER', 'https://52.38.200.51/admin/');
define('HTTPS_CATALOG', 'https://52.38.200.51/');

// DIR
define('DIR_APPLICATION', '/opt/bitnami/apps/op-wish/admin/');
define('DIR_SYSTEM', '/opt/bitnami/apps/op-wish/system/');
define('DIR_IMAGE', '/opt/bitnami/apps/op-wish/image/');
define('DIR_STORAGE', '/opt/bitnami/apps/op-wish/storage/');
define('DIR_CATALOG', '/opt/bitnami/apps/op-wish/catalog/');

define('DIR_LANGUAGE', DIR_APPLICATION . 'language/');
define('DIR_TEMPLATE', DIR_APPLICATION . 'view/template/');
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
define('DB_PASSWORD', 'GtqEPCEeDxI4');
define('DB_DATABASE', 'opencart');

define('DB_PORT', '3306');
define('DB_PREFIX', 'oc_');

// OpenCart API
define('OPENCART_SERVER', 'https://www.opencart.com/');
