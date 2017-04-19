<?php
/**
 * Bark Queue Manager class file.
 *
 * @package bark
 */

/**
 * Manage the queues bark relies on to add entries to the database.
 */
class Bark_Queue_Manager {
	private static $instance;
	protected $queue;

	public static function get_instance()
	{
		if (null === self::$instance) {
			self::$instance = new self();
		}

		return self::$instance;
	}

	public function __construct() {
		$this->queue = new Bark_Queue();
	}

	protected function add_single( $bark ) {
		$this->queue->push_to_queue( $bark );
		$this->save();
	}

	protected function add_to_collection( $bark ) {
		$this->queue->push_to_queue( $bark );
	}

	public function save() {
		$this->queue->save();
	}

	/**
	 * Add single error or add the error to the collection of errors.
	 *
	 * @param $bark
	 */
	public function add( $bark ) {
		if ( ! $this->background_processing_enabled() ) {
			$logger = new Bark_Logger();
			$logger->log( $bark['message'], $bark['level'], $bark['context'] );
		}

		if ( did_action( 'wp_loaded' ) ) {
			$this->add_single( $bark );
		} else {
			$this->add_to_collection( $bark );
		}
	}

	public function background_processing_enabled() {
		$enabled = get_option( 'bark_enable_background_processing', false );

		/**
		 * Enable background processing.
		 *
		 * @since 0.1
		 * @param bool $enabled
		 */
		return apply_filters( 'bark_enable_background_processing', $enabled );
	}

	public function run() {
		$this->queue->dispatch();
	}
}

$bark_queue_manager = Bark_Queue_Manager::get_instance();
