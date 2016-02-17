<?php

/**
 * Add custom post-type support to Slack
 *
 * @copyright Copyright (c), Forsite Media
 * @author Forsite Media / Ryan Hellyer <ryan@forsite.nu>
 * @since 1.0
 */
class WP_Slack_Settings {

	/**
	 * Class constructor
	 */
	public function __construct() {

		add_action( 'admin_init',     array( $this, 'register_settings' ) );
		add_action( 'admin_menu',     array( $this, 'add_submenu_page' ) );
		add_action( 'plugins_loaded', array( $this, 'set_post_types' ) );
	}

	/*
	 * Custom Post Type support for WP-Slack
	 */
	public function set_post_types() {
		add_filter(
			'slack_event_transition_post_status_post_types',
			function( $post_types ) {
				$set_post_types = get_option( 'slack-post-types' );
				foreach( $set_post_types as $post_type => $on ) {
					$post_types[] = $post_type;
				}

				return $post_types;
			}
		);
	}

	/**
	 * Init plugin options to white list our options
	 */
	public function register_settings(){

		// Register our option
		register_setting( 'slack-post-types', 'slack-post-types', array( $this, 'sanitize' ) );

		// Add post post-type as default
		add_option( 'slack-post-types', array( 'post' => 1 ) );
	}

	/**
	 * Add the menu page
	 */
	public function add_submenu_page() {
		add_submenu_page(
			'edit.php?post_type=slack_integration',
			__( 'Post types', 'slack-post-types' ),
			__( 'Post types', 'slack-post-types' ),
			'manage_options',
			'slack-post-types',
			array( $this, 'display_admin_page' )
		);
	}

	/**
	 * Output the admin page
	 */
	public function display_admin_page() {
		?>
		<div class="wrap">
			<?php screen_icon(); ?>

			<h2><?php _e( 'Slack post-types', 'slack-post-types' ); ?></h2>
			<p><?php _e( 'Set which post-types are operational in Slack below.', 'slack-post-types' ); ?></p>

			<form method="post" action="options.php">
				<?php settings_fields( 'slack-post-types' ); ?>

				<table class="form-table"><?php
				foreach ( $this->get_post_types()  as $post_type ) {

					// Grab existing setting
					$options = get_option( 'slack-post-types' );
					if ( isset( $options[$post_type] ) ) {
						$option = $options[$post_type];
					} else {
						$option = '';
					}
					?>

					<tr valign="top">
						<th scope="row"><?php printf( __( 'Include the "%s" post-type?', 'slack-post-types' ), $post_type ); ?></th>
						<td>
							<input type="checkbox" value="1" <?php checked( $option, 1 ); ?> id="<?php echo esc_attr( 'slack-post-types-' . $post_type ); ?>" name="<?php echo esc_attr( 'slack-post-types[' . $post_type . ']' ); ?>">
							<label class="hidden description" for="<?php echo esc_attr( 'slack-post-types-' . $post_type ); ?>"><?php _e( 'Include post-type', 'slack-post-types' ); ?></label>
						</td>
					</tr><?php

				}
				?>

				</table>

				<p class="submit">
					<input type="submit" class="button-primary" value="<?php _e( 'Save Settings', 'slack-post-types' ); ?>" />
				</p>
			</form>
		</div><?php
	}

	/**
	 * Sanitize and validate protection level
	 *
	 * @param    array    $input   The array of allowed post-types
	 * @return   array    The sanitized array of allowed post-types
	 */
	public function sanitize( $input ) {
		$post_types = $this->get_post_types();
		$output = array();

		// Iterate through possible post-types
		foreach ( $post_types  as $post_type ) {

			// Only set if post-type exists
			if ( isset( $input[$post_type] ) ) {
				$output[$post_type] = 1;
			}

		}

		return $output;
	}

	/**
	 * Get existing available public WordPress post-types
	 *
	 * @access   private
	 * @return   array  The available public post-types
	 */
	private function get_post_types() {

		$output = 'names'; // names or objects, note names is the default
		$operator = 'and'; // 'and' or 'or'

		// Collate builtin and custom post-types
		$builtin_post_types = get_post_types( array( 'public'   => true, '_builtin' => true ), $output, $operator );
		$custom_post_types = get_post_types( array( 'public'   => true, '_builtin' => false ), $output, $operator );
		$post_types = array_merge( $builtin_post_types, $custom_post_types );

		return $post_types;
	}

}
