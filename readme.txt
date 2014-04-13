=== Slack ===
Contributors:      akeda
Donate link:       http://goo.gl/DELyuR
Tags:              slack, api, chat, notification
Requires at least: 3.6
Tested up to:      3.8.1
Stable tag:        trunk
License:           GPLv2 or later
License URI:       http://www.gnu.org/licenses/gpl-2.0.html

Send notifications to Slack channels when certain events in WordPress occur.

== Description ==

This plugin allows you to send notifications to [Slack](https://slack.com) channels when certain events in WordPress occur.

[youtube http://www.youtube.com/watch?v=Az-XqfRmp_k]

By default, there are two events that can be sent to Slack:

1. When a post is published
1. When there's a new comment

It's possible to add more events using `slack_get_events` filter. For more information check [the doc](http://gedex.web.id/wp-slack/).

**Development of this plugin is done on [GitHub](https://github.com/gedex/wp-slack). Pull requests are always welcome**.

== Installation ==

1. Upload **Slack** plugin to your blog's `wp-content/plugins/` directory and activate.
1. Add new **Incoming WebHooks** service in your Slack, the URL is `https://<SUBDOMAIN>.com/services/new/incoming-webhook` (replace `<SUBDOMAIN>` with your Slack's subdomain). Once created, note the URL of the service (you'll set it into integration entry in your WordPress).
1. Go to **Slack** menu in your WordPress to add the integration (make sure you're logged in as an Adminstrator).

== Screenshots ==

1. Integrations list. Yes, you can add more than one integration.
1. Edit integration screen.
1. Your channel get notified when some events occur.

== Changelog ==

= 0.2.0 =
* For default "When a post is published" and "When there is a new comment" events, limit the post type to "post"
* Don't send notification if message evaluates to false
* Plugin's banner
* Put Slack URL in README

= 0.1.0 =
Initial release
