<?php

/**
 * @package    WordPress
 * @subpackage Formidable, gfirem_adv_search
 * @author     GFireM
 * @copyright  2017
 * @link       http://www.gfirem.com
 * @license    http://www.apache.org/licenses/
 */
if ( !defined( 'WPINC' ) ) {
    die;
}
class gfirem_adv_search_fs
{
    /**
     * Instance of this class.
     *
     * @var object
     */
    protected static  $instance = null ;
    public function __construct()
    {
        $this->gfirem_adv_search_fs();
    }
    
    /**
     * @return Freemius
     */
    public static function getFreemius()
    {
        global  $gfirem_adv_search_fs ;
        return $gfirem_adv_search_fs;
    }
    
    // Create a helper function for easy SDK access.
    public function gfirem_adv_search_fs()
    {
        global  $gfirem_adv_search_fs ;
        
        if ( !isset( $gfirem_adv_search_fs ) ) {
            // Include Freemius SDK.
            require_once dirname( __FILE__ ) . '/../includes/freemius/start.php';
            $gfirem_adv_search_fs = fs_dynamic_init( array(
                'id'             => '906',
                'slug'           => 'gfirem_adv_search',
                'type'           => 'plugin',
                'public_key'     => 'pk_a73d66ca3939d2b76c2bca1d8aa22',
                'is_premium'     => false,
                'has_addons'     => false,
                'has_paid_plans' => true,
                'menu'           => array(
                'slug'    => 'gfirem_adv_search',
                'support' => false,
            ),
                'is_live'        => true,
            ) );
        }
        
        return $gfirem_adv_search_fs;
    }
    
    /**
     * Return an instance of this class.
     *
     * @return object A single instance of this class.
     */
    public static function get_instance()
    {
        // If the single instance hasn't been set, set it now.
        if ( null == self::$instance ) {
            self::$instance = new self();
        }
        return self::$instance;
    }
    
    public static function get_current_plan()
    {
        $site = faa_fs::getFreemius()->get_site();
        if ( !empty($site) ) {
            if ( !empty($site->plan) ) {
                
                if ( !empty($site->plan) ) {
                    return $site->plan->name;
                } else {
                    return 'free';
                }
            
            }
        }
        return 'free';
    }

}