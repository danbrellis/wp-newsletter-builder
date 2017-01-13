# wp-newsletter-builder
A Wordpress plugin that offers a back-end interface for populating a newsletter template with WP Posts, compiling it and exporting the HTML you can paste into Vertical Response, Constant Contact, etc. This is a plugin for developers so that they can create a HTML/CSS newsletter and use that as the template for their WP users to populate via WP Admin.

#Installation
Download the plugin file to your computer and unzip it
Using an FTP program, or your hosting control panel, upload the unzipped plugin folder to your WordPress installation's `wp-content/plugins/` directory.
Activate the plugin from the Plugins menu within the WordPress admin.

#Usage
While a newsletter template file is included in the plugin, you can add your own flare by making a copy of `wp-newsletter-builder/templates/content-newsletter.php` and putting it in `your-theme-folder/plugins/wp-newsletter-builder/templates/content-newsletter.php`. Then simply tweak the layouts and what's displayed to your heart's content!

#Note
The included template and stylesheet is based off and uses Zurb's Foundation for Emails. This means that the styles in the newsletter may come into conflict with those of your theme, especially if you are using Bootstrap as it uses many of the same classes. To overcome this, simply dequeue your theme's stylesheet when viewing a newsletter by adding this code to your functions.php


```php
function acb_news_wp_enqueue_scripts() {
	if ( 'e_newsletter' == get_post_type() && is_single() && !is_admin() ){
		wp_dequeue_style('[replace-with-theme-style-handle]');
	}
}
add_action('wp_enqueue_scripts', 'acb_news_wp_enqueue_scripts');
```
