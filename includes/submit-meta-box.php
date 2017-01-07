<?php
/**
 * Submit Meta Box.
 *
 * @package WP_Slack
 * @subpackage Integration
 */

/**
 * Replacement for builtin submitdiv meta box for Slack integration CPT.
 */
class WP_Slack_Submit_Meta_Box {

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

		add_action( 'add_meta_boxes', array( $this, 'register_meta_box' ) );
	}

	/**
	 * Register submit meta box.
	 *
	 * @param string $post_type Post type.
	 */
	public function register_meta_box( $post_type ) {
		if ( $this->plugin->post_type->name === $post_type ) {
			add_meta_box( 'slack_submitdiv', __( 'Save Setting', 'slack' ), array( $this, 'slack_submitdiv' ), null, 'side', 'core' );
		}
	}

	/**
	 * Display post submit form fields.
	 *
	 * @param WP_Post $post Post object.
	 */
	public function slack_submitdiv( $post ) {
		require_once $this->plugin->plugin_path . 'views/submit-meta-box.php';
	}
}
