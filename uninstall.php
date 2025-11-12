<?php
// If uninstall not called from WordPress exit
if ( ! defined( 'WP_UNINSTALL_PLUGIN' ) ) {
	exit();
}

delete_option( 'elementorkit_version' );
delete_option( 'elementorkit_install_time' );
delete_option( 'elementorkit_options' );

// Remove the scheduled task.
wp_clear_scheduled_hook( 'elementorkit_cron' );