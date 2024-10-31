<?php
/**
 * Plugin Name: Private WP - Members only
 * Plugin URI: https://www.alexander-fuchs.net/private-wp/
 * Description: This plugin redirects all not logged in users to the wp-login.php page.
 * Version: 1.0
 * Author: Alexander Fuchs
 * Author URI: https://www.alexander-fuchs.net/
 * License: GPLv2+
 * Text Domain: private-wp-members-only
 * Domain Path: /languages
 */

/**
 * Copyright (c) 2017 Alexander Fuchs (https://www.alexander-fuchs.net/)
 *
 * This program is free software; you can redistribute it and/or modify
 * it under the terms of the GNU General Public License, version 2 or, at
 * your discretion, any later version, as published by the Free
 * Software Foundation.
 *
 * This program is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the
 * GNU General Public License for more details.
 *
 * You should have received a copy of the GNU General Public License
 * along with this program; if not, write to the Free Software
 * Foundation, Inc., 51 Franklin St, Fifth Floor, Boston, MA 02110-1301 USA
 */

 
//This function redirects all not autheticated users
function pwmo_loggedin_users_only() {
	global $pagenow;
	//check if the current page is not the wp-logon.php page
	//check if the current user is logged in
	if(!is_user_logged_in() && $pagenow != 'wp-login.php') {
		auth_redirect();
	}
}

//use the wp hook to load this function on every page load
add_action( 'wp', 'pwmo_loggedin_users_only' );

//use this function to indicate, that the site is now private
function pwmo_notice() {
	load_plugin_textdomain( 'private-wp-members-only', FALSE, basename( dirname( __FILE__ ) ) . '/languages/' );
    ?>
    <div class="notice-warning notice pwmo-notice is-dismissible">
        <p><?php _e( 'This site is set to private and all non logged in users will be redirected to the login page.', 'private-wp-members-only' ); ?></p>
		<script>
		jQuery(document).on( 'click', '.pwmo-notice .notice-dismiss', function() {
			jQuery.ajax({
				url: ajaxurl,
				data: {
					action: 'pwmo_dismissed'
				}
			})

		})
		</script>
    </div>
    <?php
}

//add notification to admin panel
if( !get_option( 'pwmo-notice-dismissed', FALSE ) ) {
  add_action( 'admin_notices', 'pwmo_notice' );
}



/**
 * AJAX handler to store the state of dismissible notices.
 */
function pwmo_ajax_notice_handler() {
    // Pick up the notice "type" - passed via jQuery (the "data-notice" attribute on the notice)
    // Store it in the options table
    update_option( 'pwmo-notice-dismissed', TRUE );
}
add_action( 'wp_ajax_pwmo_dismissed', 'pwmo_ajax_notice_handler' );