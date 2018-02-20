<?php

// BEGIN iThemes Security - Do not modify or remove this line
// iThemes Security Config Details: 2
define( 'DISALLOW_FILE_EDIT', true ); // Disable File Editor - Security > Settings > WordPress Tweaks > File Editor
define( 'FORCE_SSL_ADMIN', true ); // Redirect All HTTP Page Requests to HTTPS - Security > Settings > Secure Socket Layers (SSL) > SSL for Dashboard
// END iThemes Security - Do not modify or remove this line

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

if(file_exists(dirname(__FILE__).'/local-config.php')):
	include(dirname(__FILE__).'/local-config.php');
	define('WP_ENV', 'local');
elseif(file_exists(dirname(__FILE__).'/develop-config.php')):
	include(dirname(__FILE__).'/develop-config.php');
	define('WP_ENV', 'develop');
else:
	define('WP_ENV', 'production');
	define('DB_NAME', 'database_name_here');
	define('DB_USER', 'username_here');
	define('DB_PASSWORD', 'password_here');
	define('DB_HOST', 'localhost');
	define('DB_CHARSET', 'utf8');
	define('DB_COLLATE', '');
endif;

define('AUTH_KEY',         'zg9qOh)V&0=K:S@^sS]n[H.FI;b/;0KY{u;/W3$/F2-eGysEUBad&5v/6h^w:30i');
define('SECURE_AUTH_KEY',  '<5<8zmaJnEH2=wf#]0uc!Dc}U3>ZmbF0[CD|%oeH6tPr3TOZZ1P`Set&9V6w%P.-');
define('LOGGED_IN_KEY',    'ipm^NBvljs8ljlM5FwU>Y_T[?]L5cYb0Jw#xkC[<ExLd#:OF_:Rg:sd6BgVz$_u_');
define('NONCE_KEY',        'bpt!(E,[2G0sM2s1P!w*;Bm2J|WBxd@71H$`/~G9%U)/51C)+psd;v:[~Ox0X,M.');
define('AUTH_SALT',        'qBr?{{)$be|1]jyPB9 e}R;zIxBiJk~}bs(!+~>8NTCxz9-!Zyny!E/mEr>voM:u');
define('SECURE_AUTH_SALT', ']C!2`~^CFhAEGiU){W>mv~?d<QqFt_*&QwSKnjsL 1<t!MwSsoa07Vzf#owu [gK');
define('LOGGED_IN_SALT',   ',PS+5hdp/<k51Z,4d}n`c z#Et;l8V<T*>y9pnMPs!T U}m(E,MC)<fIgw(X]TyY');
define('NONCE_SALT',       'kDrdeKc!brjoNGI%2QO73)SmBb]=1@q6SgeRJu**_`Y~:T&I^4Ak u$FO,MC~Op6');

$table_prefix  = 'trustid_';

define('WPLANG', '');
define('WP_DEBUG', true);

/* Absolute path to the WordPress directory. */
if (!defined('ABSPATH')) define('ABSPATH', dirname(__FILE__) . '/');

/* Change location of wp-content folder */
define('WP_CONTENT_DIR', $_SERVER['DOCUMENT_ROOT'] . '/content');
define('WP_CONTENT_URL', 'http://' . $_SERVER['HTTP_HOST'] . '/content');

/* Limit post revisions */
define('WP_POST_REVISIONS', 5);

/** Sets up WordPress vars and included files. */
require_once(ABSPATH . 'wp-settings.php');
