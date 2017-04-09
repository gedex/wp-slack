/**
 * External dependencies
 */
import { By } from 'selenium-webdriver';
import { ComponentMetaBox } from 'wp-e2e-page-objects';
import { WebDriverHelper as helper } from 'wp-e2e-webdriver';

const METABOX_SELECTOR = By.css( '#slack_setting_metabox' );
const SERVICE_URL_SELECTOR = By.css( '[name="slack_setting[service_url]"]' );
const CHANNEL_SELECTOR = By.css( '[name="slack_setting[channel]"]' );
const USERNAME_SELECTOR = By.css( '[name="slack_setting[username]"]' );
const ICON_SELECTOR = By.css( '[name="slack_setting[icon_emoji]"]' );
const POST_PUBLISHED_SELECTOR = By.css( '[name="slack_setting[events][post_published]"]' );
const POST_NEEDS_REVIEW_SELECTOR = By.css( '[name="slack_setting[events][post_pending_review]"]' );
const NEW_COMMENT_SELECTOR = By.css( '[name="slack_setting[events][new_comment]"]' );
const ACTIVE_SELECTOR = By.css( '[name="slack_setting[active]"]' );
const TEST_SEND_NOTIFICATION_SELECTOR = By.css( '#slack-test-notify-button' );
const TEST_SEND_RESPONSE_SELECTOR = By.css( '#slack-test-notify-response span' );

export default class ComponentMetaBoxIntegrationSetting extends ComponentMetaBox {
	constructor( driver ) {
		super( driver, METABOX_SELECTOR, { wait: false } );
	}

	setServiceUrl( url ) {
		return helper.setWhenSettable( this.driver, SERVICE_URL_SELECTOR, url );
	}

	setChannel( channel ) {
		return helper.setWhenSettable( this.driver, CHANNEL_SELECTOR, channel );
	}

	setUsername( username ) {
		return helper.setWhenSettable( this.driver, USERNAME_SELECTOR, username );
	}

	setIcon( icon ) {
		return helper.setWhenSettable( this.driver, ICON_SELECTOR, icon );
	}

	setIcon( icon ) {
		return helper.setWhenSettable( this.driver, ICON_SELECTOR, icon );
	}

	checkWhenPostIsPublished() {
		return helper.setCheckbox( this.driver, POST_PUBLISHED_SELECTOR );
	}

	uncheckWhenPostIsPublished() {
		return helper.unsetCheckbox( this.driver, POST_PUBLISHED_SELECTOR );
	}

	checkWhenPostNeedsReview() {
		return helper.setCheckbox( this.driver, POST_NEEDS_REVIEW_SELECTOR );
	}

	uncheckWhenPostNeedsReview() {
		return helper.unsetCheckbox( this.driver, POST_NEEDS_REVIEW_SELECTOR );
	}

	checkWhenNewComment() {
		return helper.setCheckbox( this.driver, NEW_COMMENT_SELECTOR );
	}

	uncheckWhenNewComment() {
		return helper.unsetCheckbox( this.driver, NEW_COMMENT_SELECTOR );
	}

	checkActive() {
		return helper.setCheckbox( this.driver, ACTIVE_SELECTOR );
	}

	uncheckActive() {
		return helper.unsetCheckbox( this.driver, ACTIVE_SELECTOR );
	}

	testSendNotification() {
		return helper.clickWhenClickable( this.driver, TEST_SEND_NOTIFICATION_SELECTOR );
	}

	getTestNotificationResponse() {
		helper.waitTillPresentAndDisplayed( this.driver, TEST_SEND_RESPONSE_SELECTOR );
		return this.driver.findElement( TEST_SEND_RESPONSE_SELECTOR ).getText();
	}
}
