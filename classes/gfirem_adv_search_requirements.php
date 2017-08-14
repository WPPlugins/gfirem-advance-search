<?php
/**
 * @package    WordPress
 * @subpackage Formidable, gfirem_adv_search
 * @author     GFireM
 * @copyright  2017
 * @link       http://www.gfirem.com
 * @license    http://www.apache.org/licenses/
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

class gfirem_adv_search_requirements extends WP_Requirement {
	
	public function __construct( $text_domain = 'gfirem_requirements' ) {
		parent::__construct( $text_domain );
	}
	
	/**
	 * Set the plugins requirements
	 *
	 * @return array
	 */
	function getRequirements() {
		$requirements                = array();
		$requirement                 = new WP_PHP_Requirement();
		$requirement->minimumVersion = '5.3.0';
		array_push( $requirements, $requirement );
		$requirement                 = new WP_WordPress_Requirement();
		$requirement->minimumVersion = '4.4';
		array_push( $requirements, $requirement );
		$requirement          = new WP_Plugins_Requirement();
		$requirement->plugins = array(
			array( 'id' => 'formidable/formidable.php', 'name' => 'Formidable', 'min_version' => '2.0.0' )
		);
		array_push( $requirements, $requirement );
		$requirement        = new WP_Class_Requirement();
		$requirement->class = array( 'FrmProHooksController' => 'Need Formidable Pro installed' );
		array_push( $requirements, $requirement );
		
		return $requirements;
	}
}