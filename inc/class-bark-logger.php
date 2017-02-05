<?php
/**
 * Primary Bark_Logger class.
 *
 * @package bark
 */

/**
 * Handles functionality related to handling barked errors.
 *
 * @package bark
 */
class Bark_Logger {
	/**
	 * Log an entry.
	 *
	 * @param string $level   Level of the bark.
	 * @param string $message Message for the entry.
	 * @param string $context Additional information about the entry.
	 */
	public function log( $message, $level = 'debug', array $context = array() ) {
		if ( false === $this->should_log() ) {
			return;
		}

		$backtrace = debug_backtrace();
		$caller = array_shift( $backtrace );

		$context = wp_parse_args( $context, array(
			'file' => $caller['file'],
			'line' => $caller['line'],
		) );

		$bark = wp_insert_post( array(
			'post_type' => 'cdv8_bark',
			'post_title' => substr( $message, 0, 50 ) . '...',
			'post_content' => json_encode( array(
				'message' => $message,
				'context' => $context,
			) ),
			'post_status' => 'publish',
		) );
		$this->assign_level_to_bark( $level, $bark );

		return $bark;
	}

	public function should_log() {
		return apply_filters( 'bark_should_log', true );
	}

	/**
	 * Assign specified level to a bark entry.
	 *
	 * @param string $level_slug The slug for the level you want to assign.
	 * @param int    $bark_id    The ID of the bark you want the level assigned to.
	 */
	public function assign_level_to_bark( $level_slug, $bark_id ) {
		$level = get_term_by( 'slug', $level_slug, 'bark-level' );
		if ( false === $level ) {
			return false;
		}

		return wp_set_post_terms( $bark_id, $level->term_id, 'bark-level' );
	}
}
