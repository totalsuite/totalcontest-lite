<?php

namespace TotalContest\Admin\Options;

use TotalContest\Contracts\Migrations\Contest\Migrator;
use TotalContestVendors\TotalCore\Admin\Pages\Page as AdminPageContract;
use TotalContestVendors\TotalCore\Contracts\Foundation\Environment as EnvironmentContract;
use TotalContestVendors\TotalCore\Contracts\Http\Request as RequestContract;
use TotalContestVendors\TotalCore\Helpers\Misc;

/**
 * Class Page
 * @package TotalContest\Admin\Options
 */
class Page extends AdminPageContract {
	/**
	 * Options.
	 *
	 * @var array $options
	 */
	protected $options;
	/**
	 * @var Migrator[] $migrators
	 */
	protected $migrators;

	/**
	 * Page constructor.
	 *
	 * @param RequestContract     $request
	 * @param EnvironmentContract $env
	 */
	public function __construct( RequestContract $request, EnvironmentContract $env, $migrators ) {
		parent::__construct( $request, $env );
		$this->migrators = $migrators;
		$this->options   = TotalContest( 'options' )->getOptions();

		if ( empty( $this->options ) ):
			$this->options = null;
		endif;
	}

	/**
	 * Enqueue assets.
	 *
	 * @return mixed
	 */
	public function assets() {
		// TotalContest
		wp_enqueue_script( 'totalcontest-admin-options' );
		wp_enqueue_style( 'totalcontest-admin-options' );

		/**
		 * Filters the list of expressions that are available through the interface to override.
		 *
		 * @param array $expressions Array of expressions.
		 *
		 * @return array
		 * @since 2.0.0
		 */
		$expressions = apply_filters(
			'totalcontest/filters/admin/options/expressions',
			[
				'contest'     => [
					'label'       => __( 'Contest', 'totalcontest' ),
					'expressions' => [
						'Home'        => [
							'translations' => [
								__( 'Home', 'totalcontest' ),
							],
						],
						'Participate' => [
							'translations' => [
								__( 'Participate', 'totalcontest' ),
							],
						],
						'Submissions' => [
							'translations' => [
								__( 'Submissions', 'totalcontest' ),
							],
						],
						'Submitting'  => [
							'translations' => [
								__( 'Submitting', 'totalcontest' ),
							],
						],
					],
				],
				'submission'  => [
					'label'       => __( 'Submission', 'totalcontest' ),
					'expressions' => [
						'Vote'                                                                                   => [
							'translations' => [
								__( 'Vote', 'totalcontest' ),
							],
						],
						'This submission is awaiting moderator approval, it will published as soon as possible.' => [
							'translations' => [
								__( 'This submission is awaiting moderator approval, it will published as soon as possible.', 'totalcontest' ),
							],
						],
					],
				],
				'submissions' => [
					'label'       => __( 'Submissions', 'totalcontest' ),
					'expressions' => [
						'Search'     => [
							'translations' => [
								__( 'Search', 'totalcontest' ),
							],
						],
						'Date'       => [
							'translations' => [
								__( 'Date', 'totalcontest' ),
							],
						],
						'Views'      => [
							'translations' => [
								__( 'Views', 'totalcontest' ),
							],
						],
						'Votes'      => [
							'translations' => [
								__( 'Votes', 'totalcontest' ),
							],
						],
						'Ascending'  => [
							'translations' => [
								__( 'Ascending', 'totalcontest' ),
							],
						],
						'Descending' => [
							'translations' => [
								__( 'Descending', 'totalcontest' ),
							],
						],
					],
				],
				'errors'      => [
					'label'       => __( 'Errors', 'totalcontest' ),
					'expressions' => [
						'You cannot participate again.' => [
							'translations' => [
								__( 'You cannot participate again.', 'totalcontest' ),
							],
						],
					],
				],
				'validations' => [
					'label'       => __( 'Validations', 'totalcontest' ),
					'expressions' => [
						'{{label}} must be filled.'                                    => [
							'translations' => [
								__( '{{label}} must be filled.', 'totalcontest' ),
							],
						],
						'{{label}} must be a valid email address.'                     => [
							'translations' => [
								__( '{{label}} must be a valid email address.', 'totalcontest' ),
							],
						],
						'{{label}} must be a valid URL.'                               => [
							'translations' => [
								__( '{{label}} must be a valid URL.', 'totalcontest' ),
							],
						],
						'{{label}} is not within the supported range.'                 => [
							'translations' => [
								__( '{{label}} is not within the supported range.', 'totalcontest' ),
							],
						],
						'{{label}} does not allow this value.'                         => [
							'translations' => [
								__( '{{label}} does not allow this value.', 'totalcontest' ),
							],
						],
						'{{label}} must be a number.'                                  => [
							'translations' => [
								__( '{{label}} must be a number.', 'totalcontest' ),
							],
						],
						'{{label}} must be unique. The entered value was used before.' => [
							'translations' => [
								__( '{{label}} must be unique. The entered value was used before.', 'totalcontest' ),
							],
						],
						'{{label}} is not in an array format.'                         => [
							'translations' => [
								__( '{{label}} is not in an array format.', 'totalcontest' ),
							],
						],
						'{{label}} is not a string.'                                   => [
							'translations' => [
								__( '{{label}} is not a string.', 'totalcontest' ),
							],
						],
						'{{label}} file size must be at least %s.'                     => [
							'translations' => [
								__( '{{label}} file size must be at least %s.', 'totalcontest' ),
							],
						],
						'{{label}} file size must be less than %s.'                    => [
							'translations' => [
								__( '{{label}} file size must be less than %s.', 'totalcontest' ),
							],
						],
						'{{label}} must be at least %d characters.'                    => [
							'translations' => [
								__( '{{label}} must be at least %d characters.', 'totalcontest' ),
							],
						],
						'{{label}} must be less than %d characters.'                   => [
							'translations' => [
								__( '{{label}} must be less than %d characters.', 'totalcontest' ),
							],
						],
						'%d Characters left'                                           => [
							'translations' => [
								__( '%d Characters left', 'totalcontest' ),
							],
						],
						'Only files with these extensions are allowed: %s.'            => [
							'translations' => [
								__( 'Only files with these extensions are allowed: %s.', 'totalcontest' ),
							],
						],
						'Only %s files are accepted.'                                  => [
							'translations' => [
								__( 'Only %s files are accepted.', 'totalcontest' ),
							],
						],
						'You must upload a file.'                                      => [
							'translations' => [
								__( 'You must upload a file.', 'totalcontest' ),
							],
						],
						'Minimum length for files is: %s seconds.'                     => [
							'translations' => [
								__( 'Minimum length for files is: %s seconds.', 'totalcontest' ),
							],
						],
						'Maximum length for files is: %s seconds.'                     => [
							'translations' => [
								__( 'Maximum length for files is: %s seconds.', 'totalcontest' ),
							],
						],
						'Minimum width for images is: %s.'                             => [
							'translations' => [
								__( 'Minimum width for images is: %s.', 'totalcontest' ),
							],
						],
						'Minimum height for images is: %s.'                            => [
							'translations' => [
								__( 'Minimum height for images is: %s.', 'totalcontest' ),
							],
						],
						'Maximum width for images is: %s.'                             => [
							'translations' => [
								__( 'Maximum width for images is: %s.', 'totalcontest' ),
							],
						],
						'Maximum height for images is: %s.'                            => [
							'translations' => [
								__( 'Maximum height for images is: %s.', 'totalcontest' ),
							],
						],
						'Only links from these services are accepted: %s.'             => [
							'translations' => [
								__( 'Only links from these services are accepted: %s.', 'totalcontest' ),
							],
						],
					],
				],
			]
		);

		wp_localize_script( 'totalcontest-admin-options', 'TotalContestExpressions', $expressions );
		wp_localize_script( 'totalcontest-admin-options', 'TotalContestSavedExpressions', Misc::getJsonOption( 'totalcontest_expressions' ) );
		wp_localize_script( 'totalcontest-admin-options', 'TotalContestOptions', $this->options );
		wp_localize_script( 'totalcontest-admin-options', 'TotalContestDebugInformation', Misc::getDebugInfo() );
		wp_localize_script( 'totalcontest-admin-options', 'TotalContestMigrationPlugins', $this->migrators );
	}

	public function render() {
		/**
		 * Filters the list of tabs in options page.
		 *
		 * @param array $tabs Array of tabs [id => [label, icon, file]].
		 *
		 * @return array
		 * @since 2.0.0
		 */
		$tabs = apply_filters(
			'totalcontest/filters/admin/options/tabs',
			[
				'general'       => [ 'label' => __( 'General', 'totalcontest' ), 'icon' => 'admin-settings' ],
				'performance'   => [ 'label' => __( 'Performance', 'totalcontest' ), 'icon' => 'performance' ],
				'services'      => [ 'label' => __( 'Services', 'totalcontest' ), 'icon' => 'cloud' ],
				'sharing'       => [ 'label' => __( 'Sharing', 'totalcontest' ), 'icon' => 'share' ],
				'advanced'      => [ 'label' => __( 'Advanced', 'totalcontest' ), 'icon' => 'admin-generic' ],
				'notifications' => [ 'label' => __( 'Notifications', 'totalcontest' ), 'icon' => 'email' ],
				'expressions'   => [ 'label' => __( 'Expressions', 'totalcontest' ), 'icon' => 'admin-site' ],
				
				'import-export' => [ 'label' => __( 'Import & Export', 'totalcontest' ), 'icon' => 'update' ],
				'debug'         => [ 'label' => __( 'Debug', 'totalcontest' ), 'icon' => 'info' ],
			]
		);

		include_once __DIR__ . '/views/index.php';
	}
}
