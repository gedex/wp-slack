/**
 * External dependencies
 */
import { WPAdminPostEdit } from 'wp-e2e-page-objects';

/**
 * Internal dependencies
 */
import ComponentMetaBoxIntegrationSetting from './component-meta-box-integration-setting';

export default class WPAdminSlackIntegrationEdit extends WPAdminPostEdit {
	constructor( driver, args = {} ) {
		args = Object.assign(
			{
				components: {
					metaBoxIntegrationSetting: ComponentMetaBoxIntegrationSetting
				}
			},
			args
		);
		super( driver, args );
	}

	testSendNotification() {
		return this.components.metaBoxIntegrationSetting.testSendNotification();
	}

	getTestNotficationResponse() {
		return this.components.metaBoxIntegrationSetting.getTestNotificationResponse();
	}
}
