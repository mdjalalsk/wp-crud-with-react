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
         add_action('admin_enqueue_scripts', array( $this, 'crud_admin_enqueue_scripts' ) );
        add_action('rest_api_init', array( $this, 'register_rest_routes' ));

    }

    /**
     * Add a custom admin menu for the CRUD plugin.
     *
     * @since 1.0.0
     */
    public function crud_admin_menu() {
        add_menu_page(
            'CRUD Operation',
            'CRUD Operation',
            'manage_options',
            'crud-operation',
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
        echo '<div class="wrap">';
        echo '<div id="react-app"> </div>';
        echo '</div>';
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

    /**
     * Enqueue scripts for the admin area.
     *
     * Enqueues the React application script and localizes data for use in the script.
     *
     * @since 1.0.0
     */
    public function crud_admin_enqueue_scripts($hook)
    {
        if ($hook === 'toplevel_page_crud-operation') {
            $react=include CRUD_PLUGIN_DIR . 'build/index.asset.php';
//
            wp_enqueue_style('crud-react-style', CRUD_PLUGIN_URL .'./build/style-index.css',array(),$react['version']);
            wp_enqueue_script('crud-react-script', CRUD_PLUGIN_URL . 'build/index.js', $react['dependencies'],$react['version'], ['in_footer' => true]);

            wp_localize_script('crud-react-script', 'crudApi', array(
                'root' => esc_url_raw(rest_url()),
                'nonce' => wp_create_nonce('wp_rest'),
            ));
        }
    }
    public function register_rest_routes()
    {
        register_rest_route('crud/v1', '/items', array(
            'methods' => 'GET',
            'callback' => [$this, 'get_items'],
            'permission_callback' => [$this, 'check_permission']
        ));
        register_rest_route('crud/v1', '/item', array(
            'methods' => 'POST',
            'callback' => [$this, 'create_item'],
            'permission_callback' => [$this, 'check_permission']
        ));

        register_rest_route('crud/v1', '/item/(?P<id>\d+)', array(
            'methods' => 'PUT',
            'callback' => [$this, 'update_item'],
            'permission_callback' => [$this, 'check_permission']
        ));
        register_rest_route('crud/v1', '/item/(?P<id>\d+)', array(
            'methods' => 'DELETE',
            'callback' => [$this, 'delete_item'],
            'permission_callback' => [$this, 'check_permission']
        ));
    }

    /**
     * Check permission
     * @return true
     */

    public function check_permission()
    {
        return true;
    }
    /**
     * Retrieve items from the database with pagination.
     *
     * @param WP_REST_Request $request The request object.
     * @return WP_REST_Response Response object containing items and total count.
     * @since 1.0.0
     */
    // pagination manage backend API.
    /**
    public function get_items( $request ) {
        global $wpdb;
        $table_name = $wpdb->prefix . 'crud_table';

        $page = max(1, intval( wp_unslash( $request['page'] ) ?? 1));
        $per_page = max(1, min(100, intval(wp_unslash( $request['per_page'] ) ?? 5 )));
        $offset = ($page - 1) * $per_page;
        $items = $wpdb->get_results( $wpdb->prepare("SELECT * FROM $table_name LIMIT %d OFFSET %d", $per_page, $offset) );
        $total_items = $wpdb->get_var("SELECT COUNT(*) FROM $table_name");

        return rest_ensure_response( array(
            'items' => $items,
            'total' => $total_items,
            'total_pages' => ceil($total_items / $per_page),
            'current_page' => $page,
        ));
    }
*/
     //pagination manage frontend API.
    public function get_items( $request ) {
        global $wpdb;
        $table_name = $wpdb->prefix . 'crud_table';
        $items = $wpdb->get_results("SELECT * FROM $table_name");
        $total_items = count( $items );
        return rest_ensure_response( array(
            'items' => $items,
            'total' => $total_items,
        ));
    }


    /**
     * Create a new item in the database.
     *
     * @param WP_REST_Request $request The request object containing item data.
     * @return WP_REST_Response Response object confirming item creation.
     * @since 1.0.0
     */
    public function create_item( $request ) {
        global $wpdb;
        $table_name = $wpdb->prefix . 'crud_table';

        $name = sanitize_text_field( wp_unslash( $request['name'] ) );
        $email = sanitize_email( wp_unslash( $request['email'] ) );

        $wpdb->insert(
            $table_name,
            array(
                'name' => $name,
                'email' => $email,
            ),
            array(
                '%s',
                '%s',
            )
        );

        return rest_ensure_response( array(
            'message' => 'Item created successfully!',
            'item_id' => $wpdb->insert_id,
        ));
    }

    /**
     * Update an existing item in the database.
     *
     * @param WP_REST_Request $request The request object containing updated item data.
     * @return WP_REST_Response Response object confirming item update.
     * @since 1.0.0
     */
    public function update_item( $request ) {
        global $wpdb;
        $table_name = $wpdb->prefix . 'crud_table';

        $id = (int) wp_unslash( $request['id'] );
        $name = sanitize_text_field( wp_unslash( $request['name'] ) );
        $email = sanitize_email( wp_unslash( $request['email'] ) );

        $wpdb->update(
            $table_name,
            array(
                'name' => $name,
                'email' => $email,
            ),
            array( 'id' => $id ),
            array(
                '%s',
                '%s',
            ),
            array( '%d' )
        );

        return rest_ensure_response( array(
            'message' => 'Item updated successfully!',
        ));
    }
    /**
     * Delete an item from the database.
     *
     * @param WP_REST_Request $request The request object containing the ID of the item to delete.
     * @return WP_REST_Response Response object confirming item deletion.
     * @since 1.0.0
     */
    public function delete_item( $request ) {
        global $wpdb;
        $table_name = $wpdb->prefix . 'crud_table';

        $id = (int) wp_unslash( $request['id'] );

        $wpdb->delete(
            $table_name,
            array( 'id' => $id ),
            array( '%d' )
        );

        return rest_ensure_response( array(
            'message' => 'Item deleted successfully!',
        ));
    }



}