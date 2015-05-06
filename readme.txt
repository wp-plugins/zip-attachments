=== Zip Attachments ===
Contributors: quicoto
Tags: attachments, download, zip, attachment, button
Requires at least: 4.0
Tested up to: 4.1
Stable tag: 1.4
License: GPLv2 or later
License URI: http://www.gnu.org/licenses/gpl-2.0.html

Add a "Download" button to your posts, pages or custom post types. This button will then create a zip file of the post attachments.

== Description ==

Simple and lightweight plugin to add a "Download" button to your posts, pages or custom post types.

This button will create a zip file of the post attachments on the fly and download it.

The output is very basic, no images, no fonts, no CSS. Just a simple button element.

= Features =

*   No output by default (check the Installation tab).
*   Easy to customizable with CSS.
*   Shortcode available.
* 	Download counter stored per post.
*	You chose the output text so no translation needed.
*   Works with posts, pages and custom post types.

= Requests =

Feel free to post a request but let's keep it simple and light.

= Ping me / Blame me =

Are you using the plugin? Do you like it? Do you hate it? Let me know!

* Twitter: [@ricard_dev](http://twitter.com/ricard_dev)
* Blog: [Rick's code](http://php.quicoto.com/)

== Installation ==

First of all activate the Plugin, then you have three choices:

= Functions.php =

You can show the button after all your content (posts, pages, custom post types) by pasting this snippet at the end of your __functions.php__ file of your theme:

`function za_button_print($content)
{
	return $content.za_show_button('Download');
}
add_filter('the_content', 'za_button_print');`


= Single.php (or similar) =

Alternatively you can print the button only in certain parts of your theme. Paste the following snippet wherever you want them to show.

Be aware, it should be within [the Loop](http://codex.wordpress.org/The_Loop).

`<?=function_exists('za_show_button') ? za_show_button("Download") : ''?>`

= Shortcode =

Finally you can use the shortcode inside your post content like so:

`[za_show_download_button text="Download the file"]`

As you can see you can use your own text, the default value is "Download Attachments".

= Download Counter =

Each method has a download counter, you need to add additional parameters:

A)

`function za_button_print($content)
{
	return $content.za_show_button('Download', 'true', '(% times)');
}
add_filter('the_content', 'za_button_print');`

B)

`<?=function_exists('za_show_button') ? za_show_button("Download", "true", "(% times)") : ''?>`

C)

`[za_show_download_button text="Download the file" counter="true" counter_format="(% times)"]`

NOTE: the default counter format is `(%)`, where `%` is actual number. The plugin will automatically replace this character with the download count.

== Frequently Asked Questions ==

= I activated the plugin and I don't see the button =

You must specify where do you want to show the button your theme or post, __check out the Installation instructions__.

= Can I customize the colors? =

Absolutely. Use your theme CSS file to customize the appearnce of the button. The button comes with two CSS classes:

`.za_download_button`
`.za_download_button_loading`

== Screenshots ==

1. Using the Twenty Fourteen Theme.
2. Using the Twenty Thirteen Theme.
3. Using the Twenty Twelve Theme.

== Changelog ==

= 1.4 =
* Code improvement to match WordPress best practices.

= 1.3 =
* Add a download counter.

= 1.2 =
* Fixed undefined variable for the plugin's path.

= 1.1 =
* Sanatize the filename to avoid errors with some titles.

= 1.0 =
* Initial release, you'll love it.

== Upgrade Notice ==

= 1.4 =
* Code improvement to match WordPress best practices.

= 1.3 =
* Add a download counter (check out the Installation instructions).

= 1.2 =
* Fixed undefined variable.

= 1.1 =
* Sanatize the filename to avoid errors with some titles.

= 1.0 =
* Initial release, you'll love it.
