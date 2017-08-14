<?php
/**
 * @package WordPress
 * @subpackage Formidable, formidable_search
 * @author GFireM
 * @copyright 2017
 * @link http://www.gfirem.com
 * @license http://www.apache.org/licenses/
 */

if ( ! defined( 'WPINC' ) ) {
	die;
}

class gfirem_adv_search_admin{
	
	public function __construct() {
		add_action( 'admin_menu', array( $this, 'admin_menu' ) );
		add_action( 'fs_is_submenu_visible_gfirem_adv_search', array( $this, 'handle_sub_menu' ), 10, 2 );
	}
	
	public function handle_sub_menu( $is_visible, $menu_id ) {
		if ( $menu_id == 'account' ) {
			$is_visible = false;
		}
		
		return $is_visible;
	}
	
	/**
	 * Adding the Admin Page
	 */
	public function admin_menu() {
		add_menu_page( __( 'GFireM Adv. Filter','gfirem_adv_search-locale' ), __( 'GFireM Adv. Filter','gfirem_adv_search-locale' ), 'manage_options', 'gfirem_adv_search', array( $this, 'screen' ), 'dashicons-search' );
	}
	
	public function screen() {
		gfirem_adv_search_fs::getFreemius()->get_logger()->entrance();
		if (gfirem_adv_search_fs::getFreemius()->is_registered()) {
			gfirem_adv_search_fs::getFreemius()->_account_page_load();
			gfirem_adv_search_fs::getFreemius()->_account_page_render();
		}
	}
}