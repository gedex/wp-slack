<?php
/**
 * Custom Post Type for WP_Slack.
 *
 * @package WP_Slack
 * @subpackage Integration
 */

/**
 * Custom post type where each post stores Slack integration settings.
 */
class WP_Slack_Post_Type {

	/**
	 * Post type name.
	 *
	 * @var string
	 */
	public $name = 'slack_integration';

	/**
	 * Plugin's instance.
	 *
	 * @var WP_Slack_Plugin
	 */
	private $plugin;

	/**
	 * Constructor.
	 *
	 * @param WP_Slack_Plugin $plugin Plugin's instance.
	 */
	public function __construct( WP_Slack_Plugin $plugin ) {
		$this->plugin = $plugin;

		// Register custom post type to store Slack integration records.
		add_action( 'init', array( $this, 'register_post_type' ) );

		// Removes builtin submitdiv meta box.
		add_action( 'admin_menu', array( $this, 'remove_submitdiv' ) );

		// Enqueue scripts/styles and disables autosave for this post type.
		add_action( 'admin_enqueue_scripts', array( $this, 'enqueue_scripts' ) );

		// Alters message when post is updated.
		add_filter( 'post_updated_messages', array( $this, 'post_updated_messages' ) );

		// Alters messages when bulk updating.
		add_filter( 'bulk_post_updated_messages', array( $this, 'bulk_post_updated_messages' ), 10, 2 );

		// Custom bulk actions.
		add_filter( 'bulk_actions-edit-slack_integration', array( $this, 'custom_bulk_actions' ) );

		// Custom row actions.
		add_filter( 'post_row_actions', array( $this, 'custom_row_actions' ), 10, 2 );

		// Activate and deactivate actions.
		add_action( 'admin_action_activate',   array( $this, 'activate' ) );
		add_action( 'admin_action_deactivate', array( $this, 'deactivate' ) );

		// Add notices for activate/deactivate actions.
		add_action( 'all_admin_notices', array( $this, 'admin_notices' ) );

		// Custom columns.
		add_filter( sprintf( 'manage_%s_posts_columns', $this->name ), array( $this, 'columns_header' ) );
		add_action( sprintf( 'manage_%s_posts_custom_column', $this->name ), array( $this, 'custom_column_row' ), 10, 2 );

		// Alter post class in admin to notice whether setting is activated or not.
		add_filter( 'post_class', array( $this, 'post_class' ), 10, 3 );

		// Hides subsub top navigation.
		add_filter( 'views_edit-' . $this->name, array( $this, 'hide_subsubsub' ) );

		// Alters title placeholder.
		add_filter( 'enter_title_here', array( $this, 'title_placeholder' ) );
	}

	/**
	 * Register the custom post type.
	 */
	public function register_post_type() {
		$args = array(
			'description'         => '',
			'public'              => false,
			'publicly_queryable'  => false,
			'show_in_nav_menus'   => true,
			'show_in_admin_bar'   => true,
			'exclude_from_search' => true,

			'show_ui'             => true,
			'show_in_menu'        => true,

			'menu_position'       => 75, // Below tools.
			'menu_icon'           => $this->plugin->plugin_url . 'img/logo.png',
			'can_export'          => true,
			'delete_with_user'    => true,
			'hierarchical'        => false,
			'has_archive'         => false,
			'query_var'           => false,

			'map_meta_cap' => false,
			'capabilities' => array(

				// Meta caps (don't assign these to roles).
				'edit_post'              => 'manage_options',
				'read_post'              => 'manage_options',
				'delete_post'            => 'manage_options',

				// Primitive/meta caps.
				'create_posts'           => 'manage_options',

				// Primitive caps used outside of map_meta_cap().
				'edit_posts'             => 'manage_options',
				'edit_others_posts'      => 'manage_options',
				'publish_posts'          => 'manage_options',
				'read_private_posts'     => 'manage_options',

				// Primitive caps used inside of map_meta_cap().
				'read'                   => 'manage_options',
				'delete_posts'           => 'manage_options',
				'delete_private_posts'   => 'manage_options',
				'delete_published_posts' => 'manage_options',
				'delete_others_posts'    => 'manage_options',
				'edit_private_posts'     => 'manage_options',
				'edit_published_posts'   => 'manage_options',
			),

			'rewrite' => false,

			// What features the post type supports.
			'supports' => array(
				'title',
			),

			'labels' => array(
				'name'               => __( 'Slack Integration',             'slack' ),
				'singular_name'      => __( 'Slack Integration',             'slack' ),
				'menu_name'          => __( 'Slack',                         'slack' ),
				'name_admin_bar'     => __( 'Slack',                         'slack' ),
				'add_new'            => __( 'Add New',                       'slack' ),
				'add_new_item'       => __( 'Add New Slack Integration',     'slack' ),
				'edit_item'          => __( 'Edit Slack Integration',        'slack' ),
				'new_item'           => __( 'New Slack Integration',         'slack' ),
				'view_item'          => __( 'View Slack Integration',        'slack' ),
				'search_items'       => __( 'Search Slack Integration',      'slack' ),
				'not_found'          => __( 'No slack integration found',    'slack' ),
				'not_found_in_trash' => __( 'No slack integration in trash', 'slack' ),
				'all_items'          => __( 'Slack Integrations',            'slack' ),
			),

		);

		// Register the post type.
		register_post_type( $this->name, $args );
	}

	/**
	 * Remove default submit meta box.
	 */
	public function remove_submitdiv() {
		remove_meta_box( 'submitdiv', $this->name, 'side' );
	}

	/**
	 * Enqueue scripts for Slack Integration screens.
	 */
	public function enqueue_scripts() {
		if ( get_post_type() === $this->name ) {
			wp_dequeue_script( 'autosave' );

			wp_enqueue_style(
				'slack-admin',                                             // Handle.
				$this->plugin->plugin_url . 'css/admin.css',               // Src.
				array(),                                                   // Deps
				filemtime( $this->plugin->plugin_path . 'css/admin.css' ), // Version.
				'all'                                                      // Media.
			);

			wp_enqueue_script(
				'slack-admin-js',                                       // Handle.
				$this->plugin->plugin_url . 'js/admin.js',              // Src.
				array( 'jquery' ),                                      // Deps.
				filemtime( $this->plugin->plugin_path . 'js/admin.js' ) // Ver.
			);
		}
	}

	/**
	 * Filter message notice when integration setting is updated.
	 *
	 * @param array $messages List of messages when post is updated.
	 *
	 * @return array Updated messages
	 */
	public function post_updated_messages( $messages ) {
		$messages[ $this->plugin->post_type->name ] = array_fill( 0, 11,  __( 'Setting updated.', 'slack' ) );

		return $messages;
	}

	/**
	 * Filter message notice when integration settings were bulk-updated.
	 *
	 * @param array $bulk_messages Arrays of messages, each keyed by the
	 *                             corresponding post type. Messages are keyed with
	 *                             'updated', 'locked', 'deleted', 'trashed', and
	 *                             'untrashed'.
	 * @param array $bulk_counts   Array of item counts for each message, used to
	 *                             build internationalized strings.
	 *
	 * @return array Updated bulk messages
	 */
	public function bulk_post_updated_messages( $bulk_messages, $bulk_counts ) {
		$screen = get_current_screen();

		if ( $this->name === $screen->post_type ) {
			$bulk_messages['post'] = array(
				/* translators: placeholder is number of updated integrations. */
				'updated'   => _n( '%s integration updated.', '%s integrations updated.', $bulk_counts['updated'], 'slack' ),
				/* translators: placeholder is number of updated integrations. */
				'locked'    => _n( '%s integration not updated, somebody is editing it.', '%s integrations not updated, somebody is editing them.', $bulk_counts['locked'], 'slack' ),
				/* translators: placeholder is number of deleted integrations. */
				'deleted'   => _n( '%s integration permanently deleted.', '%s integrations permanently deleted.', $bulk_counts['deleted'], 'slack' ),
				/* translators: placeholder is number of trashed integrations. */
				'trashed'   => _n( '%s integration moved to the Trash.', '%s integrations moved to the Trash.', $bulk_counts['trashed'], 'slack' ),
				/* translators: placeholder is number of restored integrations. */
				'untrashed' => _n( '%s integration restored from the Trash.', '%s integrations restored from the Trash.', $bulk_counts['untrashed'], 'slack' ),
			);
		}

		return $bulk_messages;
	}

	/**
	 * Custom bulk actions.
	 *
	 * @param  array $actions List of actions.
	 * @return array List of actions
	 *
	 * @filter bulk_actions-edit-slack_integration
	 */
	public function custom_bulk_actions( $actions ) {
		unset( $actions['edit'] );

		// @codingStandardsIgnoreStart
		// Unfortunately adding bulk actions won't work here.
		// @see https://core.trac.wordpress.org/ticket/16031
		//
		// $actions['activate']   = __( 'Activate', 'slack' );
		// $actions['deactivate'] = __( 'Deactivate', 'slack' );
		// @codingStandardsIgnoreEnd

		return $actions;
	}

	/**
	 * Custom row actions for this post type.
	 *
	 * @param  array $actions List of actions.
	 *
	 * @return array List of actions
	 *
	 * @filter post_row_actions
	 */
	public function custom_row_actions( $actions ) {
		$post = get_post();

		if ( get_post_type( $post ) === $this->plugin->post_type->name ) {
			unset( $actions['inline hide-if-no-js'] );
			unset( $actions['view'] );

			$setting          = get_post_meta( $post->ID, 'slack_integration_setting', true );
			$post_type_object = get_post_type_object( $post->post_type );

			if ( $setting['active'] ) {
				$actions['deactivate'] = sprintf(
					'<a title="%1$s" href="%2$s">%3$s</a>',
					esc_attr__( 'Deactivate this integration setting', 'slack' ),
					wp_nonce_url( admin_url( sprintf( $post_type_object->_edit_link . '&amp;action=deactivate', $post->ID ) ), 'deactivate-post_' . $post->ID ),
					esc_html__( 'Deactivate', 'slack' )
				);
			} else {
				$actions['activate'] = sprintf(
					'<a title="%1$s" href="%2$s">%3$s</a>',
					esc_attr__( 'Activate this integration setting', 'slack' ),
					wp_nonce_url( admin_url( sprintf( $post_type_object->_edit_link . '&amp;action=activate', $post->ID ) ), 'activate-post_' . $post->ID ),
					esc_html__( 'Activate', 'slack' )
				);
			}
		}

		return $actions;
	}

	/**
	 * Activates the integration.
	 *
	 * @action admin_action_{action}
	 */
	public function activate() {
		$this->_set_active_setting();
	}

	/**
	 * Deactivates the integration.
	 *
	 * @action admin_action_{action}
	 */
	public function deactivate() {
		$this->_set_active_setting( false );
	}

	/**
	 * Action handler for activating/deactivating integration setting(s).
	 *
	 * @param bool $activate Flag to indicated whether this is activattion action.
	 */
	private function _set_active_setting( $activate = true ) {
		$screen = get_current_screen();
		if ( $screen->id !== $this->name ) {
			return;
		}

		$post = ! empty( $_REQUEST['post'] ) ? get_post( $_REQUEST['post'] ) : null;
		if ( ! $post ) {
			wp_die(
				/* translators: placeholder is action type, either 'activate' or 'deactivate'. */
				sprintf( __( 'The integration you are trying to %s is no longer exists.', 'slack' ), $activate ? 'activate' : 'deactivate' )
			);
		}

		check_admin_referer( sprintf( '%s-post_%d' , $activate ? 'activate' : 'deactivate', $post->ID ) );

		$sendback = admin_url( 'edit.php?post_type=' . $this->name );
		$setting  = get_post_meta( $post->ID, 'slack_integration_setting', true );
		$setting['active'] = $activate;

		update_post_meta( $post->ID, 'slack_integration_setting', $setting );

		$key_arg = $activate ? 'activated' : 'deactivated';

		wp_redirect( add_query_arg(
			array(
				"$key_arg" => 1,
				'ids'      => $post->ID,
			),
			$sendback
		) );

		exit;
	}

	/**
	 * Display notice when integration is activated or deactivated.
	 *
	 * @action all_admin_notices
	 */
	public function admin_notices() {
		$screen = get_current_screen();
		if ( 'edit-' . $this->name !== $screen->id ) {
			return;
		}

		$bulk_counts = array(
			'activated'   => isset( $_REQUEST['activated'] )   ? absint( $_REQUEST['activated'] )   : 0,
			'deactivated' => isset( $_REQUEST['deactivated'] ) ? absint( $_REQUEST['deactivated'] ) : 0,
		);

		$bulk_messages = array(
			/* translators: number of activated integrations. */
			'activated'   => _n( '%s integration activated.', '%s integrations activated.', $bulk_counts['activated'], 'slack' ),
			/* translators: number of deactivated integrations. */
			'deactivated' => _n( '%s integration deactivated.', '%s integrations deactivated.', $bulk_counts['deactivated'], 'slack' ),
		);

		$bulk_counts = array_filter( $bulk_counts );

		// If we have a bulk message to display.
		$messages = array();
		foreach ( $bulk_counts as $message => $count ) {
			if ( isset( $bulk_messages[ $message ] ) ) {
				$messages[] = sprintf( $bulk_messages[ $message ], number_format_i18n( $count ) );
			}
		}

		if ( $messages ) {
			echo '<div id="message" class="updated"><p>' . join( ' ', $messages ) . '</p></div>';
		}
	}

	/**
	 * Custom columns for this post type.
	 *
	 * @param  array $columns Post list columns.
	 * @return array Post list columns
	 *
	 * @filter manage_{post_type}_posts_columns
	 */
	public function columns_header( $columns ) {
		unset( $columns['date'] );

		$columns['service_url'] = __( 'Service URL', 'slack' );
		$columns['channel']     = __( 'Channel', 'slack' );
		$columns['bot_name']    = __( 'Bot Name', 'slack' );
		$columns['events']      = __( 'Notified Events', 'slack' );

		return $columns;
	}

	/**
	 * Custom column appears in each row.
	 *
	 * @param string $column  Column name.
	 * @param int    $post_id Post ID.
	 *
	 * @action manage_{post_type}_posts_custom_column
	 */
	public function custom_column_row( $column, $post_id ) {
		$setting = get_post_meta( $post_id, 'slack_integration_setting', true );
		switch ( $column ) {
			case 'service_url':
				echo ! empty( $setting['service_url'] ) ? sprintf( '<a href="%s" target="_blank">%s</a>', esc_url( $setting['service_url'] ), esc_html( $setting['service_url'] ) ) : '';
				break;
			case 'channel':
				echo ! empty( $setting['channel'] ) ? $setting['channel'] : '';
				break;
			case 'bot_name':
				echo ! empty( $setting['username'] ) ? $setting['username'] : '';
				break;
			case 'events':
				$events = $this->plugin->event_manager->get_events();

				if ( ! empty( $setting['events'] ) ) {
					echo '<ul>';
					foreach ( $setting['events'] as $event => $enabled ) {
						if ( $enabled && ! empty( $events[ $event ] ) ) {
							printf( '<li>%s</li>', esc_html( $events[ $event ]['description'] ) );
						}
					}
					echo '</ul>';
				}
				break;
		}
	}

	/**
	 * Alter post class in list table to notice whether setting is activated or not.
	 *
	 * @param array  $classes An array of post classes.
	 * @param string $class   A comma-separated list of additional classes added
	 *                        to the post.
	 * @param int    $post_id The post ID.
	 *
	 * @return array Array of post classes
	 *
	 * @filter post_class
	 */
	public function post_class( $classes, $class, $post_id ) {
		if ( ! is_admin() ) {
			return $classes;
		}

		$screen = get_current_screen();
		if ( sprintf( 'edit-%s', $this->name ) !== $screen->id ) {
			return $classes;
		}

		$setting = get_post_meta( $post_id, 'slack_integration_setting', true );
		if ( ! $setting['active'] ) {
			$classes[] = 'inactive';
		} else {
			$classes[] = 'active';
		}

		return $classes;
	}


	/**
	 * Hides subsubsub top nav.
	 *
	 * @return array Top nav links
	 */
	public function hide_subsubsub() {
		return array();
	}

	/**
	 * Change title placeholder for Slack Integration post.
	 *
	 * @param string $title Title placeholder.
	 *
	 * @return string Updated title placeholder
	 */
	public function title_placeholder( $title ) {
		$screen = get_current_screen();

		if ( $this->name === $screen->post_type ) {
			$title = __( 'Integration Name', 'slack' );
		}

		return $title;
	}
}
