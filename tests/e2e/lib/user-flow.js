/**
 * External dependencies
 */
import { UserFlow as Base } from 'wp-e2e-page-objects';

/**
 * Internal dependencies
 */
import { PAGE } from './page-map';

export default class UserFlow extends Base {
	openSlackIntegrations() {
		this.currentPage = this.open( PAGE.WP_ADMIN_SLACK_INTEGRATIONS );
		return this.currentPage;
	}

	createIntegration( title, settings = {} ) {
		this.currentPage = this.open( PAGE.WP_ADMIN_NEW_SLACK_INTEGRATION );
		this.currentPage.setTitle( title );
		return this._setIntegrationSettings( settings );
	}

	editIntegration( integrationTitle, settings = {} ) {
		this.currentPage = this.openSlackIntegrations().editIntegrationWithTitle( integrationTitle );
		return this._setIntegrationSettings( settings );
	}

	_setIntegrationSettings( settings ) {
		settings = Object.assign(
			{
				serviceUrl: '',
				channel: '',
				username: '',
				icon: '',
				events: {
					postPublished: false,
					postNeedsReview: false,
					newComment: false
				},
				active: false
			},
			settings
		);

		const metaBox = this.currentPage.components.metaBoxIntegrationSetting;

		metaBox.setServiceUrl( settings.serviceUrl );
		metaBox.setChannel( settings.channel );
		metaBox.setUsername( settings.username );
		metaBox.setIcon( settings.icon );

		settings.events.postPublished
			? metaBox.checkWhenPostIsPublished()
			: metaBox.uncheckWhenPostIsPublished();

		settings.events.postNeedsReview
			? metaBox.checkWhenPostNeedsReview()
			: metaBox.uncheckWhenPostNeedsReview();

		settings.events.newComment
			? metaBox.checkWhenNewComment()
			: metaBox.uncheckWhenNewComment();

		settings.active
			? metaBox.checkActive()
			: metaBox.uncheckActive();

		this.currentPage.publish();
		return this.currentPage;
	}
}
