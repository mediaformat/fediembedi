# FediEmbedi

>Display your Fediverse timeline in a widget

FediEmbedi will display your Mastodon, Pleroma, Pixelfed, PeerTube timeline with various display options.

### How to use shortcodes
* `[mastodon exclude_reblogs="true"]`
* `[pixelfed limit="6"]`
* `[peertube instance="https://peertube.social" actor="channel_name" is_channel="true"]`

### Currently supported software
* [Mastodon](http://joinmastodon.org/)
* [Pleroma](https://git.pleroma.social/pleroma)
* [Pixelfed](https://pixelfed.org/)
* [PeerTube](https://joinpeertube.org/)


### Planned supported software
* See the [board](https://git.feneas.org/mediaformat/fediembedi/-/boards)
* [Suggestions](https://git.feneas.org/mediaformat/fediembedi/issues)?


### Development

For the time being development will happen on [git.feneas.org](https://git.feneas.org/mediaformat/fediembedi).


### Updates

The plugin is under active development, to keep FediEmbedi updated install [Github Updater](https://github.com/afragen/github-updater) as a companion plugin, and it will manage updates from within your Wordpress installation.


## Installation

Typical installation procedure

e.g.

1. Upload `fediembedi` to the `/wp-content/plugins/` directory
1. Activate the plugin through the 'Plugins' menu in WordPress
1. Connect to your fediverse instance by visiting the configuration page in Settings -> FediEmbedi


## Frequently Asked Questions


### Does this plugin store my login info?

No, this plugin uses [OAuth 2.0](https://oauth.net/). You will be sent to login to your Instance
and redirected to your site with a secure token. Similar to how you would connect a mobile app to your account.


## Changelog

### 0.11.1
* Bug fix: Shortcode limit attribute.
* Document: Shortcodes on options page.

### 0.11.0
* Feature: PeerTube widget NSFW option.
* Fix: PeerTube number of posts to display.
* Fix: connection issues.
* Fix: Template issues.

### 0.10.5
* Bug fix: Template issues.

### 0.10.4
* Bug fix: Embed included in post edit screen, causing post save issues.

### 0.10.3
* Security fix: statuses with visibility marked unlisted, private, and direct could be displayed publicly

### 0.10.0
* Service shortcodes

### 0.9.0
* Emoji support

### 0.8.5
* CSS for small columns

### 0.8.4
* Fix large video player

### 0.8.3
* version bump

### 0.8.2
* Add Support links

### 0.8.1
* CSS fix

### 0.8.0
* Support PeerTube
* Support separate Mastodon, Pixelfed and PeerTube widgets.

### 0.7.2
* Renamed some classes and constants, and reorganized file structure

### 0.7.1
* Fixed version info preventing auto-updates

### 0.7.0
* Added Pixelfed /embed styles
* Added i18n support to template strings

### 0.6.0
* Updated settings page, with links for finding an instance to join/register
* Clarify widget options

### 0.5.0
* Mirror plugin on Github for use with [Github Updater](https://github.com/afragen/github-updater)

### 0.4.1
* Readme updates

### 0.4.0
* Fix for Github updates

### 0.3.0
* Refactor Instance selection and logic
* Updated readme.txt
* Fixed reame.md formatting
* Cleaned up settings form

### 0.2.0
* Make an actual release.

### 0.1.0
* Initial commit.


## Credits


### Mastodon Autopost
The client connection code is based on [Mastodon Autopost](https://wordpress.org/plugins/autopost-to-mastodon/).

### FediEmbedi
The name FediEmbedi was contributed by [wake.st](https://wake.st/@liaizon)
