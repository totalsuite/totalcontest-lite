<?php

namespace TotalContest\Limitations;

use TotalContestVendors\TotalCore\Limitations\Limitation;

/**
 * Class Period
 * @package TotalContest\Limitations
 */
class Period extends Limitation {
	/**
	 * @return bool|\WP_Error
	 */
	public function check() {
		$startDate = empty( $this->args['start'] ) ? false : TotalContest( 'datetime', [ $this->args['start'] ] );
		$endDate   = empty( $this->args['end'] ) ? false : TotalContest( 'datetime', [ $this->args['end'] ] );
		$now       = TotalContest( 'datetime', [ 'now' ] );

		if ( $startDate && $startDate->getTimestamp() > current_time( 'timestamp' ) ):
			$interval = $startDate->diff( $now, true );

			return new \WP_Error(
				'start_date',
				sprintf(
					__( 'Not started yet, %s left.', 'totalcontest' ),
					$interval->format( __( '%a days, %h hours and %i minutes', 'totalcontest' ) )
				)
			);
		endif;

		if ( $endDate && $endDate->getTimestamp() < current_time( 'timestamp' ) ):
			$interval = $endDate->diff( $now, true );

			return new \WP_Error(
				'finish_date',
				sprintf(
					__( 'Finished since %s.', 'totalcontest' ),
					$interval->format( __( '%a days, %h hours and %i minutes', 'totalcontest' ) )
				)
			);
		endif;

		return true;
	}
}