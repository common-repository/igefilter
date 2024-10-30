<div>
	<h2>Igefilter Plugin Settings</h2>
	<p>Manage settings for the Igefilter Plugin</p>
	<form action="options.php" method="post">
		<?php 
		settings_fields('plugin_igefilter_options');
		do_settings_sections('igefilter');
		?>
		<?php submit_button( __( 'Save Settings', 'igefilter' ), 'primary', 'plugin_igefilter_options[submit]', false ); ?>
		<?php submit_button( __( 'Reset Defaults', 'igefilter' ), 'secondary', 'plugin_igefilter_options[reset]', false ); ?>
	</form>
</div>