<?php
/**
 * @package           gfirem_adv_search
 *
 * @wordpress-plugin
 * Plugin Name:       GFireM Advance Search Filters
 * Description:       Formidable advance search filters, set it from the editor view
 * Version:           1.2.1
 * Author:            gfirem
 * License:           Apache License 2.0
 * License URI:       http://www.apache.org/licenses/
 *
 * Copyright 2017 Guillermo Figueroa Mesa (email: gfirem@gmail.com)
 *
 * Licensed under the Apache License, Version 2.0 (the "License");
 * you may not use this file except in compliance with the License.
 * You may obtain a copy of the License at
 *
 * http://www.apache.org/licenses/LICENSE-2.0
 *
 * Unless required by applicable law or agreed to in writing, software
 * distributed under the License is distributed on an "AS IS" BASIS,
 * WITHOUT WARRANTIES OR CONDITIONS OF ANY KIND, either express or implied.
 * See the License for the specific language governing permissions and
 * limitations under the License.
 */

if ( ! defined( 'WPINC' ) ) {
	die;
}

if ( ! class_exists( 'gfirem_adv_search' ) ) {
	
	class gfirem_adv_search {
		
		/**
		 * Instance of this class.
		 *
		 * @var object
		 */
		protected static $instance = null;
		protected $requirements;
		
		/**
		 * Initialize the plugin.
		 */
		private function __construct() {
			define( 'FSE_JS_PATH', plugin_dir_url( __FILE__ ) . 'assets/js/' );
			define( 'FSE_CSS_PATH', plugin_dir_url( __FILE__ ) . 'assets/css/' );
			define( 'FSE_VIEW_PATH', dirname( __FILE__ ) . DIRECTORY_SEPARATOR . 'view' . DIRECTORY_SEPARATOR );
			define( 'FSE_CLASSES_PATH', dirname( __FILE__ ) . DIRECTORY_SEPARATOR . 'classes' . DIRECTORY_SEPARATOR );
			define( 'FSE_INCLUDES_PATH', dirname( __FILE__ ) . DIRECTORY_SEPARATOR . 'includes' . DIRECTORY_SEPARATOR );
			
			require_once FSE_CLASSES_PATH . 'gfirem_adv_search_admin.php';
			new gfirem_adv_search_admin();
			require_once FSE_CLASSES_PATH . 'gfirem_adv_search_meta_box.php';
			new gfirem_adv_search_meta_box();
		}
		
		/**
		 * Return an instance of this class.
		 *
		 * @return object A single instance of this class.
		 */
		public static function get_instance() {
			// If the single instance hasn't been set, set it now.
			if ( null == self::$instance ) {
				self::$instance = new self;
			}
			
			return self::$instance;
		}
		
		/**
		 * Load the plugin text domain for translation.
		 */
		public function load_plugin_textdomain() {
			load_plugin_textdomain( 'gfirem_adv_search-locale', false, basename( dirname( __FILE__ ) ) . '/languages' );
		}
		
	}
	
	require_once dirname( __FILE__ ) . DIRECTORY_SEPARATOR . 'includes' . DIRECTORY_SEPARATOR . 'Requirements.php';
	require_once dirname( __FILE__ ) . DIRECTORY_SEPARATOR . 'classes' . DIRECTORY_SEPARATOR . 'gfirem_adv_search_requirements.php';
	$gfirem_adv_search_requirements = new gfirem_adv_search_requirements();
	if ( $gfirem_adv_search_requirements->satisfied() ) {
		require_once dirname( __FILE__ ) . DIRECTORY_SEPARATOR . 'classes' . DIRECTORY_SEPARATOR . 'gfirem_adv_search_fs.php';
		new gfirem_adv_search_fs();
		add_action( 'plugins_loaded', array( 'gfirem_adv_search', 'get_instance' ) );
	} else {
		$fauxPlugin = new WP_Faux_Plugin( 'GFireM Adv Search', $gfirem_adv_search_requirements->getResults() );
		$fauxPlugin->show_result( plugin_basename( __FILE__ ) );
	}
}
