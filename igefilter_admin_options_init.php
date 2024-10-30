<?php 

global $igefilter_options;
$igefilter_options = igefilter_get_options();
global $igefilter_options_default;
$igefilter_options_default = igefilter_get_options_default();
global $igefilter_translations;
$igefilter_translations = igefilter_get_translations();

// Register Plugin Settings
	
	// Register plugin_igefilter_options array to hold all Plugin options
	register_setting( 'plugin_igefilter_options', 'plugin_igefilter_options', 'igefilter_options_validate' );

// Add Plugin Settings Form Sections
	
	// Add a form section for the General Plugin settings
	add_settings_section('igefilter_settings_general', 'General Options', 'igefilter_settings_general_section_text', 'igefilter');
	
// Add Form Fields to General Settings Section
	
	// Add Default Translation setting to the General section
	add_settings_field('igefilter_setting_default_translation', 'Default Translation', 'igefilter_setting_default_translation', 'igefilter', 'igefilter_settings_general');
	// Add Substitution Mode setting to the General section
	add_settings_field('igefilter_setting_dynamic_substitution', 'Substitution Mode', 'igefilter_setting_dynamic_substitution', 'igefilter', 'igefilter_settings_general');
	// Add Link CSS Class setting to the General section
	add_settings_field('igefilter_setting_link_css_class', 'Link CSS Class', 'igefilter_setting_link_css_class', 'igefilter', 'igefilter_settings_general');
	// Add Open Link in New Tab/Window setting to the General section
	add_settings_field('igefilter_setting_link_target_blank', 'Open Link in New Tab/Window', 'igefilter_setting_link_target_blank', 'igefilter', 'igefilter_settings_general');
	

// Add Section Text for each Form Section

// General Settings Section
function igefilter_settings_general_section_text() { ?>
	<p><?php _e( 'Refer to the contextual help screen for descriptions and help regarding each Plugin option.', 'igefilter' ); ?></p>
<?php }

// Add form field markup for each Plugin option

// Default Translation Setting
function igefilter_setting_default_translation() {
	global $igefilter_options;;
	global $igefilter_options_default;
	$igefilter_default_translation = $igefilter_options_default['default_translation'];
	global $igefilter_translations;
	?>
	<p>
		<label for="igefilter_default_translation">
			<b><?php _e('Default Bible Translation', 'igefilter'); ?></b><br />
			<select name="plugin_igefilter_options[default_translation]">
		   <?php 
			ksort( $igefilter_translations );
			$translations_english = $igefilter_translations;
			foreach ( $translations_english as $translation_acronym => $translation_name ) { ?>
				<option <?php if ( $translation_acronym == $igefilter_options['default_translation'] ) echo 'selected="selected"'; ?> value="<?php echo esc_attr($translation_acronym); ?>"><?php echo esc_attr($translation_name); ?> (<?php echo esc_attr($translation_acronym); ?>)</option>
			<?php } ?>
			</select>
		</label>
	</p>
<?php }

// Dynamic Substitution Setting
function igefilter_setting_dynamic_substitution() {
	$igefilter_options = get_option( 'plugin_igefilter_options' ); ?>
	<p>
		<label for="igefilter_dynamic_substitution">
			<b><?php _e('igefilter Mode', 'igefilter'); ?></b><br />
			<select name="plugin_igefilter_options[dynamic_substitution]">
				<option <?php if ( true == $igefilter_options['dynamic_substitution'] ) echo 'selected="selected"'; ?> value="true">Dynamic</option>
				<option <?php if ( false == $igefilter_options['dynamic_substitution'] ) echo 'selected="selected"'; ?> value="false">Static</option>
			</select>
		</label>
	</p>
<?php }

// Link CSS Class Setting
function igefilter_setting_link_css_class() {
	$igefilter_options = get_option( 'plugin_igefilter_options' ); ?>
	<p>
		<label for="igefilter_esv_key">
			<b><?php _e('Link CSS Class', 'igefilter'); ?></b><br />
            <input type="text" name="plugin_igefilter_options[link_css_class]" value="<?php echo esc_attr($igefilter_options['link_css_class']); ?>" size="30" />
		</label>
	</p>
<?php }

// Open Link in New Window/Tab Setting
function igefilter_setting_link_target_blank() {
	$igefilter_options = get_option( 'plugin_igefilter_options' ); ?>
	<p>
		<label for="igefilter_link_target_blank">
			<b><?php _e('Open Link in New Window/Tab', 'igefilter'); ?></b><br />
			<select name="plugin_igefilter_options[link_target_blank]">
				<option <?php if ( true == $igefilter_options['link_target_blank'] ) echo 'selected="selected"'; ?> value="true">True (Open In New Tab/Window)</option>
				<option <?php if ( false == $igefilter_options['link_target_blank'] ) echo 'selected="selected"'; ?> value="false">False (Open In Same Tab/Window)</option>
			</select>
		</label>
	</p>
<?php }


// Validate data input before updating Plugin options
function igefilter_options_validate( $input ) {

	$reset_submit = ( isset( $input['reset'] ) ? true : false );
	
	if ( $reset_submit ) {
	  
	      global $igefilter_options_default;
	      
	      $valid_input['default_translation'] = $igefilter_options_default['default_translation'];
	      $valid_input['dynamic_substitution'] = $igefilter_options_default['dynamic_substitution'];
	      $valid_input['xml_show_hide'] = $igefilter_options_default['xml_show_hide'];
	      $valid_input['esv_key'] =  $igefilter_options_default['esv_key'];
	      $valid_input['xml_css'] =  $igefilter_options_default['xml_css'];
	      $valid_input['esv_query_options'] = $igefilter_options_default['esv_query_options'];
	      $valid_input['libronix'] = false;
	      $valid_input['link_css_class'] = $igefilter_options_default['link_css_class'];
	      $valid_input['link_target_blank'] = $igefilter_options_default['link_target_blank'];
	
	      return $valid_input;
	  
	} else {
	
	      $igefilter_options = igefilter_get_options();
	      
	      global $igefilter_translations;
	
	      $valid_translations_english = implode( '|', array_keys( $igefilter_translations ) );
	      
	      $valid_translations_all = $valid_translations_english;
	      
	      $valid_css_class = '[a-zA-Z]+[_a-zA-Z0-9-]*';
	      $invalid_css = array( '!', '@', '#', '$', '%', '^', '&', '*', '(', ')', '', '=', '+', ',', '.', '/', '<', '>', '?', ';', ':', '[', ']', '{', '}', '\\', '|', '\'', '\"' );

	      $valid_input = $igefilter_options;	
	
	      $valid_input['default_translation'] = ( strpos( $valid_translations_all, $input['default_translation'] ) !== false ? $input['default_translation'] : $igefilter_options['default_translation'] );
	      $valid_input['dynamic_substitution'] = ( $input['dynamic_substitution'] == 'true' ? true : false );
	      $valid_input['xml_show_hide'] = ( $input['xml_show_hide'] == 'true' ? true : false );	
	      $valid_input['xml_css'] =  wp_filter_nohtml_kses( $input['xml_css'] );
	      $valid_input['libronix'] = false;
	      $valid_input['link_css_class'] = wp_filter_nohtml_kses( str_ireplace( $invalid_css, '', ltrim( trim( $input['link_css_class'] ), "_-0..9" ) ) );
	      $valid_input['link_target_blank'] = ( $input['link_target_blank'] == 'true' ? true : false );
	      $valid_input['reset'] = false;
	
	      return $valid_input;
	
	}
}
?>