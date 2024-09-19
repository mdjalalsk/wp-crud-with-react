<?php

if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

/**
 * Main class for the CRUD Operations plugin.
 *
 * @since 1.0.0.
 */
class Crud_Operation {
    /**
     * Singleton instance of the class.
     *
     * @var Crud_Operation.
     * @since 1.0.0.
     */
    private static $instance;

    /**
     * Main plugin file path.
     *
     * @var string.
     * @since 1.0.0.
     */
    public string $file;

    /**
     * Plugin version.
     *
     * @var string.
     * @since 1.0.0.
     */
    public string $version;

    /**
     * Constructor for the Crud_Operation class.
     *
     * @param string $file    The main plugin file path.
     * @param string $version The version of the plugin.
     * @since 1.0.0.
     */
    public function __construct( $file, $version = '1.0.0' ) {
        $this->file = $file;
        $this->version = $version;
        $this->define_constants();
        $this->activation_hooks();
        $this->init_hooks();
        $this->deactivation_hooks();
    }

    /**
     * Retrieves the singleton instance of the class.
     *
     * @param string $file    The main plugin file path.
     * @param string $version The version of the plugin.
     * @return Crud_Operation.
     * @since 1.0.0.
     */
    public static function get_instance( $file, $version = '1.0.0' ) {
        if ( ! isset( self::$instance ) && ! ( self::$instance instanceof Crud_Operation ) ) {
            self::$instance = new self( $file, $version );
        }
        return self::$instance;
    }

    /**
     * Define constants.
     *
     * Defines the constants for the plugin version, directory, URL, and basename.
     *
     * @since 1.0.0
     */
    public function define_constants() {
        define('CRUD_VERSION', $this->version);
        define('CRUD_PLUGIN_DIR', plugin_dir_path($this->file));
        define('CRUD_PLUGIN_URL', plugin_dir_url($this->file));
        define('CRUD_PLUGIN_BASENAME', plugin_basename($this->file));
    }

    /**
     * Activation.
     *
     * @since 1.0.0
     * @return void
     */
    public function activation_hooks() {
        register_activation_hook( $this->file, array( $this, 'activation' ) );
    }

    /**
     * Activation hook function to create a database table.
     *
     * @since 1.0.0
     */
    public function activation() {
        global $wpdb;

        $table_name = $wpdb->prefix . 'crud_table';
        $charset_collate = $wpdb->get_charset_collate();

        // SQL query to create the table with created and updated timestamps.
        $sql = "CREATE TABLE IF NOT EXISTS $table_name (
            id mediumint(9) NOT NULL AUTO_INCREMENT,
            name tinytext NOT NULL,
            email varchar(100) NOT NULL,
            created_at datetime DEFAULT CURRENT_TIMESTAMP NOT NULL,
            updated_at datetime DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP NOT NULL,
            PRIMARY KEY (id)
        ) $charset_collate;";

        // dbDelta requires including the upgrade.php file.
        require_once( ABSPATH . 'wp-admin/includes/upgrade.php' );
        dbDelta( $sql );

        // Save the plugin version in the options table for future upgrades.
        update_option( 'crud_version', $this->version );
    }

    /**
     * Initialize hooks for admin menu and deactivation.
     *
     * @since 1.0.0
     */
    public function init_hooks() {
        add_action( 'admin_menu', array( $this, 'crud_admin_menu' ) );
    }

    /**
     * Add a custom admin menu for the CRUD plugin.
     *
     * @since 1.0.0
     */
    public function crud_admin_menu() {
        add_menu_page(
            'CRUD Operations',
            'CRUD Operations',
            'manage_options',
            'crud-operations',
            array( $this, 'crud_page_content' ),
            'dashicons-admin-generic',
            6
        );
    }

    /**
     * Content for the CRUD operations admin page.
     *
     * @since 1.0.0
     */
    public function crud_page_content() {
        echo '<div class="wrap"><h1>CRUD Operations</h1></div>';
        // Here you will include the React app or any HTML for your CRUD operations.
    }

    /**
     * Register deactivation hook.
     *
     * @since 1.0.0
     * @return void
     */
    public function deactivation_hooks() {
        register_deactivation_hook( $this->file, array( $this, 'deactivation' ) );
    }

    /**
     * Deactivation hook function.
     *
     * @since 1.0.0
     */
    public function deactivation() {
        delete_option( 'crud_version' );
    }

}
