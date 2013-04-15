<?php
/**
 * Core class for Serendip Event Manager Plugin
 *
 * @package Serendip Event Manager
 * @since 0.1
 *
 * file created in 2013/03/18 10:06:38.
 * LastUpdated :2013/04/01 13:13:54.
 * author iNo
 */

class Serendip_Event_Manager {
	const COUNT_POSTS_PER_PAGE = 10;
	const COUNT_DAYS_FROM_POSTS = 3;

	/**
	 * Version of this plugin
	 * @var float
	 */
	var $version = 1.0;

	/**
	 * Version of place database
	 * @var float
	 */
	var $db_version = 1.0;

	/**
	 * Table name of place
	 * @var string
	 */
	var $place_table = 'ws_serendip_event_manager_place';

	/**
	 * Directory of this plugin
	 * @var string
	 */
	var $dir;

	/**
	 * Domain name for i18n
	 * @var string
	 */
	static $domain = 'ws-serendip-event-manager';

	/**
	 * Return Query to create table
	 * @return string
	 */
	function sql() {
		// Set character set
		$char = defined( 'DB_CHARSET' ) ? DB_CHARSET : 'utf8';
		return <<<EOF
			CREATE TABLE {$this->place_table} (
				`ID` BIGINT(11) NOT NULL AUTO_INCREMENT,
				`name` VARCHAR(255) NOT NULL,
				`address` VARCHAR(255),
				`desc` TEXT,
				`tel` VARCHAR(20),
				`zip` VARCHAR(10),
				`web_url` TEXT,
				`map_url` TEXT,
				UNIQUE(`ID`)
			) ENGINE = MyISAM DEFAULT CHARSET = {$char} ;
EOF;
	}

	/**
	 * Check version and table structure on Plugin Activation
	 * @return void
	 */
	function activate() {
		global $wpdb;
		// Check if the database exists.
		$is_db_exists = $wpdb->get_var( $wpdb->prepare( "SHOW TABLES LIKE %s", $this->place_table ) );
		if ( $is_db_exists ) {
			// Check if the database id old.
			if ( $this->db_version >= $this->version ) {
				// Exit and do nothing.
				return;
			}
		}
		// Do database update.
		// Load required files.
		require_once ABSPATH . 'wp-admin/includes/upgrade.php';
		// Do dbDelta!
		dbDelta( $this->sql() );
		// Sava current db version
		update_option( 'ws_serendip_event_manager_place_db_version', $this->version );
	}

	/**
	 * Constructor for PHP4
	 * @param float $version
	 * @return void
	 */
	function Serendip_Event_Manager() {
		$this->__construct();
	}

	/**
	 * Constructor for PHP5
	 * @param float $version
	 * @return void
	 */
	function __construct() {
		global $wpdb;

		// Set directory
		$this->dir = dirname( __FILE__ );
		// Set Text Domain (for i18n)
		load_plugin_textdomain( self::$domain, false, basename( $this->dir ) . DIRECTORY_SEPARATOR . 'languages' );
		// Define table name
		$this->place_table = $wpdb->prefix . $this->place_table;
		// Get installed version
		$this->db_version = get_option( 'ws_serendip_event_manager_place_db_version', 0 );

		include_once $this->dir . '/ws-serendip-event-manager.view.php';
		include_once $this->dir . '/ws-serendip-event-manager.view-place.php';
		include_once $this->dir . '/ws-serendip-event-manager.view-options.php';

		$this->view = new Serendip_Event_Manager_View();
		$this->view_place = new Serendip_Event_Manager_Place_View();
		$this->view_option = new Serendip_Event_Manager_Options_View();

		// ***** Add actions *****
		// Add action hook to load assets
		add_action( 'admin_print_styles-post.php', array( $this, 'load_asset_for_admin' ), 1000 );
		add_action( 'admin_print_styles-post-new.php', array( $this, 'load_asset_for_admin' ), 1000 );
		add_action( 'wp_print_styles', array( $this, 'load_asset_for_page' ) );
		// Add edit menu
		add_action( 'init', array( $this->view, 'add_event_post' ) );
		// Add edit category menu
		add_action( 'init', array( $this->view, 'event_category_taxonomy' ) );
		// Add admin menu
		add_action( 'admin_menu', array( $this, 'create_admin_menu' ) );
		// Add event post page
		add_action( 'admin_init', array( $this, 'create_event_post_page' ) );
		// Add event post save action
		add_action( 'save_post', array( $this, 'save_event_post' ) );
		// Add event post list json date
		add_action( 'wp', array( $this->view_place, 'list_place_json_format' ) );
		// Add event post list columns
		add_filter( 'manage_posts_columns', array( $this->view, 'manage_event_manager_post_columns' ) );
		add_action( 'manage_posts_custom_column', array( $this->view, 'add_column'), 10, 2 );
		// Add event list display short code
		add_shortcode( 'ws_serendip_event_list', array( $this->view, 'load_new_event_list' ) );
		// Add finished event list display short code
		add_shortcode( 'ws_serendip_event_list_archive', array( $this->view, 'load_old_event_list' ) );
		// Add Options action
		add_action( 'admin_init', array( $this, 'register_event_manager_options_settings' ) );
		// Add validation to post event (TODO delete)
		//add_action( 'save_post', array( $this, 'validate_event_post' ), 10, 2 );
		// Add validation to publish event
		add_filter( 'wp_insert_post_data', array( $this, 'validate_event_publish' ), 10, 2 );
		add_filter('post_updated_messages', array( $this, 'event_manager_post_updated_messages_filter') );
	}

	/**
	 * Create Admin Panel
	 * @return void
	 */
	function create_admin_menu() {
		// Create place list page
		add_submenu_page( 'edit.php?post_type=event_manager', __( 'Places list', self::$domain ), __( 'Places list', self::$domain ), 'manage_categories', 'list_place', array( $this->view_place, 'list_place' ) );
		// Create place edit page
		add_submenu_page( 'edit.php?post_type=event_manager', __( 'Add new place', self::$domain ), __( 'Add new place', self::$domain ), 'manage_categories', 'add_place', array( $this->view_place, 'add_place' ) );
		add_submenu_page( 'edit.php?post_type=event_manager', __( 'Event Manager Options', self::$domain ), __( 'Options', self::$domain ), 'administrator', 'options', array( $this->view_option, 'event_manager_options' ) );
		//add_options_page( __( 'Event Manager Option', self::$domain ), __( 'Event Manager', self::$domain ), 'administrator', 'options', array( $this->view_option, 'event_manager_options' ) );
	}

	/**
	 * Create event post page
	 * @return void
	 */
	function create_event_post_page() {
		add_meta_box( 'meta_box_event_manager', __( 'Event day and place', self::$domain ), array( $this->view, 'add_field_event_manager' ), 'event_manager' );
	}

	/**
	 * Check has shortcode in post content
	 * @param array $shortCodes
	 * @return boolean
	 */
	function hasShortCode( $shortcode ) {
		global $wp_query;
		$posts = $wp_query->posts;
		$found = false;
		$pattern = '/\[' . preg_quote( $shortcode ) . '[^\]]*\]/im';
		foreach ( $posts as $post ) {
			if ( isset( $post->post_content ) ) {
				$post_content = $post->post_content;
				if ( preg_match( '/<!--more(.*?)?-->/', $post_content, $matches ) ) {
					$content = explode( $matches[0], $post_content, 2 );
					$post_content = $content[0];
				}
				if ( !empty( $post_content ) && preg_match( $pattern, $post_content ) ) {
					$found = true;
				}
			}
			if ( $found ) break;
		}
		unset( $posts );
		return $found;
	}

	/**
	 * Load JavaScript and CSS for page
	 * @return void
	 */
	function load_asset_for_page() {
		if ( $this->hasShortCode( 'ws_serendip_event_list' ) || $this->hasShortCode( 'ws_serendip_event_list_archive' ) ) {
			$assets_dir = plugin_dir_url( __FILE__ ) . 'assets';
			$user_css_file_path = plugin_dir_path( __FILE__ ) . 'assets' . '/ws-serendip-event-manager.user.css';
			$css_file_name = file_exists( $user_css_file_path ) ? 'ws-serendip-event-manager.user.css' : 'ws-serendip-event-manager.css';
			wp_enqueue_style(
				'ws-serendip-event-manager-style',
				$assets_dir . '/' . $css_file_name,
				false,
				$this->version,
				'screen'
			);
		}
	}

	/**
	 * Load JavaScript and CSS for admin
	 * @return void
	 */
	function load_asset_for_admin() {
		global $post_type;
		if ( 'event_manager' == $post_type ) {
			$assets_dir = plugin_dir_url( __FILE__ ) . 'assets';
			// Load JS files
			wp_enqueue_script(
				'ws-serendip-event-manager-language-script',
				$assets_dir . __( '/ws-serendip-event-manager-language.js', self::$domain ),
				false,
				$this->version,
				true
			);
			// Load JS files
			wp_enqueue_script(
				'ws-serendip-event-manager-script',
				$assets_dir . '/ws-serendip-event-manager.js',
				false,
				$this->version,
				true
			);
			wp_enqueue_script(
				'ui-datepicker',
				$assets_dir . '/jquery.ui.datepicker.min.js',
				array( 'jquery' ),
				$this->version,
				true
			);
			wp_enqueue_script(
				'datepicker-ja',
				$assets_dir . '/jquery.ui.datepicker-ja.min.js',
				array( 'jquery' ),
				$this->version,
				true
			);
			// Load CSS files
			wp_enqueue_style(
				'ui-datepicker',
				$assets_dir . '/jquery-ui-1.10.1.custom.min.css',
				false,
				$this->version,
				'screen'
			);
		}
	}

	/**
	 * Check database version for upgrade
	 * @return void
	 */
	function ws_serendip_event_manager_update_place_db_check() {
		if ( is_admin() ) {
			$this->activate();
		}
	}

	/**
	 * Get data from place table
	 * @param int $place_id
	 * @return object
	 */
	function get_place( $place_id ) {
		global $wpdb;
		return $wpdb->get_row( $wpdb->prepare( "SELECT * FROM {$this->place_table} WHERE ID = %d", $place_id ) );
	}

	/**
	 * Get all data from place table
	 * @return array
	 */
	function get_all_place() {
		global $wpdb;
		return $wpdb->get_results( "SELECT * FROM {$this->place_table}" );
	}

	/**
	 * Add new data to place table
	 * @param int $id
	 * @param string $name
	 * @param string $address
	 * @param string $desc
	 * @param string $tel
	 * @param string $zip
	 * @param string $web_url
	 * @param string $map_url
	 * @return int
	 */
	function add_place($name, $address, $desc, $tel, $zip, $web_url, $map_url ) {
		global $wpdb;
		$data = array(
			"name" => $name,
			"address" => $address,
			"desc" => $desc,
			"tel" => $tel,
			"zip" => $zip,
			"web_url" => $web_url,
			"map_url" => $map_url
		);
		$result = $wpdb->insert( $this->place_table, $data, array( '%s', '%s', '%s', '%s', '%s', '%s', '%s' ) );
		if ( $result ) {
			return $wpdb->insert_id;
		} else {
			return 0;
		}
	}

	/**
	 * Update data to place table
	 * @param int $id
	 * @param string $name
	 * @param string $address
	 * @param string $desc
	 * @param string $tel
	 * @param string $zip
	 * @param string $web_url
	 * @param string $map_url
	 * @return boolean
	 */
	function update_place( $id, $name, $address, $desc, $tel, $zip, $web_url, $map_url ) {
		global $wpdb;
		$data = array(
			"ID" => $id,
			"name" => $name,
			"address" => $address,
			"desc" => $desc,
			"tel" => $tel,
			"zip" => $zip,
			"web_url" => $web_url,
			"map_url" => $map_url
		);
		$result = $wpdb->update( $this->place_table, $data, array( 'ID' => $id ) );
		return (boolean) $result;
	}

	/**
	 * Delete data from place table
	 * @param int $id
	 * @return boolean
	 */
	function delete_place( $id ) {
		global $wpdb;
		return (boolean) $wpdb->query( $wpdb->prepare( "DELETE FROM {$this->place_table} WHERE ID = %d", $id ) );
	}

	/**
	 * Save event post
	 * @param int $post_id
	 * @return void
	 */
	function save_event_post( $post_id ) {
		global $post;
		$events_nonce = isset( $_POST['events-nonce'] ) ? $_POST['events-nonce'] : null;
		if ( !wp_verify_nonce( $events_nonce, 'events-nonce' ) ) {
			return $post_id;
		}
		if ( !current_user_can( 'edit_post', $post->ID ) ) {
			return $post_id;
		}
		if ( $post->post_type != 'event_manager' ) {
			return $post_id;
		}
		$event_start_date = $_POST['event_start_date'];
		$event_end_date = $_POST['event_end_date'];
		$event_undecided = $_POST['event_undecided'];
		$event_time = $_POST['event_time'];
		$event_place = $_POST['event_place'];

		update_post_meta( $post->ID, 'event_start_date', $event_start_date );
		update_post_meta( $post->ID, 'event_end_date', $event_end_date );
		update_post_meta( $post->ID, 'event_undecided', $event_undecided );
		update_post_meta( $post->ID, 'event_time', $event_time );
		update_post_meta( $post->ID, 'event_place', $event_place );
	}

	/**
	 * Validation to post event
	 * @param int $post_id
	 * @param object $post
	 * @return int
	 */
	// TODO delete
	function validate_event_post( $post_id, $post ) {
		if ( ( defined( 'DOING_AUTOSAVE' ) && DOING_AUTOSAVE ) || $post->post_status == 'auto-draft' ) {
			return $post_id;
		}
		if ( $post->post_type != 'event_manager' ) {
			return $post_id;
		}
		$meta_event_start_date_missing = false;
		// checking meta is not empty
		$my_meta = get_post_meta( $post_id, 'event_start_date', true );
		if ( empty( $my_meta ) ) {
			$meta_event_start_date_missing = true;
		}
		if ( ( isset( $_POST['publish'] ) || isset( $_POST['save'] ) ) && $_POST['post_status'] == 'publish' ) {
			if ( $meta_event_start_date_missing ) {
				global $wpdb;
				$wpdb->update( $wpdb->posts, array( 'post_status' => 'pending' ), array( 'ID' => $post_id ) );
				add_filter( 'redirect_post_location', create_function( '$location', 'return add_query_arg( "message", 99, $location );' ) );
			}
		}
	}

	/**
	 * Validation to publish event post
	 * @param object $data
	 * @return object
	 */
	function validate_event_publish( $data ) {
		if ( ( defined( 'DOING_AUTOSAVE' ) && DOING_AUTOSAVE ) || $data['post_status'] == 'auto-draft' ) {
			return $data;
		}
		if ( $data['post_type'] != 'event_manager' ) {
			return $data;
		}
		$meta_missing = false;
		// checking meta is not empty
		$event_start_date = $_POST['event_start_date'];
		$event_end_date = $_POST['event_start_date'];
		if ( empty( $event_start_date ) || empty( $event_end_date ) ) {
			$meta_missing = true;
		}
		if ( $data['post_status'] == 'publish' ) {
			if ( $meta_missing ) {
				$data['post_status'] = 'draft';
				add_filter( 'redirect_post_location', array( $this, 'event_manager_redirect_post_location_filter' ), 99 );
			}
		}
		return $data;
	}

	/**
	 * Filter redirect post location
	 * @param string $location
	 * @return string
	 */
	function event_manager_redirect_post_location_filter( $location ) {
		remove_filter( 'redirect_post_location', __FUNCTION__, 99 );
		$location = add_query_arg( 'message', 99, $location );
		return $location;
	}

	/**
	 * Filter post updated message
	 * @param object $messages
	 * @return object
	 */
	function event_manager_post_updated_messages_filter( $messages ) {
		$messages['post'][99] = __( 'Publish not allowed. (require event start date and event end date.)', self::$domain );
		return $messages;
	}

	/**
	 * Register setting
	 * @return void
	 */
	function register_event_manager_options_settings() {
		register_setting( 'event_manager', 'ws_serendip_event_manager_count_posts_per_page' );
		register_setting( 'event_manager', 'ws_serendip_event_manager_count_days_from_posts' );
	}

	/**
	 * Get wordpress query
	 * @param boolean $is_archive
	 * @return WP_Query
	 */
	function get_query( $is_archive = false, $paged = 1 ) {
		$args = array(
			'post_type' => 'event_manager',
			'post_status' => 'publish',
			'paged' => $paged,
			'meta_query' => array(
				array(
					'key' => 'event_end_date',
					'value' => date( 'Y-m' ),
					'compare' => $is_archive ? '<' : '>='
				)
			),
			'meta_key' => 'event_start_date',
			'orderby' => 'meta_value',
			'order' => $is_archive ? 'DESC' : 'ASC',
			'posts_per_page' => $is_archive ? get_option( 'ws_serendip_event_manager_count_posts_per_page', self::COUNT_POSTS_PER_PAGE ) : -1
		);
		return new WP_Query( $args );
	}
}

// vim:set syn=wordpress:noet:fenc=utf8:fdl=0 fdm=marker:ts=4 sw=4 sts=0:
?>
