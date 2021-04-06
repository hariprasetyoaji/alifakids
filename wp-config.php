<?php
define('WP_AUTO_UPDATE_CORE', 'minor');
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
define( 'DB_NAME', 'root' );

/** MySQL database username */
define( 'DB_USER', 'root' );

/** MySQL database password */
define( 'DB_PASSWORD', '' );

/** MySQL hostname */
define( 'DB_HOST', 'localhost' );

/** Database Charset to use in creating database tables. */
define( 'DB_CHARSET', 'utf8mb4' );

/** The Database Collate type. Don't change this if in doubt. */
define( 'DB_COLLATE', '' );

/**#@+
 * Authentication Unique Keys and Salts.
 *
 * Change these to different unique phrases!
 * You can generate these using the {@link https://api.wordpress.org/secret-key/1.1/salt/ WordPress.org secret-key service}
 * You can change these at any point in time to invalidate all existing cookies. This will force all users to have to log in again.
 *
 * @since 2.6.0
 */
define( 'AUTH_KEY',         'Ro?$kUh;qr|HEzG;${lHAU!qx)C:2ApW;@y]vX%y1BQId3`Aqx9;(}bY&`|4T8up' );
define( 'SECURE_AUTH_KEY',  'O8RsuA3C6^|,>O.[~Q)F&8FxSA$0W_Nqc3*{{QXAulXkNyrT >|/sa}5rLLzd>W}' );
define( 'LOGGED_IN_KEY',    '4NwQ#9>Z^G^4[q5n (8/djYKQG-Ax|9++CH{=I}=)b$#v39NSUX_x(IzZncKn~/n' );
define( 'NONCE_KEY',        'M?ri|)NUXRAl.Le[sf!<wUDl-lJ`E/A8=IoB)[Gi])7~Q-~ATp1vC $>>|0$D+8y' );
define( 'AUTH_SALT',        '=(*z_~l Eo724 QEF=EAlD3bobNO&?j>~z ~(hMI6bxz.-uAbVk>W0#01lA-*W]K' );
define( 'SECURE_AUTH_SALT', '-r<u3mf5|a:AnM%L_muO,phe_Wz8:bNKB0[oAt9`Cr6|FJw:;p^ M,3;aW(<8g3q' );
define( 'LOGGED_IN_SALT',   'oJ01GX=1C^]Y_@w9Z)ry`1yB!&BU}c1#EiJ;,n $9Oq(g2 saU9(0WHgXob2gb#S' );
define( 'NONCE_SALT',       '~U!znB@Q*O+@,(+hEc%zd@:@p~]wU{{E{vI0?*O>84@nBQ}/i;vBCWE4+vdqr%_Q' );

define('JWT_AUTH_SECRET_KEY', 'C&EClY]shq-)r-Tf-N,(+`]MFa$+[F69G|o)y0E5y+wWEXH1uiQY<kErjOe:2wS|');
define('JWT_AUTH_CORS_ENABLE', true);

/**#@-*/

/**
 * WordPress Database Table prefix.
 *
 * You can have multiple installations in one database if you give each
 * a unique prefix. Only numbers, letters, and underscores please!
 */
$table_prefix = 'ak_';

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
define( 'WP_DEBUG', true );

/* That's all, stop editing! Happy publishing. */

/** Absolute path to the WordPress directory. */
if ( ! defined( 'ABSPATH' ) ) {
	define( 'ABSPATH', dirname( __FILE__ ) . '/' );
}

/** Sets up WordPress vars and included files. */
require_once( ABSPATH . 'wp-settings.php' );
