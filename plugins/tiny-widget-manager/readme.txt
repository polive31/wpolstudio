=== Tiny Widget Manager ===
Contributors: WPol Studio
Donate link: https://ko-fi.com/wpolstudio
Tags: widgets, visibility, admin, logic, translation-ready
Requires at least: 5.0
Tested up to: 6.8
Requires PHP: 8.0
Stable tag: 1.0
License: GPLv2 or later
License URI: https://www.gnu.org/licenses/gpl-2.0.html
**Tiny Widget Manager** enhances the WordPress widget system by letting you control the visibility of each widget based on various conditions

== Description ==

**Tiny Widget Manager** enhances the WordPress widget system by letting you control the visibility of each widget based on various conditions, directly from the widget admin panel.

IMPORTANT : In order for Tiny Widget Manager to operate, you will need to display the *legacy widget interface* instead of the block-based widget page (this can be achieved in the plugins' settings page).

The power of TWIM resides in the variety of logic conditions it supports :
- Specific pages or posts
- Post types (custom or built-in)
- Archive types (category, tag, author, date)
- Logged-in users or user roles
- Device type (mobile, tablet, desktop)
- Overall logic between conditions (either all conditions need to be true, or just one)

Since each condition group can have its own show/hide setting, the combinations are endless. You have therefore a *total control* on your widget's visibility !

In addition, a text input lets you add *custom CSS classes* to your widgets. No need for another plugin just to style them!

This plugin adds a simple interface under each widget, with checkboxes and selection inputs to define visibility rules, making widget management smarter and more flexible.

No extra configuration required. Just install and go!

== Installation ==

1. Upload the plugin files to the `/wp-content/plugins/tiny-widget-manager` directory, or install the plugin through the WordPress plugins screen directly.
2. Activate the plugin through the ‘Plugins’ screen in WordPress.
3. Go to **Appearance > Widgets**, open any widget, and configure its visibility using the new panel.

== Frequently Asked Questions ==

= Does this plugin work with block-based (FSE) themes? =
No. Tiny Widget Manager currently supports classic widget-based themes only.

= Does it support custom post types and taxonomies? =
Yes, visibility rules can be applied to any registered post type or archive.

= Can I add custom CSS classes to widgets? =
Yes! There’s a built-in text input to apply your own classes—no third-party plugin needed.

= Will it slow down my site? =
No, the plugin is lightweight and adds minimal overhead. Visibility logic is processed server-side only when needed.

== Screenshots ==
1. Condition selector showing pages
2. Condition selector showing single post type
2. Condition selector showing archive pages
3. Condition selector showing user roles
4. Condition selector showing device types
5. Input for adding custom CSS classes to widgets
7. Example setup: show widget only for "recipe" post type

== Changelog ==

= 1.0 =
* Initial release with support for page, post type, archive, user, and device-based visibility rules.
* Added input for applying custom CSS classes to widgets.

== Upgrade Notice ==

= 1.0 =
First stable version. No upgrade steps necessary.
