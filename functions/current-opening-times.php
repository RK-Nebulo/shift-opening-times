<?php

function current_opening_times( $output = 'times' ) {

	global $today_date, $yesterday_date, $date_format, $use_default_styling, $plugin_dir_path;

	$current_name = 'Today - ' . date('l jS F');
	$current_image = '<img src="/wp-content/plugins/opening-times/images/open-sign.png" />';
	$current_times = 'We are currently ';

	$day_id = ( is_bank_holiday( $today_date ) || ( is_bank_holiday( $yesterday_date ) && date('l') === 'Tuesday' ) ) ? 'bank_holiday' : strtolower( date('l') );

	if( get_field( $day_id, 'options' ) === 'closed' ) :
		$current_times .= 'closed';
		$current_image = str_replace( 'open-sign.png', 'closed-sign.png', $current_image );

	else :
		$open_time = get_field( $day_id . '_opening_time', 'options' );
		$close_time = get_field( $day_id . '_closing_time', 'options' );

		$open_hour = convert_time_to_number( $open_time );
		$close_hour = convert_time_to_number( $close_time );
		$now = date('Gi');

		$current_times .= ( $now >= $open_hour && $now < $close_hour ) ? 'open until ' . preg_replace('/:00 (am|pm)/', '$1', $close_time) : 'closed';
	endif;

	if( $use_default_styling === true ) {

		$current_opening_times = array( array( 'title' => $current_name, 'times' => $current_times ) );
		return opening_times_add_styling( $current_opening_times );

	} else {

		switch ( $output ) {
			case 'title':
				return $current_name;
				break;
			case 'image':
				return $current_image;
				break;
			case 'times':
				return $current_times;
				break;
			default:
				return $current_times;
		}

	}
}

function convert_time_to_number( $time_period ) {

	$time_pattern = '/(?<hours_minutes>\d{1,2}:\d{2}) (?<period>am|pm)/';

	preg_match($time_pattern, $time_period, $time );

	return str_replace( ':', '', $time['hours_minutes'] ) + ( $time['period'] === 'pm' ? 1200 : 0 );
}