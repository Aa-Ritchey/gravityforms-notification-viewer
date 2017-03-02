<?php

//////
// All the code for checking whether Gravity Forms is installed.
//

// Display a notice to back-end users when Gravity Forms is not active.
add_action( 'admin_init', 'gfnv_admin_init' );
function gfnv_admin_init() {

	// TODO: admin_init and is_admin, are they redundant?
	if ( is_admin() ) {
	
		if ( ! gfnv_is_gravityforms_active() ) {
			add_action( 'admin_notices', 'gfnv_warn_of_inactive_plugin' );
			return;
		}
	
		//
	
	}
}

// Simply checks if Gravity Forms is active.
function gfnv_is_gravityforms_active() {
	return is_plugin_active('gravityforms/gravityforms.php');
}

// Notify the back-end user that Gravity Forms Navigation Viewer will not work.
function gfnv_warn_of_inactive_plugin() {
	?>
	<div class="notice notice-warning">
		<p><?= gfnv_plugin_not_installed_text() ?></p>
	</div>
	<?php
}

