=== Custom links in Elementor Image Carousel ===
Contributors: charlieetienne
Tags: elementor, image carousel, custom links
Stable tag: 1.1.1
Requires at least: 5.2
Tested up to: 6.1
Requires PHP: 7.2
License: GPLv2 or later
License URI: http://www.gnu.org/licenses/gpl-2.0.html
Donate Link: https://paypal.me/webnancy

Lets you add custom links in Elementor Image Carousel widget

== Description ==

There is a WordPress limitation (no custom link on images) that makes impossible to add custom links on each image in an image carousel or image gallery like the one in Elementor free version. 

This plugin just overrides WordPress attachment fields and Elementor Image Carousel widget to let you add custom links to each image in the carousel.
Basically, it adds two custom fields to each image in WordPress Media Library (Custom link and "Open in new tab?" checkbox).
Then it hooks into elementor image carousel widget just before it's rendered on frontend.

== Usage & Documentation ==

No options, no premium version, no bullshit. Just activate or deactivate.

**Note:** You have to enable the option “Link” in the Elementor Image Carousel widget and set it to: “Media Files” in order for it to work.

= Resources =

* **WordPress Plugin:** [https://wordpress.org/plugins/custom-links-in-elementor-image-carousel](https://wordpress.org/plugins/custom-links-in-elementor-image-carousel)
* **GitHub Repository:** [https://github.com/CharlieEtienne/custom-links-in-elementor-image-carousel](https://github.com/CharlieEtienne/custom-links-in-elementor-image-carousel)
* **Support:** [https://github.com/CharlieEtienne/custom-links-in-elementor-image-carousel/issues](https://github.com/CharlieEtienne/custom-links-in-elementor-image-carousel/issues)

== Installation ==

1. Install this plugin either via the WordPress.org plugin directory, or by uploading the files to your server.
2. Activate the plugin.
3. That's it. You're ready to go! Please, refer to the Usage & Documentation section for examples and how-to information.

== Frequently Asked Questions ==

= Is this plugin completely free? =
Yes.

= Can I use this plugin for commercial purposes? =
Sure, go ahead! It is completely open source.

== Screenshots ==

1. Custom fields on images in Media Library

== Changelog ==

= 1.1.1 =
* Fix fatal errors in some edge cases

= 1.1.0 =
 * Move to Singleton pattern to let other developpers unhook actions or filters

= 1.0.1 =
 * Fixes "Open in new tab" option. It's now possible to uncheck this setting
 * Improve docs