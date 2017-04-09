/**
 * External dependencies
 */
import config from 'config';
import path from 'path';
import test from 'selenium-webdriver/testing';
import { WebDriverManager } from 'wp-e2e-webdriver';

/**
 * Internal dependencies
 */
import UserFlow from './lib/user-flow';

test.before( 'open browser', function() {
	this.timeout( config.get( 'startBrowserTimeoutMs' ) );

	global.__MANAGER__ = new WebDriverManager( 'chrome', {
		baseUrl: config.get( 'url' ),
		screenshotsDir: path.resolve( process.cwd(), 'tests/e2e/screenshots' )
	} );

	global.__DRIVER__ = global.__MANAGER__.getDriver();

	global.__USER__ = new UserFlow( global.__DRIVER__, {
		baseUrl: config.get( 'url' ),
		username: config.get( 'users.admin.username' ),
		password: config.get( 'users.admin.password' )
	} );
} );
