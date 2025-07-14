<?php defined('ABSPATH') or die('This script cannot be accessed directly.');

/**
 * Control panel for indexing filter items.
 */

$us_filter_indexer = US_Filter_Indexer::instance();

$button_atts = array(
	'class' => 'usof-button for_index_filters',
);

// Disable button if no used params on the website
$button_description = '';
if ( ! $us_filter_indexer->get_used_filter_params() ) {
	$button_atts['class'] .= ' disabled';
	$button_description = '<div class="usof-index-data-message">' . sprintf( __( 'To enable indexing, add the "%s" element with "%s" enabled.', 'us' ), __( 'List Filter', 'us' ), __( 'Faceted Filtering', 'us' ) ) . '</div>';
}

if ( $us_filter_indexer->is_indexing() ) {
	$button_atts['class'] .= ' indexing';
}

$js_data = array(
	'buttonLabel' => __( 'Index filter items', 'us' ),
	'stopButtonLabel' => __( 'Stop indexing', 'us' ),
	'message' => array(
		'indexing' => __( 'Indexing', 'us' ) . ' 1%',
	),
	'ajaxData' => array(
		'action' => 'us_index_filters',
		'_nonce' => wp_create_nonce( 'us_index_filters_by_ajax' ),
	),
);

if ( ( $progress = $us_filter_indexer->get_progress() ) !== -1 ) {
	$js_data['message']['progress'] = sprintf( __( 'Indexing', 'us' ) . ' %s%%', $progress );
}

$button_atts['onclick'] = us_pass_data_to_js( $js_data, /*onclick*/FALSE );

// Output
$output = '<div class="usof-index-data">';

$output .= '<div class="usof-index-data-item for_count">';
$output .= sprintf( '%s: <span>%s</span>', __( 'Number of rows', 'us' ), $us_filter_indexer->get_row_count() );
$output .= '</div>';
$output .= '<div class="usof-index-data-item for_date">';
$output .= sprintf( '%s: <span>%s</span>', __( 'Last indexed', 'us' ), $us_filter_indexer->get_last_indexed() );
$output .= '</div>';

$output .= '</div>'; // usof-index-data

$output .= '<div ' . us_implode_atts( $button_atts ) . '>';
$output .= '<span class="usof-button-text">' . $js_data['buttonLabel'] . '</span>';
$output .= '</div>'; // usof-button

$output .= '<span class="usof-preloader"></span>';
$output .= '<div class="usof-message hidden"></div>';
$output .= $button_description;

echo $output;
