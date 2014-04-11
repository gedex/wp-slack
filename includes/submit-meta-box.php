<?php
/**
 * Replacement for builtin submitdiv meta box
 * for our custom post type.
 */
class WP_Slack_Submit_Meta_Box {

	/**
	 * @var WP_Slack_Plugin
	 */
	private $plugin;

	public function __construct( WP_Slack_Plugin $plugin ) {
		$this->plugin = $plugin;

		add_action( 'add_meta_boxes', array( $this, 'register_meta_box' ) );
	}

	/**
	 * Register submit meta box.
	 *
	 * @param
	 */
	public function register_meta_box( $post_type ) {
		if ( $this->plugin->post_type->name === $post_type ) {
			add_meta_box( 'slack_submitdiv', __( 'Save Setting', 'slack' ), array( $this, 'slack_submitdiv' ), null, 'side', 'core' );
		}
	}

	/**
	 * Display post submit form fields.
	 *
	 * @param object $post
	 */
	public function slack_submitdiv( $post ) {
		require_once $this->plugin->plugin_path . 'views/submit-meta-box.php';
	}
}
