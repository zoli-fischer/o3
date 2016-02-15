<?php

/**
 * MySql database module for O3 Engine
 *
 * Config file
 *
 * @package o3
 * @link    todo: add url
 * @author  Zotlan Fischer <zoli_fischer@yahoo.com>
 * @license http://opensource.org/licenses/gpl-license.php GNU Public License
 */

use o3\config as config;

/** Connect to mysql server after O3 initialization */
config\def('O3_MYSQLI_AUTOCONNECT',false);

/** MySql server's host name */
config\def('O3_MYSQLI_HOST',ini_get("mysqli.default_host"));

/** MySql server auth. username */
config\def('O3_MYSQLI_USER',ini_get("mysqli.default_user"));

/** MySql server auth. password */
config\def('O3_MYSQLI_PASSWORD',ini_get("mysqli.default_pw"));

/** MySql default database to select */
config\def('O3_MYSQLI_DB','');

/** MySql default port */
config\def('O3_MYSQLI_PORT',ini_get("mysqli.default_port"));

/** MySql default socket */
config\def('O3_MYSQLI_SOCKET',ini_get("mysqli.default_socket"));

/** MySql table name prefiex */
config\def('O3_MYSQLI_TABLE_PREFIX','');

/** Show performed mysqli queries in debug console if debugging allowed */
config\def('O3_MYSQLI_DEBUG_QUERIES',false);

?>