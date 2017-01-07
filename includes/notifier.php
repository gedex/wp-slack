<?php
/**
 * Slack Notifier.
 *
 * @package WP_Slack
 * @subpackage Notifier
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Notify Slack via Incoming Webhook API.
 *
 * @see https://api.slack.com/incoming-webhooks
 */
class WP_Slack_Notifier {

	/**
	 * Plugin's instance.
	 *
	 * @var WP_Slack_Plugin
	 */
	private $plugin;

	/**
	 * Constructor.
	 *
	 * @param WP_Slack_Plugin $plugin Plugin instance.
	 */
	public function __construct( WP_Slack_Plugin $plugin ) {
		$this->plugin = $plugin;
	}

	/**
	 * Notify Slack with given payload.
	 *
	 * @param WP_Slack_Event_Payload $payload Payload to send via POST.
	 *
	 * @return mixed True if success, otherwise WP_Error
	 */
	public function notify( WP_Slack_Event_Payload $payload ) {
		$payload_json = $payload->get_json_string();

		$resp = wp_remote_post( $payload->get_url(), array(
			'user-agent' => $this->plugin->name . '/' . $this->plugin->version,
			'body'       => $payload_json,
			'headers' => array(
				'Content-Type' => 'application/json',
			),
		) );

		if ( is_wp_error( $resp ) ) {
			return $resp;
		} else {
			$status  = intval( wp_remote_retrieve_response_code( $resp ) );
			$message = wp_remote_retrieve_body( $resp );
			if ( 200 !== $status ) {
				return new WP_Error( 'slack_unexpected_response', $message );
			}

			return $resp;
		}
	}
}
