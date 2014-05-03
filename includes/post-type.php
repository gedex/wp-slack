<?php
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
	 * @var WP_Slack_Plugin
	 */
	private $plugin;

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

		// Removes edit in bulk actions.
		add_filter( 'bulk_actions-edit-slack_integration', array( $this, 'remove_edit_in_bulk_actions' ) );

		// Removes quick edit and view actions.
		add_filter( 'post_row_actions', array( $this, 'remove_actions' ), 10, 2 );

		// Custom columns
		add_filter( sprintf( 'manage_%s_posts_columns', $this->name ), array( $this, 'columns_header' ) );
		add_action( sprintf( 'manage_%s_posts_custom_column', $this->name ), array( $this, 'custom_column_row' ), 10, 2 );

		// Hides subsub top navigation.
		add_filter( 'views_edit-' . $this->name, array( $this, 'hide_subsubsub' ) );

		// Alters title placeholder.
		add_filter( 'enter_title_here', array( $this, 'title_placeholder' ) );
	}

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

			'menu_position'       => 75, // Below tools
			'menu_icon'           => $this->plugin->plugin_url . 'img/logo.png',
			'can_export'          => true,
			'delete_with_user'    => true,
			'hierarchical'        => false,
			'has_archive'         => false,
			'query_var'           => false,

			'map_meta_cap' => false,
			'capabilities' => array(

				// meta caps (don't assign these to roles)
				'edit_post'              => 'manage_options',
				'read_post'              => 'manage_options',
				'delete_post'            => 'manage_options',

				// primitive/meta caps
				'create_posts'           => 'manage_options',

				// primitive caps used outside of map_meta_cap()
				'edit_posts'             => 'manage_options',
				'edit_others_posts'      => 'manage_options',
				'publish_posts'          => 'manage_options',
				'read_private_posts'     => 'manage_options',

				// primitive caps used inside of map_meta_cap()
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

	public function remove_submitdiv() {
		remove_meta_box( 'submitdiv', $this->name, 'side' );
	}

	public function enqueue_scripts() {
		if ( $this->name === get_post_type() ) {
			wp_dequeue_script( 'autosave' );

			wp_enqueue_style(
				// Handle.
				'slack-admin',

				// Src.
				$this->plugin->plugin_url . 'css/admin.css',

				// Deps
				array(),

				// Version.
				filemtime( $this->plugin->plugin_path . 'css/admin.css' ),

				// Media.
				'all'
			);

			wp_enqueue_script(
				// Handle.
				'slack-admin-js',

				// Src.
				$this->plugin->plugin_url . 'js/admin.js',

				// Deps
				array( 'jquery' ),

				// Ver.
				filemtime( $this->plugin->plugin_path . 'js/admin.js' )
			);
		}
	}

	public function post_updated_messages( $messages ) {
		$messages[ $this->plugin->post_type->name ] = array_fill( 0, 11,  __( 'Setting updated.', 'slack' ) );

		return $messages;
	}

	/**
	 * @param array $bulk_messages Arrays of messages, each keyed by the corresponding post type. Messages are
	 *                             keyed with 'updated', 'locked', 'deleted', 'trashed', and 'untrashed'.
	 * @param array $bulk_counts   Array of item counts for each message, used to build internationalized strings.
	 */
	public function bulk_post_updated_messages( $bulk_messages, $bulk_counts ) {
		$screen = get_current_screen();

		if ( $this->name === $screen->post_type ) {
			$bulk_messages['post'] = array(
				'updated'   => _n( '%s integration updated.', '%s integrations updated.', $bulk_counts['updated'] ),
				'locked'    => _n( '%s integration not updated, somebody is editing it.', '%s integrations not updated, somebody is editing them.', $bulk_counts['locked'] ),
				'deleted'   => _n( '%s integration permanently deleted.', '%s integrations permanently deleted.', $bulk_counts['deleted'] ),
				'trashed'   => _n( '%s integration moved to the Trash.', '%s integrations moved to the Trash.', $bulk_counts['trashed'] ),
				'untrashed' => _n( '%s integration restored from the Trash.', '%s integrations restored from the Trash.', $bulk_counts['untrashed'] ),
			);
		}

		return $bulk_messages;
	}

	/**
	 * @filter bulk_actions-edit-slack_integration
	 */
	public function remove_edit_in_bulk_actions( $actions ) {
		unset( $actions['edit'] );
		return $actions;
	}

	public function remove_actions( $actions ) {
		$post = get_post();

		if ( $this->plugin->post_type->name === get_post_type( $post ) ) {
			unset( $actions['inline hide-if-no-js'] );
			unset( $actions['view'] );
		}

		return $actions;
	}

	/**
	 * Custom columns for this post type.
	 *
	 * @param  array $columns
	 * @return array
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
	 * @param string $column  Column name
	 * @param int    $post_id Post ID
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
	 * Hides subsubsub top nav.
	 *
	 * @return array
	 */
	public function hide_subsubsub() {
		return array();
	}

	public function title_placeholder( $title ) {
		$screen = get_current_screen();

		if ( $this->name === $screen->post_type ) {
			$title = __( 'Integration Name', 'slack' );
		}

		return $title;
	}
}
