=== Igefilter ===
Contributors: elukacs
Donate Link: 
Tags: bible, biblia, scripture, szentiras, ige
Requires at least: 3.1
Tested up to: 6.4
Stable tag: 1.1
License: GPLv2 or later
License URI: https://www.gnu.org/licenses/gpl-2.0.html

Az Igefilter a magyar bibliai hivatkozásokat automatikusan átalakítja online Bibliára mutató linkké.

== Description ==

Az Igefilter a magyar bibliai hivatkozásokat automatikusan átalakítja online Bibliára mutató linkké.

Például a János 3:16 hivatkozást átváltoztatja egy ilyen kattintható linkre: <a href="http://www.online-biblia.ro/bible/4/JHN/3#v16">János 3:16</a>

A jelenleg támogatott Bibliák az [Online Biblia](http://www.online-biblia.ro) weboldalon találhatóak:
1. Károli Gáspár Fordítás
2. King James Version
3. Traducerea Cornilescu
4. Revideált Károli (Veritas)
5. English Standard Version

== Other Notes ==

*********************************************************************************************
KÖSZÖNET
*********************************************************************************************

Ez a plugin a Scripturizer plugin kódját használja fel.

[Glen Davis] (www.glenandpaula.com)

Az eredeti Scripturizer plugin pedig a [Heal Your Church Website's Scripturizer plugin for Movable Type](http://www.healyourchurchwebsite.com/archives/001176.shtml) alapján készült.

*********************************************************************************************
HIBAJELENTÉS
*********************************************************************************************

[Bug Reports](http://dev.wp-plugins.org/newticket)

== Installation ==

1. Töltsd fel a plugin fájljait a `/wp-content/plugins/igefilter` könyvtárba, vagy installáld a plugint egyenesen a WordPress plugin-ok oldaláról
2. Aktiváld a plugint a 'Plugins' oldalon a WordPress-en belül
3. A beállításokon módosíthatsz a Settings->Igefilter oldalon

== Frequently Asked Questions ==

= Hogyan ismeri fel az Igefilter a Bibliai hivatkozásokat =

1. Az Igefilter a következő bibliai hivatkozásokat ismeri fel:
 - [Könyv] [Fejezet]
 - [Könyv] [Fejezet]:[Vers]
 - [Könyv] [Fejezet]:[Vers]-[vers]
 - Megjegyzés: a kettőspont helyett vesszőt is lehet használni
2. Az előbbi esetben a [Könyv] lehet egy teljes vagy rövidített könyvcím, számozással ellátva. A számozás több formátumban is szerepelhet:
 - 1, 2, 3
 - I, II, II
3. Az Igefilter felismeri a magyar bibliai könyvcímek legyakoribb rövidítéseit.

== Screenshots ==

N/A

== Changelog ==

= 1.1 =
* Added compatibility with PHP 7.

= 1.0 =
* Original release.

== Upgrade Notice ==

N/A