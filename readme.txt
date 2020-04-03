=== WP REST API Helper ===
Contributors: rabiulislamrobi
Tags: rest api, api, rest,
Requires at least: 4.7
Tested up to: 5.4
Requires PHP: 7.0
License: GPLv2 or later
License URI: https://www.gnu.org/licenses/gpl-2.0.html

A plugin to help out WP REST API.

== Description ==

A plugin to help out the default WordPress REST API Data.

## Credits
Please visit my <b>YouTube Channel</b>. I publish WordPress development related videos regularly. The channel name is <a style="text-decoration: none; display: inline-block; background: #282828; padding:6px 20px; color: #fff; border-radius:5px" href="https://www.youtube.com/channel/UCxn-5T3PreCovWDUW-iJTVA?view_as=subscriber" target="_blank">Robiz Show</a>

## Features ##
=================================================
# Post & Pages #
<b>Endpoint:</b> <br>
http://your-site.com/wp-json/wp/v2/posts <br>
http://your-site.com/wp-json/wp/v2/pages

* Get feature image url of a post. <small>[ Full, Large, Medium, Thumbnail ]</small>
* Get publish date in a human readable format.
* Get author details which includes user_nicename, user_url.
* Get post terms object. That includes term id, name, slug, description, parent, post count and url.

## Posts
<b>Endpoint:</b> http://your-site.com/wp-json/wp/v2/posts

* Featured image source stated as &nbsp;<code> featured_image_src </code>&nbsp; with multiple variations <small>[ Full, Large, Medium, Thumbnail ].</small>
* Post published date in more readable format stated as &nbsp; <code>published_on</code>&nbsp; <small>(e.g: Feb 20, 2020)</small>
* Author details stated as &nbsp;<code>author_details</code>.
* Post terms stated as &nbsp;<code>post_terms</code>&nbsp;. This contains an array of objects of the terms.

## Pages
<b>Endpoint:</b> http://your-site.com/wp-json/wp/v2/pages
* Featured image source stated as &nbsp;<code>featured_image_src</code>&nbsp; with multiple variations <small>[ Full, Large, Medium, thumbnail ).</small>

## Menus
<b>Endpoint:</b> http://your-site.com/wp-json/wp/v2/menus
* Provides full menu list of a theme based on menu location. object contains parent and its child information.

## Widgets
<b>Endpoint:</b> http://your-site.com/wp-json/wp/v2/widgets
* Provides the widget area list associated with the widgets.

## General Info
<b>Endpoint:</b> http://your-site.com/wp-json/wp/v2/general
* Provides the general information about the site.

== Installation ==

This section describes how to install the plugin and get it working.

e.g.

1. Upload the plugin files to the `/wp-content/plugins/wp-rest-api-helper` directory, or install the plugin through the WordPress plugins screen directly.
1. Activate the plugin through the 'Plugins' screen in WordPress
1. Use the Settings->Plugin Name screen to configure the plugin
1. (Make your instructions match the desired user flow for activating and installing your plugin. Include any steps that might be needed for explanatory purposes)

== Frequently Asked Questions ==


== Screenshots ==


== Changelog ==
# Release Version - 1.0.0
- Initial release.
# Release Version - 2.0.0
- Internal plugin structure changed.
# Release Version - 2.0.1
- Plugin updater added.

== Upgrade Notice ==

