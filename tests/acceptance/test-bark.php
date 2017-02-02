<?php
/**
 * Acceptance tests for barking errors.
 *
 * @package bark
 */

class BarkTest extends WP_UnitTestCase {
	public function setUp() {
		parent::setUp();
		bark_add_default_levels();
	}

	/**
	 * @test
	 */
	public function barkActionAddsEntryToDatabaseAsBarkPostType() {
		$message = 'Something went wrong!';

		do_action( 'bark', array(
			'level' => 'error',
			'content' => $message,
			'context' => array(),
		) );

		$barks = new WP_Query( array(
			'post_type' => 'cdv8_bark',
		) );

		$this->assertNotEmpty( $barks->posts, 'Could not find any barks in database.' );
		$decoded_entry = json_decode( $barks->posts[0]->post_content );
		$this->assertEquals( $message, $decoded_entry->message, 'Found a bark in the database, but its title did not match what was expected.' );
	}

	/**
	 * @test
	 */
	public function barkRespectsLogLimit() {
		update_option( 'bark-limit-logs', 2 );
		do_action( 'bark', array(
			'level' => 'error',
			'content' => 'Message here',
		) );
		$barks = new WP_Query( array(
			'post_type' => 'cdv8_bark',
		) );
		$this->assertCount(1, $barks->posts);

		do_action( 'bark', array(
			'level' => 'error',
			'content' => 'Message here',
		) );
		$barks = new WP_Query( array(
			'post_type' => 'cdv8_bark',
		) );
		$this->assertCount(2, $barks->posts);

		// Should fail.
		do_action( 'bark', array(
			'level' => 'error',
			'content' => 'Message here',
		) );

		$barks = new WP_Query( array(
			'post_type' => 'cdv8_bark',
		) );
		$this->assertCount(2, $barks->posts);
	}
}
