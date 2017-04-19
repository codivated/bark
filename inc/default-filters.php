<?php
/**
 * Apply default actions to Bark filters.
 *
 * @package bark
 */

/**
 * Add barks caught before `wp_loaded` in 1 batch.
 *
 * All subsequent barks will be added individually.
 */
function bark_save_queue() {
	Bark_Queue_Manager::get_instance()->save();
}
add_action( 'wp_loaded', 'bark_save_queue', 500 );

/**
 * Handle adding an entry when `bark` action is called.
 */
function bark_add_entry( $message, $level = 'debug', $context = array() ) {
	$backtrace = debug_backtrace();

	$bark_context['file'] = '';
	$bark_context['line'] = '';

	// A few of the backtrace items are going to be WP core related to `add_action` since that is how we
	// trigger barks.
	foreach ( $backtrace as $backtrace_item ) {
		if ('bark' === $backtrace_item['args'][0]) {
			$bark_context['file'] = $backtrace_item['file'];
			$bark_context['line'] = $backtrace_item['line'];
		}
	}

	$bark_context['custom'] = $context;

	Bark_Queue_Manager::get_instance()->add( array(
		'message' => $message,
		'level' => $level,
		'context' => (array) $bark_context,
	) );
}
add_action( 'bark', 'bark_add_entry', 10, 3 );

function bark_prevent_log_if_limit_reached( $should_log ) {
	$limit = get_option( 'bark-limit-logs', 5000 );

	/**
	 * Filter the number of logs that are allowed. If there are more barks currently in the
	 * database than what this limit is set to, we prevent new barks from being added.
	 *
	 * @param int $limit Number of barks that are allowed before we prevent new barks.
	 * @since 0.1
	 */
	$limit = apply_filters( 'bark-log-limit', $limit );
	$barks = bark_get_total();

	if ( (int) $limit <= (int) $barks ) {
		$should_log = false;
	}

	return $should_log;
}
add_filter( 'bark_should_log', 'bark_prevent_log_if_limit_reached' );
