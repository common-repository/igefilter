=== Igefilter ===
Contributors: elukacs
Donate Link: 
Tags: bible, biblia, scripture, szentiras
Requires at least: 3.1
Tested up to: 6.4
Stable tag: 1.1
License: GPLv2 or later
License URI: https://www.gnu.org/licenses/gpl-2.0.html

Igefilter will convert Hungarian Bible references in your posts and comments into hyperlinks to online Bibles.

== Description ==

Igefilter will convert Hungarian Bible references in your posts and comments into hyperlinks to online Bibles.

For example, it will change János 3:16 into something like
<a href="http://www.online-biblia.ro/bible/4/JHN/3#v16">János 3:16</a>

Bibles currently supported are from [Online Biblia](http://www.online-biblia.ro):
1. Károli Gáspár Fordítás
2. King James Version
3. Traducerea Cornilescu
4. Revideált Károli (Veritas)
5. English Standard Version

== Other Notes ==

*********************************************************************************************
CREDITS
*********************************************************************************************

This plugin was derived from the Scripturizer plugin.

[Glen Davis] (www.glenandpaula.com)

The Scripturizer plugin was derived from [Heal Your Church Website's Scripturizer plugin for Movable Type](http://www.healyourchurchwebsite.com/archives/001176.shtml).

*********************************************************************************************
REPORTING BUGS
*********************************************************************************************

[Bug Reports](http://dev.wp-plugins.org/newticket)

== Installation ==

This section describes how to install the plugin and get it working.

e.g.

1. Upload the plugin files to the `/wp-content/plugins/plugin-name` directory, or install the plugin through the WordPress plugins screen directly.
2. Activate the plugin through the 'Plugins' screen in WordPress
3. Use the Settings->Plugin Name screen to configure the plugin


== Frequently Asked Questions ==

= How Igefilter Recognizes Scripture References =

1. Igefilter will recognize the following type of Scripture references:
 - [Book] [Chapter]
 - [Book] [Chapter]:[Verse]
 - [Book] [Chapter]:[Verse]-[verse]
 - Note: comma can also be used instead of a colon
2. Where [Book] can be a full name or an abbreviation, with prefix as applicable. Note that prefixes can be in several formats:
 - 1, 2, 3
 - I, II, II
3. Igefilter will recognize most common Hungarian Bible book abbreviations.

== Screenshots ==

N/A

== Changelog ==

= 1.1 =
* Added compatibility with PHP 7.

= 1.0 =
* Original release.

== Upgrade Notice ==

N/A