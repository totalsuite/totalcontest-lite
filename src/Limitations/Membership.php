<?php

namespace TotalContest\Limitations;


use TotalContestVendors\TotalCore\Limitations\Limitation;

/**
 * Class Membership
 * @package TotalContest\Limitations
 */
class Membership extends Limitation {
	/**
	 * @return bool|\WP_Error
	 */
	public function check() {
		$roles = empty( $this->args['roles'] ) ? [] : (array) $this->args['roles'];

		if ( ! empty( $roles ) ):

			if ( is_user_logged_in() ):
				if ( ! in_array( $GLOBALS['current_user']->roles[0], $roles ) ):
					return new \WP_Error(
						'membership_type',
						sprintf(
							__( 'To continue, you must be a part of these roles: %s.', 'totalcontest' ),
							implode( ', ', $roles )
						)
					);
				endif;
			else:
				return new \WP_Error(
					'logged_in',
					sprintf(
						__( 'To continue, please <a href="%s">sign in</a> or <a href="%s">register</a>.', 'totalcontest' ),
						wp_login_url( home_url( add_query_arg( null, null ) ) ),
						wp_registration_url()
					)
				);
			endif;

		endif;

		return true;
	}
}