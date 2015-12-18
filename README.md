Tax Meta Class
=======================
Contributors: bainternet
Requires at least: 3.5
Tested up to: 4.0
[![Analytics](https://ga-beacon.appspot.com/UA-50573135-3/tax-meta-class/main)](https://github.com/bainternet/Tax-Meta-Class)

Description
-----------
The Tax Meta Class is used by including it in your plugin r theme files and using its methods to 
Add meta fields for WordPress Taxonomies (categories,tags and custom taxonomies). It is meant to be very simple and 
straightforward.

This class is derived from My-Meta-Box (https://github.com/bainternet/My-Meta-Box script) which is 
a class for creating custom meta boxes for WordPress. 

Usage
--------
Take a look at the `class-usage-demo.php` file which can also be tested as a WordPress Plugin. 
Other options are available for each field which can be see in the 'Tax-meta-class.php' file, 

Changelog
---------
2.1.0
Added support for WordPress 4.4 native term meta table
added migration class

2.0.2
Fixed jquery ui not loading theme and better jquery ui to WP version handling.
Added a filter hook to change jQuery ui theme `tmc_jquery_ui_theme`.
Better preBind jQuery plgin included.

2.0.1
Fixed #93.

2.0.0
Too many changes to list :)
major ones are around the image and file fields which no use `url` instead of `src` and allow multiple images/files that are stored in two fields of urls and ids split by a pipe `|`.

1.9.9
Fix references for the class for PHP 5.4 compatibility

1.9.8
fixed issue #49

1.9.7
see issue #44

1.9.6
Added WYSIWYG in repater support and most other fields which never worked in the repeate block, now should work. issue #42

1.9.5
Fixed Typo in validation call Props @Screenfeed.

1.9.4
Added textdomain for l18n #39

1.9.3
Fixed issue #38 props to Nicola Peluchetti for finding it and giving my an idea of how to fix it.

1.9.2
Pull #36

1.9.1
Fixed Upload field issues.

1.9.0
Added 'multiple' => false to all field types as defualt.

= 1.8.9 =
fixed issue #27 (again).
fixed issues #28, #29 , #30.


= 1.8.8 =
fixed issue #27.

= 1.8.7 =
fixed issue #26.

= 1.8.6 =
fixed issue #25.

= 1.8.5 = 
fixed issue #21.

= 1.8.4 =
Renamed delete_taxonomy_metadata function to delete term meta on term deletion. pull #20.

= 1.8.3 =
Cleaned up Media uploader to simplify things. (parisholley)[https://github.com/parisholley]
Change the replacement of "INSERT TO POST" and is now done on client side.

= 1.8.2 =
Replaced get_stylesheet_directory_uri with get_template_directory_uri to work better with child themes.
Once again thanks to oobi. issue 17

= 1.8.1 =
added mete deletion on term deletion thanks to oobi

= 1.8 = 
fixed issue 12
fixed issue 13
fixed issue 14
fixed issue 15
changed "insert into post" on media uploader.

= 1.7.4 = 
Fixed bug caused by issue 2 fix.

= 1.7.3 = 
Fixed issue #2.

= 1.7.2 =
Fixed issue #11.

= 1.7.1 =
Fixed typo.

= 1.7 = 
Added strip slashes and get_tax_meta_strip function to avoid WordPress's native escape slashes.

= 1.6 =
Fixed file field issues.

= 1.5 =
Fixed repeater delete meta bug

= 1.4 =
updated addPosts in demo
and add_thickbox

= 1.3 =
Fixed WP debug errors

= 1.2=
Fixed debug Errors

= 1.0 =
* First Release


