<?php

namespace TotalContest\Shortcode;

/**
 * Video shortcode class
 * @package TotalContest\Shortcode
 * @since   1.0.0
 */
class Video extends Base {

	/**
	 * Handle shortcode.
	 *
	 * @return mixed
	 * @since 1.0.0
	 */
	public function handle() {
		$source = $this->getAttribute( 'src' );

		return do_shortcode( sprintf( '[video src="%s"]', esc_attr( $source ) ) );
	}

}