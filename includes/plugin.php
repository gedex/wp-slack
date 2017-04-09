<?php
/**
 * Main Plugin Class.
 *
 * @package WP_Slack
 */

/**
 * This is the plugin class and acts as container for component instances and
 * basic properties of a plugin. Using container like this will avoid polluting
 * global namespaces. There's no global constants and only one global object
 * defined, that's this class' instance.
 */
class WP_Slack_Plugin {

	/**
	 * Items for setter and getter.
	 *
	 * @var array
	 */
	private $items = array();

	/**
	 * Run the plugin.
	 *
	 * @param string $path Path to main plugin file.
	 */
	public function run( $path ) {
		// This maybe used to prefix options, slug of menu or page, and
		// filters/actions.
		$this->name    = 'wp_slack';

		$this->version = '0.6.0';

		// Path.
		$this->plugin_path   = trailingslashit( plugin_dir_path( $path ) );
		$this->plugin_url    = trailingslashit( plugin_dir_url( $path ) );
		$this->includes_path = $this->plugin_path . trailingslashit( 'includes' );

		// Instances.
		$this->post_type       = new WP_Slack_Post_Type( $this );
		$this->notifier        = new WP_Slack_Notifier( $this );
		$this->post_meta_box   = new WP_Slack_Post_Meta_Box( $this );
		$this->submit_meta_box = new WP_Slack_Submit_Meta_Box( $this );
		$this->event_manager   = new WP_Slack_Event_Manager( $this );
	}

	/**
	 * Store item's value with a given key.
	 *
	 * @param string $key   Item's key.
	 * @param mixed  $value Item's value.
	 */
	public function __set( $key, $value ) {
		$this->items[ $key ] = $value;
	}

	/**
	 * Retrieve item with given key.
	 *
	 * @param string $key Item's key.
	 *
	 * @return mixed Item's value
	 */
	public function __get( $key ) {
		if ( isset( $this->items[ $key ] ) ) {
			return $this->items[ $key ];
		}

		return null;
	}

	/**
	 * Checks whether an item with given key exists.
	 *
	 * @param string $key Item's key.
	 *
	 * @return bool Returns true if item with given key exists.
	 */
	public function __isset( $key ) {
		return isset( $this->items[ $key ] );
	}

	/**
	 * Unset item with given key.
	 *
	 * @param string $key Item's key.
	 */
	public function __unset( $key ) {
		if ( isset( $this->items[ $key ] ) ) {
			unset( $this->items[ $key ], $this->raws[ $key ], $this->frozen[ $key ] );
		}
	}
}
