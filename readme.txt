=== FileJet Pro ===
Contributors: filejet
Tags: image optimization, webp, smart cdn, filejet 
Requires at least: 4.0
Tested up to: 5.2
Requires PHP: 5.6
Stable tag: 1.3.6
License: GPLv2 or later
License URI: https://www.gnu.org/licenses/gpl-2.0.html

FileJet Pro plugin provides easy integration with FileJet service for serving as much optimized images as possible for your clients.


== Description ==

# Size matters!

**Stop sending billboard sized images to mobile devices**

The images on websites are often too big. Your users accessing your Wordpress site through mobile devices may experience long loading times and very big download sizes of your images. 

FileJet is here to help you with this issue - we will help you to serve your clients only what they need. You can use your Wordpress instance as before eg. you are still uploading images as you are used to but with our plugin they are optimized for your clients' satisfaction.

Simply turn on FileJet Pro plugin within the Plugins section, fill in Storage ID, API key and the secret at FileJet Pro settings page and let the plugin do its magic. You upload one image and we provide all other versions for all types of devices on the fly automatically. We cache the images by using worldwide smart CDN so everyone on the planet will access your beautiful images as quick as possible.


== Installation ==

This section describes how to install the plugin and get it working.

e.g.

1. Upload the plugin files to the `/wp-content/plugins/filejetpro` directory, or install the plugin through the WordPress plugins screen directly.
2. Activate the plugin through the 'Plugins' screen in WordPress
3. Register at https://filejet.io to access your credentials
4. Use the FileJet Pro screen to configure credentials obtained at https://filejet.io
5. Everything is working automatically for you from the first second but if you wish to customize the plugin behaviour you can do it at FileJet Pro settings screen


== Frequently Asked Questions ==

= Do I need to register at FileJet? =

Yes, you need to register at https://filejet.io in order to obtain Storage ID, API key and the secret. The FileJet service is paid service but you can get free invitation from your friends. The invitation count is limited so hurry up to get one ;-)

= Does FileJet Pro plugin work without additional configuration? =

Only thing you need to configure are credentials which can be obtained at https://filejet.io - everything else works out of box automatically. If you need to additionally configure behaviours of any specific images you can do it via FileJet Pro settings page. Currently you can add image CSS classes to ignore list so FileJet won't optimize them or you can set up specific mutations for specific CSS classes.

= Is it possible to stop using FileJet Pro? =

You can clear you credentials at FileJet Pro settings page at any time if you wish. Also you can uninstall the plugin at any time without losing any of your images.

= Do I loose any images when I stop using the plugin? =

No. You won't loose any of your images after stopping the FileJet Pro plugin. We only cache your images so your original images are saved with your Wordpress instance. 

== Screenshots ==

1. FileJet Pro - credentials settings
2. FileJet Pro - overview
3. FileJet Pro - mutation settings
4. FileJet Pro - ignore list settings
5. FileJet Pro - image attributes settings
6. FileJet Pro - dashboard widget

== Changelog ==

= 1.3.6 =
* handle invalid statistic date input

= 1.3.5 =
* bugfixes and design improvements

= 1.3.4 =
* show plugin version in plugin settings page

= 1.3.3 =
* add support for special characters in image URLs

= 1.3.2 =
* add statistics and dashboard widget

= 1.3.1 =
* bugfix - ingore all src attributes with data URIs

= 1.3 =
* remove credentials when the plugin is uninstalled
* add management of image attributes which are picked up by FileJet
* add support for background-image within inline styles
* support for PHP 5.6 and more

= 1.2.2 =
* ignore base64 encoded images in src attribute
* guess host for relative image paths
* guess protocol for external images without protocol
* UX improvements (show Storage ID within settings screen)

= 1.2.1 =
* bugfix

= 1.2 =
* Improve settings UX
* Disable image replacement when credentials missing
* Introduce composer for external dependencies

= 1.1 =
* Added support for src-set
* Optimize images for the whole output not just content of the post
* Various bug fixes

= 1.0 =
* Initial version of the plugin.


