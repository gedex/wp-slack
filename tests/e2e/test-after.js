/**
 * External dependencies
 */
import config from 'config';
import test from 'selenium-webdriver/testing';
import { WebDriverHelper as helper } from 'wp-e2e-webdriver';

test.afterEach( 'take screenshot', function() {
	this.timeout( config.get( 'afterHookTimeoutMs' ) );
	return helper.takeScreenshot( global.__MANAGER__, this.currentTest );
} );

test.after( 'quit browser', function() {
	this.timeout( config.get( 'afterHookTimeoutMs' ) );
	if ( global.__MANAGER__ ) {
		global.__MANAGER__.quitBrowser();
	}
} );
