<?php
/**
 * The base configuration for WordPress
 *
 * The wp-config.php creation script uses this file during the installation.
 * You don't have to use the website, you can copy this file to "wp-config.php"
 * and fill in the values.
 *
 * This file contains the following configurations:
 *
 * * Database settings
 * * Secret keys
 * * Database table prefix
 * * ABSPATH
 *
 * @link https://developer.wordpress.org/advanced-administration/wordpress/wp-config/
 *
 * @package WordPress
 */

// ** Database settings - You can get this info from your web host ** //
/** The name of the database for WordPress */
define( 'DB_NAME', 'if0_38603078_wp988' );

/** Database username */
define( 'DB_USER', '38603078_5' );

/** Database password */
define( 'DB_PASSWORD', '@QSp263.3y' );

/** Database hostname */
define( 'DB_HOST', 'sql304.byetcluster.com' );

/** Database charset to use in creating database tables. */
define( 'DB_CHARSET', 'utf8mb4' );

/** The database collate type. Don't change this if in doubt. */
define( 'DB_COLLATE', '' );

/**#@+
 * Authentication unique keys and salts.
 *
 * Change these to different unique phrases! You can generate these using
 * the {@link https://api.wordpress.org/secret-key/1.1/salt/ WordPress.org secret-key service}.
 *
 * You can change these at any point in time to invalidate all existing cookies.
 * This will force all users to have to log in again.
 *
 * @since 2.6.0
 */
define( 'AUTH_KEY',         'fgdwkaar1vjhztao9rrpqemsiau2afuonxyp7bx2xac41ok0sfjskpzhinl8rsnx' );
define( 'SECURE_AUTH_KEY',  'cpimma5ofqohddw7uwhtikaedjkfc7kirpv2qgf7fv9yrno4ubc1ebz8u8orzme9' );
define( 'LOGGED_IN_KEY',    'kuknzqkqjq3raruwmsteq8qlfnhokfmokcczc7bqaguwvde6wifg2ewy7mzszckk' );
define( 'NONCE_KEY',        '0pn2knggeuxl7x8jynq0ztbk3yiin9lyun8ogthfjfq40lg2rgug14pdlvppxbsh' );
define( 'AUTH_SALT',        'ikiqtcsapzn6arbcl173p7uupvspbn42twnkdrqcythivingwasnw68lsv4wjb36' );
define( 'SECURE_AUTH_SALT', 'hd3ovrvr5bjtxm1unqx3gvvahznbhzgdkmxfsuq5u0uyax7doo3kkvf2pwzyz2dk' );
define( 'LOGGED_IN_SALT',   'aqzsab9j2i422kw7jex6auajsq3q2oo75jclfa0d2oq5zo7yqsqvi2kpgbxonawv' );
define( 'NONCE_SALT',       'gw3emozrtug9tte0m3ygtwezfe5ebdij1usie7f2iybwstajctps3vw6we26tt0e' );

/**#@-*/

/**
 * WordPress database table prefix.
 *
 * You can have multiple installations in one database if you give each
 * a unique prefix. Only numbers, letters, and underscores please!
 *
 * At the installation time, database tables are created with the specified prefix.
 * Changing this value after WordPress is installed will make your site think
 * it has not been installed.
 *
 * @link https://developer.wordpress.org/advanced-administration/wordpress/wp-config/#table-prefix
 */
$table_prefix = 'wp7k_';

/**
 * For developers: WordPress debugging mode.
 *
 * Change this to true to enable the display of notices during development.
 * It is strongly recommended that plugin and theme developers use WP_DEBUG
 * in their development environments.
 *
 * For information on other constants that can be used for debugging,
 * visit the documentation.
 *
 * @link https://developer.wordpress.org/advanced-administration/debug/debug-wordpress/
 */
define( 'WP_DEBUG', false );

/* Add any custom values between this line and the "stop editing" line. */


define('ALLOW_UNFILTERED_UPLOADS', true);
/* That's all, stop editing! Happy publishing. */

/** Absolute path to the WordPress directory. */
if ( ! defined( 'ABSPATH' ) ) {
	define( 'ABSPATH', __DIR__ . '/' );
}

/** Sets up WordPress vars and included files. */
require_once ABSPATH . 'wp-settings.php';
