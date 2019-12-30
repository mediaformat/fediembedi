=== Plugin Name ===
Contributors: dj_angola
Donate link: https://paypal.me/MediaFormat
Tags: mastodon, pixelfed, fediverse
Requires at least: 5.1
Tested up to: 5.3.2
Requires PHP: 7.2
Stable tag: 4.3
License: GPLv2 or later
License URI: http://www.gnu.org/licenses/gpl-2.0.html

Display your Fediverse timeline in a widget

== Description ==

> FediEmbedi is beta software.

FediEmbedi will display your Mastodon, Pleroma, or Pixelfed timeline in a widget, with various display options.

= Currently supported software =
* Mastodon
* Pleroma
* Pixelfed

= Planned supported software =
* PeerTube
* *Suggestions?*

= Development =

For the time being development will happen on [git.feneas.org](https://git.feneas.org/mediaformat/fediembedi "FediEmbedi").

= Updates =

The plugin is under active development, and will keep be tagging releases.
I will be setting up a Github mirror, to facilitate updates using [Github Updater](https://github.com/afragen/github-updater)
as a companion plugin.

== Installation ==

Typical installation procedure

e.g.

1. Upload `fediembedi` to the `/wp-content/plugins/` directory
1. Activate the plugin through the 'Plugins' menu in WordPress
1. Connect to your fediverse instance by visiting the configuration page in Settings -> FediEmbedi

or using Github Updater
1. Settings -> Github Updater
1. Install plugin tab -> Plugin URI = `https://git.feneas.org/mediaformat/fediembedi`

== Frequently Asked Questions ==

= Does this plugin store my login info? =

No, this plugin uses oAuth 2.0. You will be sent to login to your Instance
and redirected to your site with a secure token. Similar to how apps connect to your account

== Changelog ==

= 0.4.0 =
* Fix for Github updates

= 0.3.0 =
* Refactor Instance selection and logic
* Updated readme.txt
* Fixed reame.md formatting
* Cleaned up settings form

= 0.2.0 =
* Make an actual release.

= 0.1.0 =
* Initial commit.

== Credits ==

= Mastodon Autopost =
The App registration, oAuth connection and portions of the Mastodon API code are based on [Mastodon Autopost](https://wordpress.org/plugins/autopost-to-mastodon/).

= Mastodon =
The CSS and SVG icon come from the Mastodon project
