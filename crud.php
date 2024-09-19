<?php
/*
 * Plugin Name:       CRUD
 * Description:       This simple Database CRUD Operations using React.
 * Version:           1.0.1
 * Requires at least: 6.5
 * Requires PHP:      7.2
 * Author:            jalal
 * License:           GPL v2 or later
 * License URI:       https://www.gnu.org/licenses/gpl-2.0.html
 */
if ( ! defined( 'ABSPATH' ) ) {
    exit();
}
// includes class
require_once __DIR__ . '/inc/class-crud-operation.php';
function crud_init()
{
    return Crud_Operation::get_instance(__FILE__,'1.0.0');

}
crud_init();