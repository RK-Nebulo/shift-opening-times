<?php

	$bank_holidays_file_path = $plugin_dir_path . 'json/bank_holidays.json';

	function custom_date_add ( $date_string, $add ) {

		global $date_format;

		$date = date_create( $date_string );
		date_add( $date, date_interval_create_from_date_string( $add . ' days' ));
		return date_format( $date, $date_format);
	}

	function update_bank_holidays_json() {

		global $bank_holidays_file_path, $today_date, $date_format;

		$bank_holidays_list = array( 'next_update' => false, 'dates' => array() );

		for( $d = 0; $d <= 93; $d++ ) :
			$date = date_create( custom_date_add( $today_date, $d ) );

			$url = 'http://kayaposoft.com/enrico/json/v1.0/?action=isPublicHoliday&date=' . date_format($date, 'd-m-Y') . '&country=eng';

			$date_json = json_decode( file_get_contents( $url ), true );

			if ( $date_json['isPublicHoliday'] ) :
				$bank_holidays_list['dates'][] = date_format( $date, $date_format );
			endif;

		endfor;

		// TODO: update this variable to pull in the first day of next month

		$bank_holidays_list['next_update'] = date( 'Y-m-d', strtotime( 'first day next month' ) );

		$json_file = fopen( $bank_holidays_file_path, 'w' );
		fwrite( $json_file, json_encode( $bank_holidays_list ) );
		fclose( $json_file );

	}

	function is_bank_holiday( $date ) {

		global $bank_holidays_file_path, $date_format;

		$bank_holiday_dates = json_decode( file_get_contents( $bank_holidays_file_path ) )->dates;

		$date = date_create( $date );

		return in_array( date_format($date, $date_format) , $bank_holiday_dates ) ? true : null;
	}

	if ( file_exists( $bank_holidays_file_path ) ) :
		$bank_holidays_json = json_decode( file_get_contents( $bank_holidays_file_path ), true );
		if ( $bank_holidays_json['next_update'] <= $today_date ) :
			update_bank_holidays_json();
		endif;
	else :
		update_bank_holidays_json();
	endif;

	function bank_holiday_opening_times() {

		global $today_date, $date_format, $use_default_styling;

		$bank_holiday_names = array(
			'25-12' => 'Christmas Day',
			'26-12' => 'Boxing Day',
			'01-01' => 'New Year\'s Day',
			'03-04' => 'Good Friday',
			'06-04' => 'Easter Monday',
		);

		$bank_holidays_list = array();

		for($d = 1; $d <= 62; $d++) :

			$date = custom_date_add( $today_date, $d );
			if ( is_bank_holiday( $date ) ) :
				$date_obj = date_create( $date );
				$bank_holiday_id = date_format($date_obj, 'd-m');
				$bank_holiday_date = date_format($date_obj, 'F jS' . ( date_format($date_obj, 'Y') !== date('Y') ? ' Y' : '' ) );
				$bank_holiday_name = isset($bank_holiday_names[$bank_holiday_id]) ? $bank_holiday_names[$bank_holiday_id] : $bank_holiday_date;

				$bank_holidays_list[] = array( 'title' => $bank_holiday_name, 'times' => get_opening_hours( 'bank_holiday' ) );					
			endif;
		endfor;

		if( count( $bank_holidays_list ) === 0 ) :
			$bank_holidays_list[] = array( 'title' => 'Bank Holidays', 'times' => get_opening_hours( 'bank_holiday' ) );					
		endif;

		return $use_default_styling === true ? opening_times_add_styling( $bank_holidays_list ) : $bank_holidays_list;
	}
