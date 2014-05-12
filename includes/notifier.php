<?php

class WP_Slack_Notifier {

	/**
	 * @var WP_Slack_Plugin
	 */
	private $plugin;

	public function __construct( WP_Slack_Plugin $plugin ) {
		$this->plugin = $plugin;
	}

	/**
	 * Notify Slack with given payload.
	 *
	 * @var WP_Slack_Event_Payload $payload
	 *
	 * @return mixed True if success, otherwise WP_Error
	 */
	public function notify( WP_Slack_Event_Payload $payload ) {
		$payload_json = $payload->toJSON();

		$resp = wp_remote_post( $payload->get_url(), array(
			'user-agent' => $this->plugin->name . '/' . $this->plugin->version,
			'body'       => $payload_json,
			'headers'=> array(
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
