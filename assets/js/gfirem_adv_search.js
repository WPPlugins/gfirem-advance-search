/*
 * @package WordPress
 * @subpackage Formidable, gfirem_adv_search
 * @author GFireM
 * @copyright 2017
 * @link http://www.gfirem.com
 * @license http://www.apache.org/licenses/
 */

jQuery(document).ready(function ($) {
	$('#frm_search_enabled').click(function (e) {
		var checked = $(this).is(':checked');
		if (checked) {
			$('.frm_search_filters').show();
			$(this).val('1');
		}
		else {
			$('.frm_search_filters').hide();
			$(this).val('0');
		}
	});
	
/* Premium Code Stripped by Freemius */

});