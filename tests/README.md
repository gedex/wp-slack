WP Slack Tests
==============

[![Build Status](https://travis-ci.org/gedex/wp-slack.svg?branch=master)](https://travis-ci.org/gedex/wp-slack)

## Unit Tests

### Getting started

1. Make sure you have [`PHPUnit`](http://phpunit.de/) installed
2. Install WordPress and WP Unit Test library.

   ```
   $ tests/bin/install-wp-tests.sh wp_slack_tests root root
   ```

   **Note**: `wp_slack_tests` is a database name. It will be created if it
   doesn't exist and all data will be removed during the testing.

### Running the tests

Change to the plugin root directory and type:

```
$ phpunit
```

Example output:

```
Installing...
Running as single site... To run multisite, use -c tests/phpunit/multisite.xml
Not running ajax tests. To execute these, use --group ajax.
Not running ms-files tests. To execute these, use --group ms-files.
Not running external-http tests. To execute these, use --group external-http.
PHPUnit 5.7.15 by Sebastian Bergmann and contributors.

................                                                  16 / 16 (100%)

Time: 2 seconds, Memory: 26.00MB

OK (16 tests, 123 assertions)
```

## End-to-end Tests

### Getting started

1. Make sure you have [`node`](https://docs.npmjs.com/getting-started/installing-node),
   `npm`, and [`chromedriver`](https://sites.google.com/a/chromium.org/chromedriver/downloads).
2. Execute `npm install` to install all dependencies.
3. Create your e2e local config, for example:

   ```
   $ touch tests/e2e/local-development.json
   ```
4. Update mandatory config (`url`, `slack.settings.serviceUrl`, `slack.channel`,
   and `slack.token`) in your `local-development.json`.

### Running the tests

Change to the plugin root directory and type:

```
$ npm test
```

If everything sets properly, you should see tests running like [this](https://cloudup.com/cksHkEYv5lW).
