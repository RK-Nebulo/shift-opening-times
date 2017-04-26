<?php

	function get_opening_hours( $day_id ) {
		if( get_field( $day_id, 'options' ) === 'closed' ) {
			return 'Closed';

		} else {
			$open_hour = get_field( $day_id . '_opening_time', 'options' );
			$close_hour = get_field( $day_id . '_closing_time', 'options' );
			return preg_replace('/:00 (am|pm)/', '$1', 'Open from ' . $open_hour . ' to ' . $close_hour);

		}
	}

	function weekly_opening_times( $type = 'list' ) {

	global $use_default_styling;

	$weekly_days = array( 'monday', 'tuesday', 'wednesday', 'thursday', 'friday', 'saturday', 'sunday' );

	$weekly_sequences = array();

	$i = 1;

	foreach( $weekly_days as $id => $day_id ) {

		if( $type === 'list' ) {

			$weekly_sequences[$i] = array( 'days' => array( ucfirst( $day_id ) ), 'times' => get_opening_hours( $day_id ) );
			$i++;

		} elseif( $type === 'sequence' ) {

			$day_hours = get_opening_hours( $day_id );
			$prev_day_hours = get_opening_hours( $weekly_days[ ( (6+$id) % 7 ) ] );
			$next_day_hours = get_opening_hours( $weekly_days[ ( (1+$id) % 7 ) ] );

			$weekly_sequences[$i]['days'][] = ucfirst( $day_id );
			$weekly_sequences[$i]['times'] = isset( $sequences[$i]['times'] ) ? $sequences[$i]['times'] : $day_hours;

			if( $day_hours !== $next_day_hours ) {	$i++; }

		}
	}

	foreach( $weekly_sequences as $open_hours ) {

		$first_day = reset( $open_hours['days'] );
		$last_day = end($open_hours['days']) !== $first_day ? end($open_hours['days']) : null;
		$count_days = count($open_hours['days']);

		$days_name = $first_day . ( $last_day ? ( $count_days === 2 ? ' and ' : ' to ' ) . $last_day : '' );
		$days_hours = $open_hours['times'];

		$weekly_opening_times[] = array( 'title' => $days_name, 'times' => $days_hours );

	}

	return $use_default_styling === true ? opening_times_add_styling( $weekly_opening_times ) : $weekly_opening_times;
}