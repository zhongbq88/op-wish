<?php
/**
 * The base configuration for WordPress
 *
 * The wp-config.php creation script uses this file during the
 * installation. You don't have to use the web site, you can
 * copy this file to "wp-config.php" and fill in the values.
 *
 * This file contains the following configurations:
 *
 * * MySQL settings
 * * Secret keys
 * * Database table prefix
 * * ABSPATH
 *
 * @link https://codex.wordpress.org/Editing_wp-config.php
 *
 * @package WordPress
 */

// ** MySQL settings - You can get this info from your web host ** //
/** The name of the database for WordPress */
define('DB_NAME', 'wordpress');

/** MySQL database username */
define('DB_USER', 'root');

/** MySQL database password */
define('DB_PASSWORD', '');

/** MySQL hostname */
define('DB_HOST', 'localhost');

/** Database Charset to use in creating database tables. */
define('DB_CHARSET', 'utf8mb4');

/** The Database Collate type. Don't change this if in doubt. */
define('DB_COLLATE', '');

/**#@+
 * Authentication Unique Keys and Salts.
 *
 * Change these to different unique phrases!
 * You can generate these using the {@link https://api.wordpress.org/secret-key/1.1/salt/ WordPress.org secret-key service}
 * You can change these at any point in time to invalidate all existing cookies. This will force all users to have to log in again.
 *
 * @since 2.6.0
 */
define('AUTH_KEY',         '#{.k*fW6Ob/@cOkU/uB:!}6*,9^O=T#OZI.5K.EU#e$j^<xi2`Af;dF- 1cYIf;-');
define('SECURE_AUTH_KEY',  'n|^I%+1Rl=O^hReo[Mu c15*w6AUTv|?oqvQ)rRbLl8z^$DYdw<S14uB( ^BgKgy');
define('LOGGED_IN_KEY',    'te8j9v|ss8V:OG^b3eD}kcERMrLviK#Y<^HTf]tosJ~N|%bm]5JX1A.V|RSx]l5x');
define('NONCE_KEY',        '.=Kp}7iW^~z1e$><zvMczJ@Qy^4,s[n>Eq{+.^v{N`)}Of`>V`#M_& 3lFx ]O)K');
define('AUTH_SALT',        'VEp8#)B948@o)/lK`g6CCGw)=dKUyxpV!4=U5s^sw$I)y$RkGzL$U4~R/}^CJc3`');
define('SECURE_AUTH_SALT', 'dZ6W#WE9B=Mbxo:YK,<pjEIl]+I}`AHk<5HEw((5i1R-q~e-o&4`,4G <cT_(R<@');
define('LOGGED_IN_SALT',   '4sY|^3x1nNMlegN();fp!A;P0j+./T7AMT$a=[P~WXss0@O-cfGa(5bnN9&YmJp}');
define('NONCE_SALT',       '>)jbEN1#pTDD9@!@eLO_h]ed.VIa~+`-5bX1B~>/P>Li&)1Q,0,5)v#vCs;#~~#^');

/**#@-*/

/**
 * WordPress Database Table prefix.
 *
 * You can have multiple installations in one database if you give each
 * a unique prefix. Only numbers, letters, and underscores please!
 */
$table_prefix  = 'wp_';

/**
 * For developers: WordPress debugging mode.
 *
 * Change this to true to enable the display of notices during development.
 * It is strongly recommended that plugin and theme developers use WP_DEBUG
 * in their development environments.
 *
 * For information on other constants that can be used for debugging,
 * visit the Codex.
 *
 * @link https://codex.wordpress.org/Debugging_in_WordPress
 */
define('WP_DEBUG', false);

/* That's all, stop editing! Happy blogging. */

/** Absolute path to the WordPress directory. */
if ( !defined('ABSPATH') )
	define('ABSPATH', dirname(__FILE__) . '/');

/** Sets up WordPress vars and included files. */
require_once(ABSPATH . 'wp-settings.php');
