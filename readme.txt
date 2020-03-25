=== WP Rest API Helper ===
Contributors: rabiulislamrobi
Tags: rest api, rest, josn data, api, rest apu helper.
Requires at least: 4.7
Tested up to: 5.3
License: GPLv2 or later
License URI: https://www.gnu.org/licenses/gpl-2.0.html

A plugin to help out WP Rest API.

== Description == 
A plugin to help out the default WordPress Rest Api Data. 

== Features ==
-- Post & Pages --
Endpoint: http://your-site.com/wp-json/wp/v2/menus
1. Get feature image url of a post. [ Full, large, Medium, Thumbnail ]
2. Get publish date in a human readable format.
3. Get author details which includes user_nicename, user_url.
4. Get post terms object. That includes term id, name, slug, description, parent, post count and url.

-- Posts -- 
Endpoint: http://your-site.com/wp-json/wp/v2/posts
1. Featured image source stated as <pre>featured_image_src</pre> with multiple variations ( full, large, medium, thumbnail ).
2. Post published date in more readable format stated as <pre>published_on</pre>. (e.g: Feb 20, 2020)
3. Author details stated as <pre>author_details</pre>.
4. Post terms stated as <pre>post_terms</pre>. This contains an array of objects of the terms.

-- Pages -- 
Endpoint: http://your-site.com/wp-json/wp/v2/pages
1. Featured image source stated as <pre>featured_image_src</pre> with multiple variations ( full, large, medium, thumbnail ).

-- Menus -- 
Endpoint: http://your-site.com/wp-json/wp/v2/menus
1. Provides full menu list of a theme based on menu location. object contains parent and its child informations.

-- Widgets -- 
Endpoint: http://your-site.com/wp-json/wp/v2/widgets
1. Provides the widget area list associated with the widgets.

-- General Info -- 
Endpoint: http://your-site.com/wp-json/wp/v2/general
1. Provides the general information about the site.

= Changelog =
- Initial Release 1.0.0