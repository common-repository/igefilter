<?php
/*
 * Plugin Name: Igefilter
 * Version: 1.1
 * Plugin URI: http://dev.wp-plugins.org/wiki/igefilter
 * Description: Changes Bible references to hyperlinks.
 * Author: Online Biblia
 * Author URI: http://www.online-biblia.ro/
 * License:       GNU General Public License, v2 (or newer)
 * License URI:  http://www.gnu.org/licenses/old-licenses/gpl-2.0.html
 * 
 * Original PERL MovableType Plugin Copyright 2002-2004 Dean Peters
 * Port to PHP WordPress Plugin Copyright Glen Davis
 *
 * This program is free software; you can redistribute it and/or modify
 * it under the terms of the GNU General Public License as published by
 * the Free Software Foundation; either version 2 of the License, or
 * (at your option) any later version.
 *
 * This program is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.
 *  
 * Permission is hereby granted, free of charge, to any person obtaining a
 * copy of this software and associated documentation files (the "Software"),
 * to deal in the Software without restriction, including without limitation
 * the rights to use, copy, modify, merge, publish, distribute, sublicense,
 * and/or sell copies of the Software, and to permit persons to whom the
 * Software is furnished to do so, subject to the following conditions:
 * 
 * The above copyright notice and this permission notice shall be included in
 * all copies or substantial portions of the Software.
 *
*/

define('IGEFILTER_DEFAULT_BIBLE_TRANSLATION', '4');

/**
 * Get avaliable translations
 */
function igefilter_get_translations() {
	$translations = array(
		'1'=>'Károli Gáspár Fordítás', // Online Biblia
		'2'=>'King James Version', // Online Biblia
		'3'=>'Traducerea Cornilescu', // Online Biblia
		'4'=>'Revideált Károli (Veritas)', // Online Biblia
		'5'=>'English Standard Version', // Online Biblia
	);
	return apply_filters( 'igefilter_translations', $translations );
};
global $igefilter_translations;
$igefilter_translations = igefilter_get_translations();


/**
 * Get default options
 */
function igefilter_get_options_default() {
	$default_options = array(
		'default_translation' => '4',
		'dynamic_substitution' => true,
		'xml_show_hide' => false,
		'esv_key' => 'IP',
		'xml_css' => 'white-space: pre; display: none; padding: 10px; border: dotted blue 1px; border-left: solid blue 5px; color: black;',
		'esv_query_options' => 'action=doPassageQuery&include-passage-references=true&include-short-copyright=true&include-audio-link=false&output-format=plain-text&include-passage-horizontal-lines=false&include-heading-horizontal-lines=false&line-length=60&include-headings=false&include-subheadings=false&include-footnotes=false',
		'libronix' => false,
		'link_css_class' => 'igefilter',
		'link_target_blank' => false
	);
	return apply_filters( 'igefilter_default_options', $default_options );
};
global $igefilter_options_default;
$igefilter_options_default = igefilter_get_options_default();


/**
 * Add Plugin options to variable array
 * 
 * @Since 2.0
 * 
 */	
function igefilter_get_options() {
	// Get the option defaults
	$option_defaults = igefilter_get_options_default();
	// Globalize the variable that holds the Theme options
	global $igefilter_options;
	// Parse the stored options with the defaults
	$igefilter_options = wp_parse_args( get_option( 'plugin_igefilter_options', array() ), $option_defaults );
	// Return the parsed array
	return $igefilter_options;
}
global $igefilter_options;
$igefilter_options = igefilter_get_options();


/**
 * igefilter admin options hook
 */
global $igefilter_admin_options_hook;

/**
 * Plugin initialization function
 * Defines default options as an array
 * If option settings from earlier versions of the Plugin exist,
 * copies the setting into the options array, and deletes the old option
 * 
 * @Since 2.0
 * 
 */	
function igefilter_init() {

	// set options equal to defaults
	global $igefilter_options_default;
	global $igefilter_options;
	$igefilter_options = get_option( 'plugin_igefilter_options' );
	
	$igefilter_options_initial = ( ! $igefilter_options ? $igefilter_options_default : $igefilter_options );
	
	// if options exist from previous Plugin version, update options array with old option settings
	// and delete old database options
	foreach( $igefilter_options_initial as $key => $value ) {
		if( $existing = get_option( 'igefilter_' . $key ) ) {
			$igefilter_options_initial[$key] = $existing;
			delete_option( 'igefilter_' . $key );
		}
	}
	
	// Add/update the database options array
	update_option( 'plugin_igefilter_options', $igefilter_options_initial );
}

/**
 * Plugin admin options page
 * 
 * @Since 1.55
 * 
 */	
// Function to add admin options page
function igefilter_menu() {
	global $igefilter_admin_options_hook;
	$igefilter_admin_options_hook = add_options_page('Options', 'igefilter', 'manage_options', 'igefilter', 'igefilter_admin_options_page');
}
// Admin options page markup 
// Moved to separate file for ease of management
function igefilter_admin_options_page() {
	include_once( 'igefilter_admin_options_page.php' );
}

// Codex Reference: http://codex.wordpress.org/Settings_API
// Codex Reference: http://codex.wordpress.org/Data_Validation
// Reference: http://ottopress.com/2009/wordpress-settings-api-tutorial/
// Reference: http://planetozh.com/blog/2009/05/handling-plugins-options-in-wordpress-28-with-register_setting/
function igefilter_admin_init(){
	include_once( 'igefilter_admin_options_init.php' );
}

// Admin options page contextual help markup
// Separate file for ease of management
function igefilter_contextual_help( $contextual_help, $screen_id, $screen ) {		
	global $igefilter_admin_options_hook;
	include_once( 'igefilter_admin_options_help.php' );
	if ( $screen_id == $igefilter_admin_options_hook ) {
		$contextual_help = $text;
	}
	
return $contextual_help;
}

/**
 * Link to admin options page in Plugin Action links on Manage Plugins page
 * 
 * @Since 2.0
 * 
 */	
function igefilter_actlinks( $links ) {
	$igefilter_settings_link = '<a href="options-general.php?page=igefilter">Settings</a>'; 
	$links[] = $igefilter_settings_link;
	return $links; 
}



/**
 * function scripturize()
 * 
 * Split the_content accordingly, and only attempt to add scripture references to text that
 * is inside of anchor tags, pre tags, code tags, [bible] shortcodes, or that is part of a tag attribute.
 * 
 * @Since 1.2
 * 
 */	
function igefilter_scripturize( $text = '', $bible = NULL ) {
	
	global $igefilter_options;
	
	if ( ! isset( $bible ) ) {
		$bible = $igefilter_options['default_translation'];
	}
    // skip everything within a hyperlink, a <pre> block, a <code> block, or a tag
    // we skip inside tags because something like <img src="nicodemus.jpg" alt="John 3:16"> should not be messed with
	$anchor_regex = '<a\s+href.*?<\/a>';
	$pre_regex = '<pre>.*<\/pre>';
	$code_regex = '<code>.*<\/code>';
	$other_plugin_regex= '\[bible\].*\[\/bible\]'; // for the ESV Wordpress plugin (out of courtesy)
	$other_plugin_block_regex='\[bibleblock\].*\[\/bibleblock\]'; // ditto
	$tag_regex = '<(?:[^<>\s]*)(?:\s[^<>]*){0,1}>'; // $tag_regex='<[^>]+>';
	$split_regex = "/((?:$anchor_regex)|(?:$pre_regex)|(?:$code_regex)|(?:$other_plugin_regex)|(?:$other_plugin_block_regex)|(?:$tag_regex))/i";
	$parsed_text = preg_split( $split_regex, $text, -1, PREG_SPLIT_DELIM_CAPTURE );
	$linked_text = '';

	while ( list( $key, $value ) = each( $parsed_text ) ) {
      if ( preg_match( $split_regex, $value ) ) {
         $linked_text .= $value; // if it is an HTML element or within a link, just leave it as is
      } else {
        $linked_text .= igefilter_scripturizeAddLinks( $value, $bible ); // if it's text, parse it for Bible references
      }
  }
  return $linked_text;
}

/**
 * function scripturizeAddLinks()
 * 
 * Search filtered text from the_content for Scripture references, and if found replace with hyperlink
 * 
 * @Since 1.2
 * 
 */	
function igefilter_scripturizeAddLinks( $text = '', $bible = NULL ) {

	global $igefilter_translations;
	global $igefilter_options;

	if ( ! isset( $bible ) ) {
		$bible = $igefilter_options['default_translation'];
	}
    $volume_regex = '1|2|3|4|5|1\.|2\.|3\.|4\.|5\.|I|II|III|IV|V';

    $book_regex  = 'M&oacute;zes|J&oacute;zsu&eacute;|B&iacute;r&aacute;k|Ruth|S&aacute;muel|Kir&aacute;lyok|Kr&oacute;nika|Ezsdr&aacute;s|Neh&eacute;mi&aacute;s|Eszter';
    $book_regex .= '|J&oacute;b|Zsolt&aacute;rok|P&eacute;ldabesz&eacute;dek|Pr&eacute;dik&aacute;tor|&Eacute;nekek &eacute;neke|&Eacute;zsai&aacute;s|Jeremi&aacute;s|Jeremi&aacute;s Siralmai|Ez&eacute;kiel|D&aacute;niel|H&oacute;se&aacute;s|J&oacute;el|&Aacute;m&oacute;s|Abdi&aacute;s|J&oacute;n&aacute;s|Mike&aacute;s|N&aacute;hum|Habakuk|Sof&oacute;ni&aacute;s|Aggeus|Haggeus|Zakari&aacute;s|Malaki&aacute;s';
    $book_regex .= '|M&aacute;t&eacute;|M&aacute;rk|Luk&aacute;cs|J&aacute;nos|Apostolok Cselekedetei|R&oacute;ma|Korintus|Galata|Ef&eacute;zus|Filippi|Koloss&eacute;|Thessalonika|Tim&oacute;teus|Titusz|Filemon|Zsid&oacute;khoz &iacute;rt lev&eacute;l|Jakab|P&eacute;ter|J&aacute;nos|J&uacute;d&aacute;s|Jelen&eacute;sek';
    // UTF8
    $book_regex .= '|Mózes|Józsué|Bírák|Ruth|Sámuel|Királyok|Krónika|Ezsdrás|Nehémiás|Eszter';
    $book_regex .= '|Jób|Zsoltárok|Példabeszédek|Prédikátor|Énekek éneke|Ézsaiás|Jeremiás|Jeremiás Siralmai|Ezékiel|Dániel|Hóseás|Jóel|Ámós|Abdiás|Jónás|Mikeás|Náhum|Habakuk|Sofóniás|Aggeus|Haggeus|Zakariás|Malakiás';
    $book_regex .= '|Máté|Márk|Lukács|János|Apostolok Cselekedetei|Róma|Korintus|Galata|Efézus|Filippi|Kolossé|Thessalonika|Timóteus|Titusz|Filemon|Zsidókhoz írt levél|Jakab|Péter|János|Júdás|Jelenések';

//split these up from the Perl code because I want to be able to have an optional period at the end of just the abbreviations

    $abbrev_regex  = 'M&oacute;z|J&oacute;zs|Bir|B&iacute;r|S&aacute;m|Kir|Kr&oacute;n|Ezsd|Neh|Esz|Eszt';
    $abbrev_regex .= '|Zsolt|Zsolt&aacute;r|P&eacute;ld|Pr&eacute;d|&Eacute;n|&Eacute;zs|Jer|Sir|JSir|Jsir|Ez|Ez&eacute;k|D&aacute;n|H&oacute;s|&Aacute;m|Ab|Abd|J&oacute;n|Mik|N&aacute;h|Hab|Sof|Agg|Hag|Zak|Mal';
    $abbrev_regex .= '|Mt|M&aacute;t|Mk|Lk|Luk|Jn|J&aacute;n|ApCsel|Csel|R&oacute;m|Kor|Gal|Ef|Ef&eacute;z|Fil|Kol|Thess|Thesz|Thessz|Tim|Tit|Filem|Zsid|Zsid&oacute;k|Jak|Pt|P&eacute;t|J&uacute;d|Jel';
    // UTF8
    $abbrev_regex .= '|Móz|Józs|Bir|Bír|Sám|Kir|Krón|Ezsd|Neh|Esz|Eszt';
    $abbrev_regex .= '|Zsolt|Zsoltár|Péld|Préd|Én|Ézs|Jer|Sir|JSir|Jsir|Ez|Ezék|Dán|Hós|Ám|Ab|Abd|Jón|Mik|Náh|Hab|Sof|Agg|Hag|Zak|Mal';
    $abbrev_regex .= '|Mt|Mát|Mk|Lk|Luk|Jn|Ján|ApCsel|Csel|Róm|Kor|Gal|Ef|Eféz|Fil|Kol|Thess|Thesz|Thessz|Tim|Tit|Filem|Zsid|Zsidók|Jak|Pt|Pét|Júd|Jel';

    $book_regex='(?:'.$book_regex.')|(?:'.$abbrev_regex.')\.?';

    $verse_regex="\d{1,3}(?:[:,]\d{1,3})?(?:[-,]?\d+)*[-:\d]*"; // felismeri a kovetkezoket: Mt. 5:2, Jn 5,6, 1Kor. 1:12-2:11

//    $translation_regex = 'NIV|NASB|AMP|NLT|KJV|ESV|CEV|NET|NKJV|KJ21|ASV|WE|YLT|DARBY|WYC|NIV-UK|TNIV|MSG|NIRV';
//    $passage_regex = '/(?:('.$volume_regex.')\s?)?('.$book_regex.')\s('.$verse_regex.')(?:\s?[,-]?\s?((?:'.$translation_regex.')|\s?\((?:'.$translation_regex.')\)))?/';
    $passage_regex = '/(?:('.$volume_regex.')\s?)?('.$book_regex.')\s('.$verse_regex.')(?:\s?[,-]?\s?((?:NIV|KJV)|\s?\((?:NIV|KJV)\)))?/';

//    $replacement_regex = "igefilter_scripturizeLinkReference('\\0','\\1','\\2','\\3','\\4','$bible')";

//    $text = preg_replace_callback( $passage_regex, $replacement_regex, $text );
  $wrapper = new IgeFilterCallbackWrapper();
  $wrapper->bible = $bible;
  $text = preg_replace_callback($passage_regex,
    array(
      &$wrapper,
      "igefilterCallback",
    ),
     $text);

    return $text; // TODO: make this an array, to return text, plus OT/NT (for original languages)
}


/**
 * A simple class to wrap our callback function.
 *
 * We need this because we need to pass the Bible parameter in.
 * The preg_replace callback is only passed the matched groups.
 */
class IgeFilterCallbackWrapper {

  /**
   * A function just to work on a Bible reference, and turn into a link.
   *
   * It is passed the details of the incoming Bible reference to, and also the
   * original reference, so that the reference can be surrounded by an anchor
   * linking to the relevant site which has the text for that verse. It then
   * passes the transformed reference back out so that it can be replaced in the
   * wider body of text from which it came.
   * Two arguments for book; the first will be populated if no volume is given.
   * The second will be populated if there's a volume number.
   */
//function scripturizeLinkReference($reference='',$volume='',$book='',$verse='',$translation='',$user_translation='') {

  public function igefilterCallback($matches) {
    $reference = $matches[0];
    $book1 = $matches[2];
    $volume = $matches[1];
//    $book2 = $matches[3];
    $verse = $matches[3];
    $translation = isset($matches[5]) ? $matches[5] : NULL;
    $user_translation = $this->bible;

   global $igefilter_options;
   //echo $reference .'|'. $book .'|' . $volume .'|'. $verse .'|'. $translation .'|';

   $link_target = ( $igefilter_options['link_target_blank'] ? ' target="_blank"' : '' );

    $book = ($book1 == '') ? $book2 : $book1;
    if ($volume) {
       $volume = str_replace('IV','4',$volume);
       $volume = str_replace('V','5',$volume);
       $volume = str_replace('III','3',$volume);
       $volume = str_replace('II','2',$volume);
       $volume = str_replace('I','1',$volume);
       $volume = $volume{0}; // will remove st,nd,and rd (presupposes regex is correct)
    }

//   $user_translation = $this->bible;
   if (!$translation) {
	if (!$user_translation) {
        	$translation = IGEFILTER_DEFAULT_BIBLE_TRANSLATION; //   $translation = '4';
      	}
     	else {
        	$translation = $user_translation;
      	}
   }
   //if necessary, just choose part of the verse reference to pass to the web interfaces
   //they wouldn't know what to do with John 5:1-2, 5, 10-13 so I just give them John 5:1-2
   //this doesn't work quite right with something like 1:5,6 - it gets chopped to 1:5 instead of converted to 1:5-6
   if ($verse) $verse = strtok($verse,'& ');

   switch ($translation) {
        case '1': // Károli Gáspár Fordítás
             $link = "https://www.online-biblia.ro/bible/1";
             break;
        case '2': // King James Version
             $link = "https://www.online-biblia.ro/bible/2";
             break;
        case '3': // Traducerea Cornilescu
             $link = "https://www.online-biblia.ro/bible/3";
             break;
        case '5': // English Standard Version
             $link = "https://www.online-biblia.ro/bible/5";
             break;

        default: // Revideált Károli (Veritas) - online-biblia.ro
             $link = "https://www.online-biblia.ro/bible/4";
             break;
    }

    $title = 'Ige';
    $chapter = trim(strtok($verse,':,'));
    $verses = trim(strtok('-,'));
    $book = igefilter_scripturizeNumbering($volume, $book);

    // $link = sprintf('<a href="%s/%s/%s/%s#v%s" class="reform" title="%s">%s</a>',$link,htmlentities(urlencode($book)),$chapter,$verses,$verses,$title,trim($reference));
    $link = sprintf('<a class="%s"%s href="%s/%s/%s#v%s" class="reform" title="%s">%s</a>',$igefilter_options['link_css_class'], $link_target,$link,htmlentities(urlencode($book)),$chapter,$verses,$title,trim($reference));

    return $link;
}
}


function igefilter_scripturizeNumbering($volume='', $book='') {
    $book = $volume.' '.$book;
    $book = preg_replace('/\s+/', '', $book); //strip whitespace
    $book = preg_replace('/\.+/', '', $book); //strip dot
    $book = preg_replace('/1+/', '', $book);  //strip 1
    $book = preg_replace('/2+/', '', $book);  //strip 2
    $book = preg_replace('/3+/', '', $book);  //strip 3
    $book = preg_replace('/4+/', '', $book);  //strip 4
    $book = preg_replace('/5+/', '', $book);  //strip 5

    switch ($book) {

// Old Testament Books
	case 'M&oacute;zes':			$book='1';break;

	case 'J&oacute;zsu&eacute;':		$book='6';break;
	case 'B&iacute;r&aacute;k':		$book='7';break;
	case 'Ruth':				$book='8';break;
	case 'S&aacute;muel':			$book='9';break;

	case 'Kir&aacute;lyok':			$book='11';break;

	case 'Kr&oacute;nika':			$book='13';break;
	case 'Ezsdr&aacute;s':			$book='15';break;
	case 'Neh&eacute;mi&aacute;s':		$book='16';break;
	case 'Eszter':				$book='17';break;
	case 'J&oacute;b':			$book='18';break;
	case 'Zsolt&aacute;rok':		$book='19';break;
	case 'P&eacute;ldabesz&eacute;dek':	$book='20';break;
	case 'Pr&eacute;dik&aacute;tor':	$book='21';break;
	case '&Eacute;nekek &eacute;neke':	$book='22';break;
	case '&Eacute;zsai&aacute;s':		$book='23';break;
	case 'Jeremi&aacute;s':			$book='24';break;
	case 'Jeremi&aacute;s Siralmai':	$book='25';break;
	case 'Ez&eacute;kiel':			$book='26';break;
	case 'D&aacute;niel':			$book='27';break;
	case 'H&oacute;se&aacute;s':		$book='28';break;
	case 'J&oacute;el':			$book='29';break;
	case '&Aacute;m&oacute;s':		$book='30';break;
	case 'Abdi&aacute;s':			$book='31';break;
	case 'J&oacute;n&aacute;s':		$book='32';break;
	case 'Mike&aacute;s':			$book='33';break;
	case 'N&aacute;hum':			$book='34';break;
	case 'Habakuk':				$book='35';break;
	case 'Sof&oacute;ni&aacute;s':		$book='36';break;
	case 'Aggeus':				$book='37';break;
	case 'Haggeus':				$book='37';break;
	case 'Zakari&aacute;s':			$book='38';break;
	case 'Malaki&aacute;s':			$book='39';break;

// Old Testament Abbreviations
	case 'M&oacute;z':			$book='1';break;
	case 'J&oacute;zs':			$book='6';break;
	case 'Bir':				$book='7';break;
	case 'B&iacute;r':			$book='7';break;
	case 'S&aacute;m':			$book='9';break;
	case 'Kir':				$book='11';break;
	case 'Kr&oacute;n':			$book='13';break;
	case 'Ezsd':				$book='15';break;
	case 'Neh':				$book='16';break;
	case 'Esz':				$book='17';break;
	case 'Eszt':				$book='17';break;
	case 'Zsolt':				$book='19';break;
	case 'Zsolt&aacute;r':			$book='19';break;
	case 'P&eacute;ld':			$book='20';break;
	case 'Pr&eacute;d':			$book='21';break;
	case '&Eacute;n':			$book='22';break;
	case '&Eacute;zs':			$book='23';break;
	case 'Jer':				$book='24';break;
	case 'Sir':				$book='25';break;
	case 'JSir':				$book='25';break;
	case 'Jsir':				$book='25';break;
	case 'Ez':				$book='26';break;
	case 'Ez&eacute;k':			$book='26';break;
	case 'D&aacute;n':			$book='27';break;
	case 'H&oacute;s':			$book='28';break;
	case '&Aacute;m':			$book='30';break;
	case 'Ab':				$book='31';break;
	case 'Abd':				$book='31';break;
	case 'J&oacute;n':			$book='32';break;
	case 'Mik':				$book='33';break;
	case 'N&aacute;h':			$book='34';break;
	case 'Hab':				$book='35';break;
	case 'Sof':				$book='36';break;
	case 'Agg':				$book='37';break;
	case 'Hag':				$book='37';break;
	case 'Zak':				$book='38';break;
	case 'Mal':				$book='39';break;

// New Testament Books
	case 'M&aacute;t&eacute;':		$book='40';break;
	case 'M&aacute;rk':			$book='41';break;
	case 'Luk&aacute;cs':			$book='42';break;
	case 'J&aacute;nos':			$book='43';break;
	case 'Apostolok Cselekedetei':		$book='44';break;
	case 'R&oacute;ma':			$book='45';break;
	case 'Korintus':			$book='46';break;

	case 'Galata':				$book='48';break;
	case 'Ef&eacute;zus':			$book='49';break;
	case 'Filippi':				$book='50';break;
	case 'Koloss&eacute;':			$book='51';break;
	case 'Thessalonika':			$book='52';break;

	case 'Tim&oacute;teus':			$book='54';break;

	case 'Titusz':				$book='56';break;
	case 'Filemon':				$book='57';break;
	case 'Zsid&oacute;khoz &iacute;rt lev&eacute;l':	$book='58';break;
	case 'Jakab':				$book='59';break;
	case 'P&eacute;ter':			$book='60';break;

	case 'J&uacute;d&aacute;s':		$book='65';break;
	case 'Jelen&eacute;sek':		$book='66';break;

// New Testament Abbreviations
	case 'Mt':				$book='40';break;
	case 'M&aacute;t':			$book='40';break;
	case 'Mk':				$book='41';break;
	case 'Luk':				$book='42';break;
	case 'Lk':				$book='42';break;
	case 'Jn':				$book='43';break;
	case 'J&aacute;n':			$book='43';break;
	case 'ApCsel':				$book='44';break;
	case 'Csel':				$book='44';break;
	case 'R&oacute;m':			$book='45';break;
	case 'Kor':				$book='46';break;
	case 'Gal':				$book='48';break;
	case 'Ef':				$book='49';break;
	case 'Ef&eacute;z':			$book='49';break;
	case 'Fil':				$book='50';break;
	case 'Kol':				$book='51';break;
	case 'Thess':				$book='52';break;
	case 'Thesz':				$book='52';break;
	case 'Thessz':				$book='52';break;
	case 'Tim':				$book='54';break;
	case 'Tit':				$book='56';break;
	case 'Filem':				$book='57';break;
	case 'Zsid':				$book='58';break;
	case 'Zsid&oacute;k':			$book='58';break;
	case 'Jak':				$book='59';break;
	case 'Pt':				$book='60';break;
	case 'P&eacute;t':			$book='60';break;
	case 'J&uacute;d':			$book='65';break;
	case 'Jel':				$book='66';break;

// UTF8

// Old Testament Books
case 'Mózes': $book='1';break;
case 'Józsué': $book='6';break;
case 'Bírák': $book='7';break;
case 'Ruth': $book='8';break;
case 'Sámuel': $book='9';break;
case 'Királyok': $book='11';break;
case 'Krónika': $book='13';break;
case 'Ezsdrás': $book='15';break;
case 'Nehémiás': $book='16';break;
case 'Eszter': $book='17';break;
case 'Jób': $book='18';break;
case 'Zsoltárok': $book='19';break;
case 'Példabeszédek': $book='20';break;
case 'Prédikátor': $book='21';break;
case 'Énekek éneke': $book='22';break;
case 'Ézsaiás': $book='23';break;
case 'Jeremiás': $book='24';break;
case 'Jeremiás Siralmai': $book='25';break;
case 'Ezékiel': $book='26';break;
case 'Dániel': $book='27';break;
case 'Hóseás': $book='28';break;
case 'Jóel': $book='29';break;
case 'Ámós': $book='30';break;
case 'Abdiás': $book='31';break;
case 'Jónás': $book='32';break;
case 'Mikeás': $book='33';break;
case 'Náhum': $book='34';break;
case 'Habakuk': $book='35';break;
case 'Sofóniás': $book='36';break;
case 'Aggeus': $book='37';break;
case 'Haggeus': $book='37';break;
case 'Zakariás': $book='38';break;
case 'Malakiás': $book='39';break;

// Old Testament Abbreviations
case 'Móz': $book='1';break;
case 'Józs': $book='6';break;
case 'Bir': $book='7';break;
case 'Bír': $book='7';break;
case 'Sám': $book='9';break;
case 'Kir': $book='11';break;
case 'Krón': $book='13';break;
case 'Ezsd': $book='15';break;
case 'Neh': $book='16';break;
case 'Esz': $book='17';break;
case 'Eszt': $book='17';break;
case 'Zsolt': $book='19';break;
case 'Zsoltár': $book='19';break;
case 'Péld': $book='20';break;
case 'Préd': $book='21';break;
case 'Én': $book='22';break;
case 'Ézs': $book='23';break;
case 'Jer': $book='24';break;
case 'Sir': $book='25';break;
case 'JSir': $book='25';break;
case 'Jsir': $book='25';break;
case 'Ez': $book='26';break;
case 'Ezék': $book='26';break;
case 'Dán': $book='27';break;
case 'Hós': $book='28';break;
case 'Ám': $book='30';break;
case 'Ab': $book='31';break;
case 'Abd': $book='31';break;
case 'Jón': $book='32';break;
case 'Mik': $book='33';break;
case 'Náh': $book='34';break;
case 'Hab': $book='35';break;
case 'Sof': $book='36';break;
case 'Agg': $book='37';break;
case 'Hag': $book='37';break;
case 'Zak': $book='38';break;
case 'Mal': $book='39';break;

// New Testament Books
case 'Máté': $book='40';break;
case 'Márk': $book='41';break;
case 'Lukács': $book='42';break;
case 'János': $book='43';break;
case 'Apostolok Cselekedetei': $book='44';break;
case 'Róma': $book='45';break;
case 'Korintus': $book='46';break;
case 'Galata': $book='48';break;
case 'Efézus': $book='49';break;
case 'Filippi': $book='50';break;
case 'Kolossé': $book='51';break;
case 'Thessalonika': $book='52';break;
case 'Timóteus': $book='54';break;
case 'Titusz': $book='56';break;
case 'Filemon': $book='57';break;
case 'Zsidókhoz írt levél': $book='58';break;
case 'Jakab': $book='59';break;
case 'Péter': $book='60';break;
case 'Júdás': $book='65';break;
case 'Jelenések': $book='66';break;

// New Testament Abbreviations
case 'Mt': $book='40';break;
case 'Mát': $book='40';break;
case 'Mk': $book='41';break;
case 'Luk': $book='42';break;
case 'Lk': $book='42';break;
case 'Jn': $book='43';break;
case 'Ján': $book='43';break;
case 'ApCsel': $book='44';break;
case 'Csel': $book='44';break;
case 'Róm': $book='45';break;
case 'Kor': $book='46';break;
case 'Gal': $book='48';break;
case 'Ef': $book='49';break;
case 'Eféz': $book='49';break;
case 'Fil': $book='50';break;
case 'Kol': $book='51';break;
case 'Thess': $book='52';break;
case 'Thesz': $book='52';break;
case 'Thessz': $book='52';break;
case 'Tim': $book='54';break;
case 'Tit': $book='56';break;
case 'Filem': $book='57';break;
case 'Zsid': $book='58';break;
case 'Zsidók': $book='58';break;
case 'Jak': $book='59';break;
case 'Pt': $book='60';break;
case 'Pét': $book='60';break;
case 'Júd': $book='65';break;
case 'Jel': $book='66';break;

	default:$book = substr($book,0,3);
    }

    if ($volume) { if ($book == '43') $book = 62;  $book = $book + $volume -1; }

    switch ($book) { // Transform to BLS code

	case '1':	$book='GEN';break;
	case '2':	$book='EXO';break;
	case '3':	$book='LEV';break;
	case '4':	$book='NUM';break;
	case '5':	$book='DEU';break;
	case '6':	$book='JOS';break;
	case '7':	$book='JUG';break;
	case '8':	$book='RUT';break;
	case '9':	$book='1SM';break;
	case '10':	$book='2SM';break;
	case '11':	$book='1KG';break;
	case '12':	$book='2KG';break;
	case '13':	$book='1CH';break;
	case '14':	$book='2CH';break;
	case '15':	$book='EZR';break;
	case '16':	$book='NEH';break;
	case '17':	$book='EST';break;
	case '18':	$book='JOB';break;
	case '19':	$book='PS';break;
	case '20':	$book='PRO';break;
	case '21':	$book='ECC';break;
	case '22':	$book='SON';break;
	case '23':	$book='ISA';break;
	case '24':	$book='JER';break;
	case '25':	$book='LAM';break;
	case '26':	$book='EZE';break;
	case '27':	$book='DAN';break;
	case '28':	$book='HOS';break;
	case '29':	$book='JOE';break;
	case '30':	$book='AMO';break;
	case '31':	$book='OBA';break;
	case '32':	$book='JON';break;
	case '33':	$book='MIC';break;
	case '34':	$book='NAH';break;
	case '35':	$book='HAB';break;
	case '36':	$book='ZEP';break;
	case '37':	$book='HAG';break;
	case '38':	$book='ZEC';break;
	case '39':	$book='MAL';break;
	case '40':	$book='MAT';break;
	case '41':	$book='MAK';break;
	case '42':	$book='LUK';break;
	case '43':	$book='JHN';break;
	case '44':	$book='ACT';break;
	case '45':	$book='ROM';break;
	case '46':	$book='1CO';break;
	case '47':	$book='2CO';break;
	case '48':	$book='GAL';break;
	case '49':	$book='EPH';break;
	case '50':	$book='PHL';break;
	case '51':	$book='COL';break;
	case '52':	$book='1TS';break;
	case '53':	$book='2TS';break;
	case '54':	$book='1TM';break;
	case '55':	$book='2TM';break;
	case '56':	$book='TIT';break;
	case '57':	$book='PHM';break;
	case '58':	$book='HEB';break;
	case '59':	$book='JAM';break;
	case '60':	$book='1PE';break;
	case '61':	$book='2PE';break;
	case '62':	$book='1JN';break;
	case '63':	$book='2JN';break;
	case '64':	$book='3JN';break;
	case '65':	$book='JUD';break;
	case '66':	$book='REV';break;

	default:$book = substr($book,0,3);
    }

    return $book;
  }


function igefilter_scripturizePost($post_ID) {
    global $wpdb;
	
	$tableposts=$wpdb->posts;

    $postdata=$wpdb->get_row("SELECT * FROM $tableposts WHERE ID = '$post_ID'");

    $content = igefilter_scripturize($postdata->post_content);

    $wpdb->query("UPDATE $tableposts SET post_content = '$content' WHERE ID = '$post_ID'");
    
    return $post_ID;
}

function igefilter_scripturizeComment($comment_ID) {
    global $wpdb;
    
	$tablecomments=$wpdb->comments;

    $postdata=$wpdb->get_row("SELECT * FROM $tablecomments WHERE ID = '$comment_ID'");

    $content = igefilter_scripturize($postdata->comment_content);

    $wpdb->query("UPDATE $tablecomments SET comment_content = '$content' WHERE ID = '$comment_ID'");
    
    return $comment_ID;
}

##### ADD ACTIONS AND FILTERS
// Initialize Plugin options
add_action('activate_igefilter/igefilter.php', 'igefilter_init' );
// add the admin settings and such
add_action('admin_init', 'igefilter_admin_init');
// Load the Admin Options page
add_action('admin_menu', 'igefilter_menu');
// Add contextual help to Admin Options page
add_action('contextual_help', 'igefilter_contextual_help', 10, 3);
// Add a Settings link to Plugin Action Links on Manage Plugins page
add_filter('plugin_action_links_'.plugin_basename(__FILE__), 'igefilter_actlinks', 10, 1 );
// Load the javascript if the xml show/hide option is turned on
if ( isset( $igefilter_options['xml_show_hide'] ) && $igefilter_options['xml_show_hide'] ) { 
    add_action('wp_head', 'esvShowHideHeader', 10);
    add_action('admin_head', 'esvShowHideHeader', 5);
}
// Update content per Dynamic/Static mode setting
if ( isset( $igefilter_options['dynamic_substitution'] ) && $igefilter_options['dynamic_substitution'] ) {
	add_filter('the_content','igefilter_scripturize');
	add_filter('comment_text','igefilter_scripturize');
} else {
	add_action('publish_post','igefilter_scripturizePost');
	add_action('comment_post','igefilter_scripturizeComment');
	add_action('edit_post','igefilter_scripturizePost');
	add_action('edit_comment','igefilter_scripturizeComment');
	// note, adding the edit_post action guarantees that if you add or change a scripture reference the link will be inserted
	// HOWEVER, it will prevent you from removing a link you don't want!
}
?>
