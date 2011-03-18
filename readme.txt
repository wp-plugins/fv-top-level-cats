=== FV Top Level Categories ===
Contributors: FolioVision
Donate link: http://foliovision.com/seo-tools/wordpress/plugins/fv-top-level-categories
Tags: categories, permalink
Requires at least: 3.1
Tested up to: 3.1
Stable tag: 1.1

This is a fix of Top Level Category plugin for Wordpress 3.1.

== Description ==

This is a fix of Top Level Category plugin for Wordpress 3.1. It's purpose is to provide the same behavior as the original plugin, but in new Wordpress versions.

The Top Level Categories plugin allows you to remove the prefix before the URL to your category page. For example, instead of http://foliovision.com/category/work, you can use http://foliovision.com/work for the address of "work" category. WordPress doesn't allow you to have a blank prefix for categories (they insert `category/` before the name), this plugin works around that restriction.

[Support](http://foliovision.com/seo-tools/wordpress/plugins/fv-top-level-categories)

== Installation ==

1. Copy the `top-level-cats.php` file into your `wp-content/plugins` directory
1. Activate the plugin through the 'Plugins' menu in WordPress
1. That's it! :)

== Known Issues / Bugs ==

1. This plugin **will not work** if you have a permalink structure like `%postname` or `%category%/%postname%` -- there is currently no workaround

== Frequently Asked Questions ==

= My links are broken when using `%postname` or `%category%/%postname%` as my permalink structure =

This is a known issue, for which there is unfortunately no good workaround. If you add a suffix to your permalink structure (such as `.html`) you can fix this issue. For example, try `%category%/%postname%.html` -- I realize this is not ideal, but there is no good solution for this issue.

= How do I automatically redirect people from the old category permalink? =

We recommend that you use the [Redirection](http://wordpress.org/extend/plugins/redirection/) plugin and add your old an new category links, or use a Regex redirection

^/category/(.*) -> /$1

== Uninstall ==

1. Deactivate the plugin
1. That's it! :)

== Changelog ==

= 1.1.2 =
* fix for /category/child-category redirecting to /child-category page

= 1.1.1 =
* fix for deeper nested categories

= 1.1 =
* fix for WP 3.1

= 1.0.1 =
* original version
