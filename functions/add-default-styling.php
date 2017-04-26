<?php

	if ( $use_default_styling === true ) {

		function enqueue_opening_times_stylesheet() {

			wp_enqueue_style( 'opening_times_stylesheet', plugins_url() . '/opening-times/style.css' );
		}

		add_action( 'wp_enqueue_scripts', 'enqueue_opening_times_stylesheet' );

	}

	function opening_times_add_styling( $opening_times ) {

		$output = '<dl class="opening-times__list">';

		foreach( $opening_times as $opening_time ) {

			$output .= '<dt class="opening-times__title">' . $opening_time['title'] . '</dt>';
			$output .= '<dd class="opening-times__times">' . $opening_time['times'] . '</dd>';

		}

		$output .= '</dl>';
		
		return $output;

	}
