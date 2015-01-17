<?php
/**
 * User login module for O3 Engine
 *
 * Config file
 *
 * @package o3
 * @link    todo: add url
 * @author  Zotlan Fischer <zoli_fischer@yahoo.com>
 * @license http://opensource.org/licenses/gpl-license.php GNU Public License
 */
 
use o3\config as config;
 
/** user table name */
config\def('O3_LOGIN_TABLE','');

/** user table name field id */
config\def('O3_LOGIN_FIELD_ID','');

/** user table name field username */
config\def('O3_LOGIN_FIELD_USERNAME','');

/** user table name field password */
config\def('O3_LOGIN_FIELD_PASSWORD','');

/** user table name field deleted */
config\def('O3_LOGIN_FIELD_DELETED','');

/** user table name field active */
config\def('O3_LOGIN_FIELD_ACTIVE','');

/** user table name field action timestamp */
config\def('O3_LOGIN_FIELD_ACTION','');

/** user table name field counting unsuccessful logins */
config\def('O3_LOGIN_FIELD_LOGIN_TRIES','');

/** user table name field last unsuccessful logins timestamp */
config\def('O3_LOGIN_FIELD_LAST_LOGIN_TRY','');

/** allow to connect cookies with database - higher security level */
config\def('O3_USE_PERSIST_TABLE',false);

/** cookie table name */
config\def('O3_PERSIST_TABLE','');

/** cookie table name field id */
config\def('O3_PERSIST_FIELD_ID','');

/** cookie table name field hash */
config\def('O3_PERSIST_FIELD_HASH','');

/** cookie table name field timestamp */
config\def('O3_PERSIST_FIELD_TIMESTAMP','');

/** recover login table name */
config\def('O3_RECOVER_TABLE','');

/** recover login table name field id */
config\def('O3_RECOVER_FIELD_ID','');

/** recover login table name field hash */
config\def('O3_RECOVER_FIELD_HASH','');

/** recover login table name field timestamp */
config\def('O3_RECOVER_FIELD_TIMESTAMP','');

?>