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
define('DB_PASSWORD', 'GtqEPCEeDxI4');

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
define('AUTH_KEY',         'Vq]{eHl`/UlSBX]un?%>mg3(?V#|X#P0AD#4l<VsA-_an/~yi_VR>,E_4w<KUqi&');
define('SECURE_AUTH_KEY',  'd{g<4R95LSl[Yt!T3b4)^j>v;^Vymb@t403=6Z:(`#@/JzV<ADP8CmV{w6({o-+m');
define('LOGGED_IN_KEY',    'A,f7-9`Nd7+oYL}te=)hh>?+WB1Mt;W*bvk4O!VqWh4:ML*`5cCa=E{6-(cMqO]%');
define('NONCE_KEY',        '1Q`gQ}]}`NSD!2LYUnZ/ dXQ^DQ8M:d*185:O1v%HL?8O6_#fXaN?slmxtkeDty|');
define('AUTH_SALT',        'I)@kOjD^#jPP]u.$[Kv:H755v_qL=lU:)E6/19Dv~4s`4Mr^) 1N>zPQ#`8$juF,');
define('SECURE_AUTH_SALT', 'a;IQSG:MYp.H/j:14L2>)K=[$]?+nIz3<.ai-.><?h#N3Epx@:vRgIKcM{IY5CCL');
define('LOGGED_IN_SALT',   '5rIB>:Zclsy~tu+eUJabl<D^Ro}fz><vnrviefy3|j2|Eu%Ws0nnX!TT+Vr5]:C-');
define('NONCE_SALT',       'Pt%djtpPh=7>l<!ZCAD9g_>LkEGmMQp5WPp%ak7z:Xl(?aTsDgVu<5(P62Gtv)aS');

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
