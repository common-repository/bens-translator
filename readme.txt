=== Plugin Name ===
Contributors: Ben O'Sullivan
Donate link: http://benosullivan.co.uk/bens-translator/
Tags: global translator, translate, translator, translate blog, translate google, translate analytics, widget, google translations, automatic translator, multilanguage
Requires at least: 2.9.2
Tested up to: 2.9.2
Stable tag: 1.7.2

Bens Translator is a plugin for Wordpress that generates translated pages using Google Translator.
The pages are cached by your webserver and displayed as a normal webpage.
This allows indexing by Google and other search engines to boost your traffic.

== Description ==

Bens Translator is a plugin for Wordpress that generates translated pages using Google Translator.
The pages are cached by your webserver and displayed as a normal webpage.
This allows indexing by Google and other search engines to boost your traffic.

== Features ==

Search Engine Optimized: it uses the permalinks by adding the language code at the beginning of all your URI. 
For example the english version on www.domain.com/mycategory/mypost will be automatically transformed in www.domain.com/de/mycategory/mypost

Fast Caching System: new fast, smart, optimized, self-cleaning and built-in caching system. 
Drastically reduction of the risk of temporarily ban from translation engines.

Fully configurable layout: you can easily customize the appearance of the translation bar by choosing between a TABLE or DIV based layout for the flags bar.
You can also select the number of translations to make available to your visitors.

No database modifications: Bens Translator is not intrusive. 
It doesn't create or alter any table on your database, this feature permits to obtain better performance by not running extra database queries.

== Installation ==
http://benosullivan.co.uk/bens-translator/setup-guide-bens-translator/

== Frequently Asked Questions ==
http://benosullivan.co.uk/bens-translator/help-guide-bens-translator/

== Screenshots ==

1. Administration interface in WordPress 2.8
2. Translated Pages Interface

== Changelog ==

= Known Bugs =
* When changing permalink structure from on to off, plugin settings need to be updated or you will get a 404 error

= 1.7.2 = 
* FIXED   - Text links not displaying

= 1.7.1 = 
* ADDED   - Tags and Categories to cache management page
* CHANGED - Cache management with jquery and customisable views options (thanks to bloguedegeek.net)

= 1.7 =
* ADDED   - Cache Management Page (You can Backup, Delete and Manually Flush the cache)
* FIXED   - Minor Bug Fixes
* CHANGED - Number of pages shown in cache browser now 20 at a time

= 1.6.2 =
* ADDED   - Added Romanian Language

= 1.6.1 = 
* FIXED   - Page not translated page "looping"
* ADDED   - Option to turn Validation engine on/off in settings
* REMOVED - Wordpress Proxy Support (Will be back)

= 1.6 = 
* CHANGED - File directory error checking
* ADDED   - Wordpress Proxy Support

= 1.5 =
* ADDED   - New page showing what pages are translated
* FIXED   - Directory not writable 0777 error
* FIXED   - filemtime error

= 1.4.1 =
* FIXED   - 500/403 "Redirect Loop" error
* FIXED   - Translation stopped after language bar
* ADDED   - Customisable Template for redirect

= 1.4 =
* ADDED   - Italian Translation (Provided By )
* FIXED   - Adding random characters to the url would show original page (with invalid url)
* FIXED   - Empty redirect in Google Webmaster, removed Extra Frame in google translation
* FIXED   - Pages wouldn't Expire to Stale Cache
* UPDATED - Estimated pages to translate is now more accurate
* ADDED   - Meta Language tag to translated pages
* ADDED   - Translated pages will now validate

= 1.3 =
* CHANGED - Changed all variables from gltr prefix to bentr, To prevent conflicts with Global Translator
* CHANGED - Changed how Bens-translator handles google returning an error (Works without the widget/flag bar on the page)
* ADDED   - Added support for Wordpress po/mo translation framework (No Translations Yet)
* CHANGED - Changed Language drop down list to include localisation
* ADDED   - Check to see if permalinks have been changed and then automatically updates the .htaccess
* CHANGED - Changed redirect status code to 307 "temporary redirect"
* CHANGED - Changed Alt attribute of flag from "flag" to the language

= 1.2.1 =
* CHANGED - Language settings for many languages incorrect

= 1.2 =
* FIXED - Bug where a blog not using permalinks wouldn't redirect to google translate correctly**
* ADDED - Text links instead of country flags
* FIXED - Bug where translated pages would be added to google sitemaps regardless of setting**

= 1.1 =
* ADDED- Optional user editable header can be displayed on any translated page

= 1.0 =
* First release

= 0.9 =
* ADDED - Checks for php/wordpress version for compatability
* REMOVED - Extra Translation Engines, Now uses Google solely
* REMOVED - Languages not allowed by Google Adsense
* FIXED - number of files translated error, where folders would be included in total**
* ADDED - Files translated is now automatically loaded in admin panel
* ADDED - Estimated total number of files to be translated added to admin panel
* CHANGED - English flag changed to the Union Flag (Union Jack)
* CHANGED - Cache files and directory organised for easier viewing
* CHANGED - Default expiry changed to 30 days
* CHANGED - Default translation engine connect time changed to 580 seconds
* CHANGED - Options page reorganised
* FIXED - Bug where pages were moved to stale cache, and were younger than the user cache time**
* ADDED - Clears Database entrys on deactivation and creates fresh variables on activation

** Bug from Global Translator