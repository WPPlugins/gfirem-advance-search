<?php
/**
 * @package    WordPress
 * @subpackage Formidable, gfirem_adv_search
 * @author     GFireM
 * @copyright  2017
 * @link       http://www.gfirem.com
 * @license    http://www.apache.org/licenses/
 */

wp_nonce_field( 'gfirem_adv_search_metabox_collect_settings', 'gfirem_adv_search_metabox_nonce' );
$go_gro      = '';
$disable_pro = '';
if ( gfirem_adv_search_fs::getFreemius()->is_free_plan() ) {
	$go_gro      = 'class="gfirem-disabled"';
	$disable_pro = 'disabled';
}
?>
    <p>
        <input type="checkbox" id="frm_search_enabled" name="frm_search_enabled" value="<?php echo "$enabled_adv_filtering"; ?>" <?php checked( $enabled_adv_filtering, '1' ); ?> /><?php _e( 'Enabled the advance filtering', 'gfirem_adv_search-locale' ); ?><br/>
    </p>
<?php if ( ! empty( $display->frm_where ) || ! empty( $display->frm_order_by ) ) : ?>
    <div <?php echo "$show_adv_view"; ?> class="frm_search_filters">
        <div class="frm_search_divider">
            <strong><?php _e( 'Select the Filters combinations', 'gfirem_adv_search-locale' ); ?></strong>
            <div class="frm_search_filter_item">
				<?php $i = 0;
				if ( count( $display->frm_where ) > 1 ):
					foreach ( $display->frm_where as $item ) :
						if ( empty( $item ) ) {
							continue;
						}
						$i ++;
						$field  = FrmField::getOne( $item );
						$name   = strlen( $field->name ) > 15 ? substr( $field->name, 0, 15 ) . "..." : $field->name;
						$filter = ( ! empty( $filters ) && ! empty( $filters[ $item ] ) ) ? $filters[ $item ]['filter'] : 'AND';
						echo "$name" . ' [' . esc_html( $field->id ) . '] ';
						if ( $i != count( $display->frm_where ) ): ?>
                            <select <?php echo disabled( count( $display->frm_where ), $i, true ); ?> name="frm_search_field[<?php echo esc_attr( $field->id ); ?>]" id="frm_search_field_filter">
                                <option <?php selected( $filter, 'AND', true ); ?> value="AND"><?php _e( 'AND', 'gfirem_adv_search-locale' ); ?></option>
                                <option <?php selected( $filter, 'OR', true ); ?> value="OR"><?php _e( 'OR', 'gfirem_adv_search-locale' ); ?></option>
                            </select>
						<?php endif; ?>
					<?php endforeach; ?>
				<?php else:
					_e( 'You need more than one filter.', 'gfirem_adv_search-locale' );
				endif; ?>
            </div>
        </div>
    </div>
    <div class="frm_search_divider">
        <strong <?php echo "$go_gro"; ?>><?php _e( 'Select Sort combinations from URL', 'gfirem_adv_search-locale' ); ?></strong><br/>
        <i><?php _e( 'Set the name of the URL parameters where you will put the order (ASC/DESC) for each field', 'gfirem_adv_search-locale' ); ?></i>
        <div class="frm_search_sort_item">
			<?php if ( ! empty( $display->frm_order_by ) ): ?>
				<?php
				foreach ( $display->frm_order_by as $item ) :
					if ( empty( $item ) ) {
						continue;
					}
					$i ++;
					if ( is_numeric( $item ) ) {
						$field = FrmField::getOne( $item );
					} else {
						$field       = new stdClass();
						$field->id   = $item;
						$field->name = gfirem_adv_search_meta_box::get_extra_option( $item );
					}
					$order = ( ! empty( $orders ) && ! empty( $orders[ $item ] ) ) ? $orders[ $item ] : '';
					$name  = strlen( $field->name ) > 15 ? substr( $field->name, 0, 15 ) . "..." : $field->name;
					echo "<strong >$name" . ' [' . esc_html( $field->id ) . '] </strong>';
					echo "<input " . $disable_pro . " type='text' name='frm_search_order[" . esc_attr( $field->id ) . "]' id='frm_search_field_order'  value='" . esc_attr( $order ) . "'/>&nbsp;&nbsp;";
				endforeach; ?>
			<?php endif; ?>
        </div>
    </div>
    <div class="frm_enabled_scroll_to_container">
        <strong <?php echo "$go_gro"; ?>><?php _e( 'Set the options to scroll', 'gfirem_adv_search-locale' ); ?></strong><br/>
        <i><?php _e( 'The selector can be (#selector|.selector) where the scroll be move, if you leave empty not be used. If you leave empty the url parameter the scroll will be trigger always, this can be more than one separated by come (,).', 'gfirem_adv_search-locale' ); ?></i>
        <div class="frm_search_sort_item">
            <table>
                <tr>
                    <td><strong>Selector</strong> <input <?php echo "$disable_pro"; ?> type="text" id="frm_enabled_scroll_to" name="frm_enabled_scroll_to" value="<?php echo "$frm_enabled_scroll_to"; ?>"/></td>
                    <td><strong>Padding</strong> <input <?php echo "$disable_pro"; ?> type="text" id="frm_enabled_scroll_padding" name="frm_enabled_scroll_padding" value="<?php echo "$frm_enabled_scroll_padding"; ?>"/></td>
                    <td><strong>Scroll if exist the URL parameter</strong> <input <?php echo "$disable_pro"; ?> type="text" id="frm_enabled_scroll_if_query" name="frm_enabled_scroll_if_query" value="<?php echo "$frm_enabled_scroll_if_query"; ?>"/></td>
                </tr>
            </table>
        </div>
    </div>

<?php else:
	_e( '<i>If you don\'t see anything yet, you need to save your View with a Filter(s). Look the <a target="_blank" href="https://formidableforms.com/knowledgebase/filtering-entries/">documentation</a></i>', 'gfirem_adv_search-locale' );
endif; ?>