<?php
/**
 * ElementorKit:
 *
 * Elements Welcome Page UI.
 *
 * @package ElementorKit
 * @since 1.0.0
 */

namespace ElementorKit\Backend;

use ElementorKit\Utils\Base;

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly.
}


/**
 * ElementorKit Welcome Page UI.
 *
 * @since 1.0.0
 */
class Welcome extends Base{

	/**
	 * Registers our main "Elements" menu in the sidebar
	 */
	public function admin_menu() {

    	$svg_icon = '<svg xmlns="http://www.w3.org/2000/svg" version="1.1" xmlns:xlink="http://www.w3.org/1999/xlink" width="512" height="512" x="0" y="0" viewBox="0 0 100 100" style="enable-background:new 0 0 512 512" xml:space="preserve" class=""><g><path d="M30.43 85.75C16.32 85.6 8.58 75.03 8.7 59.82c.06-16.08 6.61-31.84 17.97-43.23 7.78-7.22 18.53 3.5 11.33 11.3-8.41 8.43-13.25 20.08-13.3 31.99.14 10.27 3.6 11.52 12.5 8.4-2.43-6.98-2.09-15.63 1.08-25.52 1.72-5.36 4.19-11.43 9.52-15.8 6.2-5.08 16.67-6.54 23.45-.19 11.16 10.15 2.38 31.03-6.57 41.13 4.56-1.55 8.52-5.35 11.63-10.87 2.15-3.86 7.02-5.25 10.88-3.1s5.25 7.02 3.1 10.88c-3.4 6.11-9.56 14.18-19.55 17.91-7.83 2.92-16.36 2.21-22.99-1.59-5.19 2.6-11.79 4.68-17.33 4.62zm29.72-47.23c-5.36.07-8.57 14.37-8.51 19.92 3.85-4.21 6.77-9.28 8.31-14.72.79-2.81.63-4.62.4-5.19h-.2z" fill="#ffffff" opacity="1" data-original="#000000" class=""></path></g></svg>';

		$page = add_menu_page(
			__( 'ElementorKit', 'elementorkit' ),
			__( 'ElementorKit', 'elementorkit' ),
			'edit_posts',
			ELEMENTORKIT_SLUG,
			[ $this, 'admin_page_open' ],
			'data:image/svg+xml;base64,' . base64_encode($svg_icon),
			'58.6'
		);
		add_action( 'admin_print_scripts-' . $page, [ $this, 'admin_page_assets' ] );

		$submenu = add_submenu_page(
			ELEMENTORKIT_SLUG,
			__( 'ElementorKit', 'elementorkit' ),
			__( 'Welcome', 'elementorkit' ),
			'edit_posts',
			ELEMENTORKIT_SLUG,
			[ $this, 'admin_page_open' ]
		);

		$submenu = add_submenu_page(
			ELEMENTORKIT_SLUG,
			__( 'Template Kits', 'elementorkit' ),
			__( 'Template Kits', 'elementorkit' ),
			'edit_posts',
			ELEMENTORKIT_SLUG . '#/template-kits/premium-kits',
			[ $this, 'admin_page_open' ]
		);

		$submenu = add_submenu_page(
			ELEMENTORKIT_SLUG,
			__( 'Installed Kits', 'elementorkit' ),
			__( 'Installed Kits', 'elementorkit' ),
			'edit_posts',
			ELEMENTORKIT_SLUG . '#/template-kits/installed-kits',
			[ $this, 'admin_page_open' ]
		);

		$submenu = add_submenu_page(
			ELEMENTORKIT_SLUG,
			__( 'Photos', 'elementorkit' ),
			__( 'Photos', 'elementorkit' ),
			'edit_posts',
			ELEMENTORKIT_SLUG . '#/photos',
			[ $this, 'admin_page_open' ]
		);

		$submenu = add_submenu_page(
			ELEMENTORKIT_SLUG,
			__( 'Settings', 'elementorkit' ),
			__( 'Settings', 'elementorkit' ),
			'edit_posts',
			ELEMENTORKIT_SLUG . '#/settings',
			[ $this, 'admin_page_open' ]
		);

	}

	/**
	 * Called when the plugin page is opened.
	 */
	public function admin_page_open(){
		?>
		<div id="elementorkit-app-holder"></div>
		<script type="text/javascript">
			jQuery(function(){
        var appHolder = document.getElementById( 'elementorkit-app-holder' );
        if (appHolder && 'undefined' !== typeof window.ElementorKit) {
					window.ElementorKit.initBackend( appHolder );
        }
      })
		</script>
		<?php
	}

	/**
	 * Assets required for the admin page to render correctly (i.e. all our react stuff)
	 */
	public function admin_page_assets(){
		wp_enqueue_style( 'elementorkit-admin', ELEMENTORKIT_URI . 'assets/main.css', [], filemtime( ELEMENTORKIT_DIR . 'assets/main.css' ) );
		wp_enqueue_script( 'elementorkit-admin', ELEMENTORKIT_URI . 'assets/main.js', [], filemtime( ELEMENTORKIT_DIR . 'assets/main.js' ), true );
	}

}
