<?php

namespace TotalContest\Shortcode;

/**
 * Image shortcode class
 * @package TotalContest\Shortcode
 * @since   1.0.0
 */
class Image extends Base {

	/**
	 * Handle shortcode.
	 *
	 * @return mixed
	 * @since 1.0.0
	 */
	public function handle() {
		$image = $this->getAttribute( 'src' );

		if ( empty( $image ) && $this->getAttribute( 'id' ) ):
			$image = wp_get_attachment_url( get_post_thumbnail_id( $this->getAttribute( 'id' ) ) );
		endif;

		return sprintf( '<img src="%s">', esc_attr( $image ) );
	}

}