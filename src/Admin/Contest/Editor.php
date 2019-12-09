<?php

namespace TotalContest\Admin\Contest;

use TotalContest\Contracts\Contest\Model;
use TotalContest\Contracts\Contest\Repository as ContestRepository;
use TotalContestVendors\TotalCore\Contracts\Foundation\Environment;
use TotalContestVendors\TotalCore\Contracts\Modules\Repository as ModulesRepository;
use TotalContestVendors\TotalCore\Helpers\Arrays;
use TotalContestVendors\TotalCore\Helpers\Misc;

/**
 * Class Editor
 * @package TotalContest\Admin\Contest
 */
class Editor {
	/**
	 * @var Environment $env
	 */
	protected $env;
	/**
	 * @var \WP_Filesystem_Base $filesystem
	 */
	protected $filesystem;
	/**
	 * @var ModulesRepository $modulesRepository
	 */
	protected $modulesRepository;
	/**
	 * @var ContestRepository $contestRepository
	 */
	protected $contestRepository;
	/**
	 * @var Model $contest
	 */
	protected $contest;
	/**
	 * @var array $templates
	 */
	protected $templates = [];
	/**
	 * @var array $settings
	 */
	protected $settings = [];

	/**
	 * Bootstrap constructor.
	 *
	 * @param                     $env
	 * @param \WP_Filesystem_Base $filesystem
	 * @param ModulesRepository   $modulesRepository
	 * @param ContestRepository   $contestRepository
	 */
	public function __construct( $env, $filesystem, ModulesRepository $modulesRepository, ContestRepository $contestRepository ) {
		$this->env               = $env;
		$this->filesystem        = $filesystem;
		$this->contestRepository = $contestRepository;
		$this->modulesRepository = $modulesRepository;

		// Templates
		$this->templates = $this->modulesRepository->getActiveWhere( [ 'type' => 'template' ] );
		foreach ( $this->templates as $templateId => $template ):
			foreach ( [ 'defaults', 'settings', 'preview' ] as $item ):
				$this->templates[ $templateId ][ $item ] = add_query_arg(
					[ 'action' => "totalcontest_templates_get_{$item}", 'template' => $templateId ],
					admin_url( 'admin-ajax.php' )
				);
			endforeach;
		endforeach;

		// Enqueue assets
		add_action( 'admin_enqueue_scripts', [ $this, 'assets' ] );
		// Editor
		add_action( 'edit_form_after_title', [ $this, 'content' ] );
		// Actions
		add_action( 'submitpost_box', [ $this, 'actions' ] );
		// Save contest
		add_filter( 'wp_insert_post_data', [ $this, 'save' ], 10, 2 );
		// Remove WP filters
		remove_filter( 'content_save_pre', 'wp_targeted_link_rel' );
	}

	/**
	 * Front-end assets.
	 */
	public function assets() {
		if ( ! empty( $GLOBALS['post'] ) ):
			$this->contest  = $this->contestRepository->getById( $GLOBALS['post']->ID );
			$this->settings = json_decode( $GLOBALS['post']->post_content, true );
		endif;

		// Disable auto save
		wp_dequeue_script( 'autosave' );

		// WP Media
		wp_enqueue_media();

		// TinyMCE
		if ( ! class_exists( '_WP_Editors', false ) ):
			require ABSPATH . WPINC . '/class-wp-editor.php';
			\_WP_Editors::enqueue_scripts();
		endif;

		// TotalContest
		wp_enqueue_script( 'totalcontest-admin-contest-editor' );
		wp_enqueue_style( 'totalcontest-admin-contest-editor' );

		/**
		 * Filters the settings of contest passed to frontend controller.
		 *
		 * @param array $settings Array of settings.
		 *
		 * @return array
		 * @since 2.0.0
		 */
		$settings = apply_filters( 'totalcontest/filters/admin/contest/editor/settings', $this->settings );

		/**
		 * Filters the information passed to frontend controller.
		 *
		 * @param array $information Array of values [key => value].
		 *
		 * @return array
		 * @since 2.0.0
		 */
		$information = apply_filters(
			'totalcontest/filters/admin/contest/editor/information',
			[
				'imageSizes' => get_intermediate_image_sizes(),
				'sidebars'   => $GLOBALS['wp_registered_sidebars'],
			]
		);

		/**
		 * Filters the defaults settings of contest editor.
		 *
		 * @param array $defaults Array of settings.
		 *
		 * @return array
		 * @since 2.0.0
		 */
		$defaults = apply_filters(
			'totalcontest/filters/admin/contest/editor/defaults',
			TotalContest( 'contests.defaults' )
		);

		// Send JSON to TotalContest frontend controller
		wp_localize_script( 'totalcontest-admin-contest-editor', 'TotalContestSettings', $settings );
		wp_localize_script( 'totalcontest-admin-contest-editor', 'TotalContestDefaults', $defaults );
		wp_localize_script( 'totalcontest-admin-contest-editor', 'TotalContestInformation', $information );
		wp_localize_script( 'totalcontest-admin-contest-editor', 'TotalContestTemplates', $this->templates );
		wp_localize_script( 'totalcontest-admin-contest-editor', 'TotalContestLanguages', Misc::getSiteLanguages() );
	}

	/**
	 * Editor content
	 */
	public function content() {
		/**
		 * Filters tabs list in contest editor.
		 *
		 * @param array $tabs Array of tabs [id => [label, icon]].
		 *
		 * @return array
		 * @since 2.0.0
		 */
		$tabs = apply_filters(
			'totalcontest/filters/admin/contest/editor/tabs',
			[
				'form'         => [ 'label' => __( 'Fields', 'totalcontest' ), 'icon' => 'feedback' ],
				'settings'     => [ 'label' => __( 'Settings', 'totalcontest' ), 'icon' => 'admin-settings' ],
				'design'       => [ 'label' => __( 'Design', 'totalcontest' ), 'icon' => 'admin-appearance' ],
				'integration'  => [ 'label' => __( 'Integration', 'totalcontest' ), 'icon' => 'admin-generic' ],
				'translations' => [ 'label' => __( 'Translations', 'totalcontest' ), 'icon' => 'translation' ],
			]
		);
		/**
		 * Filters the list of settings tabs in contest editor.
		 *
		 * @param array $settingsTabs Array of tabs [id => [label, icon, tabs => []]].
		 *
		 * @return array
		 * @since 2.0.0
		 */
		$settingsTabs = apply_filters(
			'totalcontest/filters/admin/contest/editor/settings/tabs',
			[
				'contest'       => [
					'label' => __( 'Contest', 'totalcontest' ),
					'icon'  => 'megaphone',
					'tabs'  => [
						'submissions' => [ 'label' => __( 'Submissions', 'totalcontest' ), 'icon' => 'admin-settings' ],
						'limitations' => [ 'label' => __( 'Limitations', 'totalcontest' ), 'icon' => 'lock' ],
						'frequency'   => [ 'label' => __( 'Frequency', 'totalcontest' ), 'icon' => 'backup' ],
					],
				],
				'vote'          => [
					'label' => __( 'Vote', 'totalcontest' ),
					'icon'  => 'marker',
					'tabs'  => [
						'type'        => [ 'label' => __( 'Type', 'totalcontest' ), 'icon' => 'admin-settings' ],
						'limitations' => [ 'label' => __( 'Limitations', 'totalcontest' ), 'icon' => 'lock' ],
						'frequency'   => [ 'label' => __( 'Frequency', 'totalcontest' ), 'icon' => 'backup' ],
					],
				],
				'content'       => [
					'label' => __( 'Pages', 'totalcontest' ),
					'icon'  => 'admin-page'
				],
				'seo'           => [
					'label' => __( 'SEO', 'totalcontest' ),
					'icon'  => 'search',
					'tabs'  => [
						'contest'    => [ 'label' => __( 'Contest', 'totalcontest' ), 'icon' => 'laptop' ],
						'submission' => [ 'label' => __( 'Submission', 'totalcontest' ), 'icon' => 'feedback' ],
					],
				],
				'notifications' => [
					'label' => __( 'Notifications', 'totalcontest' ),
					'icon'  => 'email',
					'tabs'  => [
						'email'   => [ 'label' => __( 'Email', 'totalcontest' ), 'icon' => 'email' ],
						'push'    => [ 'label' => __( 'Push', 'totalcontest' ), 'icon' => 'format-status' ],
						'webhook' => [ 'label' => __( 'WebHook', 'totalcontest' ), 'icon' => 'admin-site' ],
					],
				],
				'customization' => [ 'label' => __( 'Customization', 'totalcontest' ), 'icon' => 'admin-plugins' ],
			]
		);

		/**
		 * Filters the list of design tabs in contest editor.
		 *
		 * @param array $designTabs Array of tabs [id => [label]].
		 *
		 * @return array
		 * @since 2.0.0
		 */
		$designTabs = apply_filters(
			'totalcontest/filters/admin/editor/design/tabs',
			[
				'templates' => [ 'label' => __( 'Templates', 'totalcontest' ) ],
				'layout'    => [ 'label' => __( 'Layout', 'totalcontest' ) ],
				'colors'    => [ 'label' => __( 'Colors', 'totalcontest' ) ],
				'text'      => [ 'label' => __( 'Text', 'totalcontest' ) ],
				'advanced'  => [
					'label' => __( 'Advanced', 'totalcontest' ),
					'tabs'  => [
						'template-settings' => [ 'label' => __( 'Template Settings', 'totalcontest' ) ],
						'behaviours'        => [ 'label' => __( 'Behaviours', 'totalcontest' ) ],
						'effects'           => [ 'label' => __( 'Effects', 'totalcontest' ) ],
						'custom-css'        => [ 'label' => __( 'Custom CSS', 'totalcontest' ) ],
					],
				],
			]
		);


		/**
		 * Filters the list of integration tabs in contest editor.
		 *
		 * @param array $tabs Array of tabs [id => [label, description, icon]].
		 *
		 * @return array
		 * @since 2.0.0
		 */
		$integrationTabs = apply_filters(
			'totalcontest/filters/admin/contest/editor/integration/tabs',
			[
				'shortcode' => [ 'label' => __( 'Shortcode', 'totalcontest' ), 'description' => __( 'WordPress feature', 'totalcontest' ), 'icon' => 'editor-code' ],
				'widget'    => [ 'label' => __( 'Widget', 'totalcontest' ), 'description' => __( 'WordPress feature', 'totalcontest' ), 'icon' => 'megaphone' ],
				'link'      => [ 'label' => __( 'Direct link', 'totalcontest' ), 'description' => __( 'Standard link', 'totalcontest' ), 'icon' => 'admin-links' ],
				'embed'     => [ 'label' => __( 'Embed', 'totalcontest' ), 'description' => __( 'External inclusion', 'totalcontest' ), 'icon' => 'admin-site' ],
				'email'     => [ 'label' => __( 'Email', 'totalcontest' ), 'description' => __( 'Vote links', 'totalcontest' ), 'icon' => 'email' ],
			]
		);

		if ( ! current_user_can( 'edit_theme_options' ) ):
			unset( $integrationTabs['widget'] );
		endif;

		include_once __DIR__ . '/views/editor.php';
	}

	/**
	 * Editors sidebar actions.
	 */
	public function actions() {
		$actions = [];

		if ( current_user_can( 'edit_contests' ) ):
			$actions['submissions'] = [ 'label' => __( 'Submissions', 'totalcontest' ), 'icon' => 'images-alt2', 'url' => add_query_arg( [ 'post_type' => TC_SUBMISSION_CPT_NAME, 'contest' => $GLOBALS['post']->ID ], admin_url( 'edit.php' ) ) ];
		endif;

		if ( current_user_can( 'manage_options' ) ):
			$actions['log'] = [ 'label' => __( 'Log', 'totalcontest' ), 'icon' => 'editor-table', 'url' => add_query_arg( [ 'post_type' => TC_CONTEST_CPT_NAME, 'page' => 'log', 'contest' => $GLOBALS['post']->ID ], admin_url( 'edit.php' ) ) ];
		endif;

		/**
		 * Filters the list of available action (side) in contest editor.
		 *
		 * @param array $actions Array of actions [id => [label, icon, url]].
		 *
		 * @return array
		 * @since 2.0.0
		 */
		$actions = apply_filters( 'totalcontest/filters/admin/contest/editor/actions', $actions );

		include_once __DIR__ . '/views/actions.php';
	}

	/**
	 * Save contest.
	 *
	 * @param $contestArgs
	 * @param $post
	 *
	 * @return mixed
	 */
	public function save( $contestArgs, $post ) {
		$contestId = absint( $post['ID'] );

		if ( ! empty( $contestArgs['post_content'] ) ):
			$settings = json_decode( wp_unslash( $contestArgs['post_content'] ), true );

			/**
			 * Filters the settings before saving the contest.
			 *
			 * @param array $settings    Array of settings.
			 * @param array $contestArgs Array of post args.
			 * @param int   $contestId   Contest post ID.
			 *
			 * @return array
			 * @since 2.0.0
			 */
			$settings = apply_filters( 'totalcontest/filters/before/admin/contest/editor/save/settings', $settings, $contestArgs, $contestId, $this );

			// Purge CSS cache
			if ( ! empty( $settings['presetUid'] ) ):
				$cachedFile = wp_normalize_path( $this->env['cache']['path'] . "css/{$settings['presetUid']}.css" );
				$this->filesystem->delete( $cachedFile );
			endif;

			// Validations
			$numericFields = [
				'contest.submissions.perPage',
				'contest.limitations.quota.value',
				'contest.frequency.count',
				'contest.frequency.timeout',

				'vote.scale',
				'vote.limitations.quota.value',
				'vote.frequency.count',
				'vote.frequency.perItem',
				'vote.frequency.timeout',

				'design.layout.columns',
			];

			foreach ( $numericFields as $field ):
				$value    = Arrays::getDotNotation( $settings, $field );
				$settings = Arrays::setDotNotation( $settings, $field, absint( $value ) );
			endforeach;

			// Validate limitations (date based)
			foreach ( [ 'contest', 'vote' ] as $section ):
				$timePeriodStart = Arrays::getDotNotation( $settings, "{$section}.limitations.period.start", '' );
				if ( ! (bool) strtotime( $timePeriodStart ) ):
					$settings = Arrays::setDotNotation(
						$settings,
						"{$section}.limitations.period.start",
						''
					);
				endif;
				$timePeriodEnd = Arrays::getDotNotation( $settings, "{$section}.limitations.period.end", '' );
				if ( ! (bool) strtotime( $timePeriodEnd ) ):
					$settings = Arrays::setDotNotation(
						$settings,
						"{$section}.limitations.period.end",
						''
					);
				endif;
			endforeach;

			// Fields
			$fields = (array) Arrays::getDotNotation( $settings, 'contest.form.fields', [] );
			foreach ( $fields as $fieldIndex => $field ):
				// Validate field name
				$settings = Arrays::setDotNotation(
					$settings,
					"contest.form.fields.{$fieldIndex}.name",
					sanitize_title_with_dashes( Arrays::getDotNotation( $field, 'name', uniqid( 'untitled_', false ) ), '', 'save' )
				);

				// Disable file-related validations when file upload is unchecked
				if ( empty( $field['validations']['file']['enabled'] ) && in_array( $field['type'], [ 'video', 'audio' ] ) ):
					$settings = Arrays::setDotNotation( $settings, "contest.form.fields.{$fieldIndex}.validations.size.enabled", false );
					$settings = Arrays::setDotNotation( $settings, "contest.form.fields.{$fieldIndex}.validations.length.enabled", false );
					$settings = Arrays::setDotNotation( $settings, "contest.form.fields.{$fieldIndex}.validations.formats.enabled", false );
				endif;

				// Sort validations
				$validations = Arrays::getDotNotation( $settings, "contest.form.fields.{$fieldIndex}.validations" );
				/** @noinspection SlowArrayOperationsInLoopInspection */
//				$validations = array_replace( array_flip( [ 'filled', 'file', 'formats', 'size', 'length', 'dimensions' ] ), $validations );
				$settings = Arrays::setDotNotation( $settings, "contest.form.fields.{$fieldIndex}.validations", $validations );
			endforeach;

			// Generate a UID based on design settings
			$settings['presetUid'] = md5( json_encode( $settings['design'] ) );

			/**
			 * Filters the settings after validation to be saved.
			 *
			 * @param array $settings    Array of settings.
			 * @param array $contestArgs Array of post args.
			 * @param int   $contestId   Contest post ID.
			 *
			 * @return array
			 * @since 2.0.0
			 */
			$settings = apply_filters( 'totalcontest/filters/admin/contest/editor/save/settings', $settings, $contestArgs, $contestId, $this );

			$contestArgs['post_content'] = json_encode( $settings );
			// Sanitize
			if ( ! current_user_can( 'unfiltered_html' ) ):
				$contestArgs['post_content'] = wp_kses_post( $contestArgs['post_content'] );
			endif;
			// Add slashes
			$contestArgs['post_content'] = wp_slash( $contestArgs['post_content'] );

			/**
			 * Filters the arguments that are passed back to wp_update_post to save the changes.
			 *
			 * @param array $contestArgs Array of post args.
			 * @param array $settings    Array of settings.
			 * @param int   $contestId   Contest post ID.
			 *
			 * @return array
			 * @since 2.0.0
			 * @see   Check wp_update_post documentaion for more details.
			 *
			 */
			$contestArgs = apply_filters( 'totalcontest/filters/admin/contest/editor/save/post', $contestArgs, $settings, $contestId, $this );
		endif;

		// Purge global cache
		Misc::purgePluginsCache();

		// Adjust redirect url
		add_filter( 'redirect_post_location', function ( $location ) {
			$params = [
				'tab' => empty( $_POST['totalcontest_current_tab'] ) ? null : urlencode( (string) $_POST['totalcontest_current_tab'] ),
			];

			return add_query_arg( $params, $location );
		} );

		return $contestArgs;
	}
}
