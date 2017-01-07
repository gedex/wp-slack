<?php
/**
 * Event Payload.
 *
 * @package WP_Slack
 * @subpackage Event
 */

/**
 * Event payload representation.
 */
class WP_Slack_Event_Payload {

	/**
	 * Setting.
	 *
	 * @var array
	 */
	private $setting;

	/**
	 * Constructor.
	 *
	 * @param array $setting Setting values.
	 */
	public function __construct( array $setting ) {
		$this->setting = $setting;
	}

	/**
	 * Get service URL.
	 *
	 * @return string Service URL
	 */
	public function get_url() {
		return $this->setting['service_url'];
	}

	/**
	 * Get JSON string for the setting.
	 *
	 * @return string JSON string
	 */
	public function get_json_string() {
		return json_encode( array(
			'channel'      => $this->setting['channel'],
			'username'     => $this->setting['username'],
			'text'         => $this->setting['text'],
			'icon_emoji'   => $this->setting['icon_emoji'],
		) );
	}

	/**
	 * Get JSON string for the setting.
	 *
	 * @deprecated 0.6.0 Use get_json_string()
	 * @see WP_Slack_Event_Payload::get_json_string()
	 *
	 * @return string JSON string
	 * @codingStandardsIgnoreStart
	 */
	public function toJSON() {
		// @codingStandardsIgnoreEnd
		_deprecated_function( __METHOD__, '0.6.0', 'WP_Slack_Event_Payload::get_json_string()' );
		return $this->get_json_string();
	}
}
