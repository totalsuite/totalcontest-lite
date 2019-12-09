<?php

namespace TotalContest\Contest;

use TotalContest\Contracts\Contest\Model as ModelContract;
use TotalContest\Form\ParticipateForm;
use TotalContestVendors\TotalCore\Contracts\Form\Field;
use TotalContestVendors\TotalCore\Contracts\Form\Form;
use TotalContestVendors\TotalCore\Helpers\Arrays;
use TotalContestVendors\TotalCore\Helpers\Misc;
use TotalContestVendors\TotalCore\Helpers\Strings;
use TotalContestVendors\TotalCore\Traits\Metadata;

/**
 * Contest Model
 * @package TotalContest\Contest
 * @since   1.0.0
 */
class Model implements ModelContract {
	use Metadata;

	/**
	 * Contest ID.
	 *
	 * @var int|null
	 * @since 1.0.0
	 */
	protected $id = null;

	/**
	 * Contest attributes.
	 *
	 * @var array
	 * @since 1.0.0
	 */
	protected $attributes = [];

	/**
	 * Contest action.
	 *
	 * @var string
	 * @since 1.0.0
	 */
	protected $action = 'landing';

	/**
	 * Contest settings.
	 *
	 * @var array|null
	 * @since 1.0.0
	 */
	protected $settings = null;

	/**
	 * Contest seo attributes.
	 *
	 * @var array|null
	 * @since 1.0.0
	 */
	protected $seo = null;

	/**
	 * Received votes.
	 *
	 * @var int
	 * @since 1.0.0
	 */
	protected $receivedVotes = 0;

	/**
	 * Contest WordPress post.
	 *
	 * @var array|null|\WP_Post
	 * @since 1.0.0
	 */
	protected $contestPost = null;

	/**
	 * Contest submissions.
	 * @var string
	 * @since 1.0.0
	 */
	protected $submissions = [];

	/**
	 * Contest submissions count.
	 * @var string
	 * @since 1.0.0
	 */
	protected $submissionsCount = null;

	/**
	 * Submission per page.
	 * @var string
	 * @since 1.0.0
	 */
	protected $submissionsPerPage = 10;

	/**
	 * Contest total pages.
	 *
	 * @var int|null
	 * @since 1.0.0
	 */
	protected $pagesCount = 0;

	/**
	 * Contest current page.
	 *
	 * @var int|null
	 * @since 1.0.0
	 */
	protected $currentPage = 1;

	/**
	 * Contest upload form.
	 *
	 * @var \TotalContestVendors\TotalCore\Contracts\Form\Form $form
	 * @since 1.0.0
	 */
	protected $form = null;

	/**
	 * Contest current screen
	 * @var string
	 * @since 1.0.0
	 */
	protected $screen = 'contest.landing';

	/**
	 * Contest sub screen
	 * @var string
	 * @since 1.0.0
	 */
	protected $customPage = '';

	/**
	 * Sort by field.
	 * @var string
	 * @since 1.0.0
	 */
	protected $sortBy = 'date';

	/**
	 * Sort direction.
	 * @var string
	 * @since 1.0.0
	 */
	protected $sortDirection = 'desc';

	/**
	 * Filter value.
	 * @var string
	 * @since 2.0.0
	 */
	protected $filter = null;

	/**
	 * Filter by field.
	 * @var string
	 * @since 2.0.0
	 */
	protected $filterBy = null;

	/**
	 * Contest menu visibility.
	 *
	 * @var boolean $menu
	 * @since 1.0.0
	 */
	protected $menu = true;

	/**
	 * Contest menu items visibility.
	 *
	 * @var array $menuItemsVisibility
	 * @since 1.1.0
	 */
	protected $menuItemsVisibility = [
		'landing'     => true,
		'participate' => true,
		'submissions' => true,
		'pages'       => true,
	];

	/**
	 * Limitations
	 *
	 * @var \TotalContestVendors\TotalCore\Contracts\Limitations\Bag
	 * @since 1.0.0
	 */
	protected $limitations;

	/**
	 * Restrictions
	 *
	 * @var \TotalContestVendors\TotalCore\Contracts\Restrictions\Bag
	 * @since 1.0.0
	 */
	protected $restrictions;

	/**
	 * Error.
	 * @var null|\WP_Error
	 * @since 1.0.0
	 */
	protected $error;

	/**
	 * Model constructor.
	 *
	 * @param $attributes array Contest attributes.
	 *
	 * @since 1.0.0
	 */
	public function __construct( $attributes ) {
		/**
		 * Filters the contest attributes.
		 *
		 * @param array                                 $attributes Contest model attributes.
		 * @param \TotalContest\Contracts\Contest\Model $contest    Contest model object.
		 *
		 * @return array
		 * @since 2.0.0
		 */
		$attributes       = apply_filters( 'totalcontest/filters/contest/attributes', $attributes, $this );
		$this->attributes = $attributes;

		$this->id          = $attributes['id'];
		$this->contestPost = $attributes['post'];

		// Parse settings JSON.
		$this->settings = (array) json_decode( $this->contestPost->post_content, true );

		/**
		 * Filters the contest attributes.
		 *
		 * @param array                                 $settings Contest settings.
		 * @param \TotalContest\Contracts\Contest\Model $contest  Contest model object.
		 *
		 * @return array
		 * @since 2.0.0
		 */
		$this->settings = apply_filters( 'totalcontest/filters/contest/settings', $this->settings, $this );

		// Current page
		$this->currentPage = empty( $attributes['currentPage'] ) ? $this->currentPage : (int) $attributes['currentPage'];

		// Submission per page
		$this->submissionsPerPage = (int) $this->getSettingsItem( 'contest.submissions.perPage', 10 );

		// Sort
		$this->sortBy        = (string) $attributes['sortBy'] ?: $this->getSettingsItem( 'design.sorting.field', 'date' );
		$this->sortDirection = (string) $attributes['sortDirection'] ?: $this->getSettingsItem( 'design.sorting.direction', 'desc' );

		// Filter
		$this->filter   = (string) $attributes['filter'];
		$this->filterBy = (string) $attributes['filterBy'];

		// Content Id
		$this->customPage = (string) $attributes['customPage'] ?: '';

		// Action
		$this->action = (string) $attributes['action'] ?: $this->getDefaultPage();
		if ( ! $this->hasLandingPage() && $this->action === 'landing' ):
			$this->action = 'participate';
		endif;

		// Map of action => screen
		$actionScreenMap = [
			'landing'     => $this->hasLandingPage() ? 'contest.landing' : 'contest.participate',
			'submissions' => 'contest.submissions',
			'participate' => 'contest.participate',
			'content'     => 'contest.content',
			'submission'  => 'submission.view',
		];

		// Screen
		$this->screen = isset( $actionScreenMap[ $this->action ] ) ? $actionScreenMap[ $this->action ] : 'contest.participate';

		// Limitations
		$this->limitations = new \TotalContestVendors\TotalCore\Limitations\Bag();

		$periodArgs = $this->getSettingsItem( 'contest.limitations.period' );
		if ( ! empty( $periodArgs['enabled'] ) ):
			$this->limitations->add( 'period', new \TotalContest\Limitations\Period( $periodArgs ) );
		endif;

		$membershipArgs = $this->getSettingsItem( 'contest.limitations.membership' );
		if ( ! empty( $membershipArgs['enabled'] ) ):
			$this->limitations->add( 'membership', new \TotalContest\Limitations\Membership( $membershipArgs ) );
		endif;

		$quotaArgs = $this->getSettingsItem( 'contest.limitations.quota' );
		if ( ! empty( $quotaArgs['enabled'] ) ):
			$quotaArgs['currentValue'] = $this->getSubmissionsCount();
			$this->limitations->add( 'quota', new \TotalContest\Limitations\Quota( $quotaArgs ) );
		endif;

		/**
		 * Fires after limitations setup.
		 *
		 * @param \TotalContest\Contracts\Contest\Model $contest Contest model object.
		 *
		 * @since 2.0.0
		 */
		do_action( 'totalcontest/actions/contest/limitations', $this );

		// Restrictions
		$this->restrictions = new \TotalContestVendors\TotalCore\Restrictions\Bag();

		$frequencyArgs              = $this->getSettingsItem( 'contest.frequency' );
		$frequencyArgs['contest']   = $this;
		$frequencyArgs['action']    = 'submission';
		$frequencyArgs['fullCheck'] = TotalContest()->option( 'performance.fullChecks.enabled' );
		$frequencyArgs['message']   = __( 'You cannot submit new entries in this contest.' );

		if ( ! empty( $frequencyArgs['cookies']['enabled'] ) ):
			$this->restrictions->add( 'cookies', new \TotalContest\Restrictions\Cookies( $frequencyArgs ) );
		endif;

		if ( ! empty( $frequencyArgs['ip']['enabled'] ) ):
			$this->restrictions->add( 'ip', new \TotalContest\Restrictions\IPAddress( $frequencyArgs ) );
		endif;

		if ( ! empty( $frequencyArgs['user']['enabled'] ) ):
			$this->restrictions->add( 'user', new \TotalContest\Restrictions\LoggedInUser( $frequencyArgs ) );
		endif;

		/**
		 * Fires after restrictions setup.
		 *
		 * @param \TotalContest\Contracts\Contest\Model $contest       Contest model object.
		 * @param array                                 $frequencyArgs Frequency arguments.
		 *
		 * @since 2.0.0
		 */
		do_action( 'totalcontest/actions/contest/restrictions', $this, $frequencyArgs );

		/**
		 * Fires after contest model setup is completed.
		 *
		 * @param \TotalContest\Contracts\Contest\Model $contest Contest model object.
		 *
		 * @since 2.0.0
		 */
		do_action( 'totalcontest/actions/contest/setup', $this );
	}

	/**
	 * Has landing page.
	 *
	 * @return bool
	 */
	public function hasLandingPage() {
		$landingContent = $this->getSettingsItem( 'pages.landing.content' );

		return ! empty( $landingContent );
	}

	/**
	 * Get default page.
	 *
	 * @return string
	 */
	public function getDefaultPage( $default = 'landing' ) {
		return $this->getSettingsItem( 'pages.default', $default );
	}

	/**
	 * Get settings item.
	 *
	 * @param bool $needle  Settings name.
	 * @param bool $default Default value.
	 *
	 * @return mixed|array|null
	 * @since 1.0.0
	 */
	public function getSettingsItem( $needle, $default = null ) {
		/**
		 * Filters the contest settings item.
		 *
		 * @param array                                 $settings Contest settings.
		 * @param string                                $default  Default value.
		 * @param \TotalContest\Contracts\Contest\Model $contest  Contest model object.
		 *
		 * @return mixed
		 * @since 2.0.0
		 */
		return apply_filters( "totalcontest/filters/contest/settings-item/{$needle}", Arrays::getDotNotation( $this->settings, $needle, $default ), $this->settings, $default, $this );
	}

	/**
	 * Get settings section or item.
	 *
	 * @param bool $section Settings section.
	 * @param bool $args    Path to setting.
	 *
	 * @return mixed|array|null
	 * @since 1.0.0
	 */
	public function getSettings( $section = false, $args = false ) {
		// Deep selection.
		if ( $args !== false && $section && isset( $this->settings[ $section ] ) ):
			$paths = func_get_args();
			unset( $paths[0] );

			return Arrays::getDeep( $this->settings[ $section ], $paths );
		endif;

		// Return specific settings section, otherwise, return all settings.
		if ( $section ):
			return isset( $this->settings[ $section ] ) ? $this->settings[ $section ] : null;
		endif;

		return $this->settings;
	}

	/**
	 * Get submissions count.
	 *
	 * @return int
	 */
	public function getSubmissionsCount() {
		if ( $this->submissionsCount === null ):
			$this->submissionsCount = (int) TotalContest( 'submissions.repository' )->count( [ 'contest' => $this->id ] );
		endif;

		return $this->submissionsCount;
	}

	/**
	 * Get contest id.
	 *
	 * @return int
	 * @since 1.0.0
	 */
	public function getId() {
		return (int) $this->id;
	}

	/**
	 * Get action.
	 *
	 * @return string
	 * @since 1.0.0
	 */
	public function getAction() {
		return $this->action;
	}

	/**
	 * Get seo attributes.
	 *
	 * @return array
	 * @since 1.0.0
	 */
	public function getSeoAttributes() {
		if ( $this->seo === null ):
			$bindings = [
				'title'    => $this->getTitle(),
				'sitename' => get_bloginfo( 'name' ),
			];

			$this->seo = [
				'title'       => Strings::template( $this->getSettingsItem( 'seo.contest.title' ), $bindings ) ?: $this->getTitle(),
				'description' => Strings::template( $this->getSettingsItem( 'seo.contest.description' ), $bindings ),
			];

			$this->seo = array_map( 'wp_strip_all_tags', $this->seo );
		endif;

		/**
		 * Filters the contest seo attributes.
		 *
		 * @param array                                 $seo     SEO attributes.
		 * @param \TotalContest\Contracts\Contest\Model $contest Contest model object.
		 *
		 * @return array
		 * @since 2.0.0
		 */
		return apply_filters( 'totalcontest/filters/contest/seo', $this->seo, $this );
	}

	/**
	 * Get contest title.
	 *
	 * @return string
	 * @since 1.0.0
	 */
	public function getTitle() {
		return $this->contestPost->post_title;
	}

	/**
	 * Get share attributes.
	 *
	 * @return array
	 * @since 1.0.0
	 */
	public function getShareAttributes() {
		$websites     = array_filter( (array) TotalContest()->option( 'share.websites', [] ) );
		$websitesUrls = [
			'facebook'   => 'https://www.facebook.com/sharer.php?u={{url}}',
			'twitter'    => 'https://twitter.com/intent/tweet?url={{url}}',
			'googleplus' => 'https://plus.google.com/share?url={{url}}',
			'pinterest'  => 'https://pinterest.com/pin/create/bookmarklet/?url={{url}}',
			'whatsapp'   => 'whatsapp://send?text={{url}}',
		];

		foreach ( $websitesUrls as $website => $websiteUrl ):
			$shareUrl                 = home_url(
				add_query_arg(
					[
						'utm_source'   => $website,
						'utm_medium'   => 'contest-share-button',
						'utm_campaign' => $this->getTitle(),
					]
				)
			);
			$websitesUrls[ $website ] = Strings::template( $websiteUrl, [ 'url' => urlencode( $shareUrl ) ] );
		endforeach;

		/**
		 * Filters the contest sharing attributes.
		 *
		 * @param array                                 $attributes Sharing attributes.
		 * @param \TotalContest\Contracts\Contest\Model $contest    Contest model object.
		 *
		 * @return array
		 * @since 2.0.0
		 */
		return apply_filters( 'totalcontest/filters/contest/share', array_intersect_key( $websitesUrls, $websites ), $this );
	}

	/**
	 * Get contest thumbnail.
	 *
	 * @return false|string
	 * @since 1.0.0
	 */
	public function getThumbnail() {
		$thumbnail = wp_get_attachment_image_src( get_post_thumbnail_id( $this->id ), 'post-thumbnail' );

		$thumbnail = empty( $thumbnail[0] ) ? TotalContest()->env( 'url' ) . 'assets/dist/images/no-preview.png' : $thumbnail[0];

		/**
		 * Filters the contest thumbnail.
		 *
		 * @param array                                 $thumbnail Contest thumbnail.
		 * @param \TotalContest\Contracts\Contest\Model $contest   Contest model object.
		 *
		 * @return string
		 * @since 2.0.0
		 */
		return apply_filters( 'totalcontest/filters/contest/thumbnail', $thumbnail, $this );
	}

	/**
	 * Get time left to start.
	 *
	 * @param string $type Either 'contest' or 'vote'
	 *
	 * @return int|\DateInterval
	 * @since 1.0.0
	 */
	public function getTimeLeftToStart( $type = 'contest' ) {
		$startDate = $this->getStartDate( $type );
		$now       = TotalContest( 'datetime', [ 'now' ] );

		if ( $startDate && $startDate->getTimestamp() > current_time( 'timestamp' ) ):
			return $startDate->diff( $now, true );
		endif;

		return 0;
	}

	/**
	 * Get time left to end.
	 *
	 * @param string $type Either 'contest' or 'vote'
	 *
	 * @return int|\DateInterval
	 * @since 1.0.0
	 */
	public function getTimeLeftToEnd( $type = 'contest' ) {
		$endDate = $this->getEndDate( $type );
		$now     = TotalContest( 'datetime', [ 'now' ] );

		if ( $endDate && $endDate->getTimestamp() > current_time( 'timestamp' ) ):
			return $endDate->diff( $now, true );
		endif;

		return 0;
	}

	/**
	 * @param string $type
	 *
	 * @return null|\TotalContestVendors\TotalCore\Application|\TotalContestVendors\TotalCore\Contracts\Helpers\DateTime
	 */
	public function getStartDate( $type = 'contest' ) {
		$startDate = $this->getSettingsItem( "{$type}.limitations.period.start" );

		return $startDate ? TotalContest( 'datetime', [ $startDate ] ) : null;
	}

	/**
	 * @param string $type
	 *
	 * @return null|\TotalContestVendors\TotalCore\Application|\TotalContestVendors\TotalCore\Contracts\Helpers\DateTime
	 */
	public function getEndDate( $type = 'contest' ) {
		$endDate = $this->getSettingsItem( "{$type}.limitations.period.end" );

		return $endDate ? TotalContest( 'datetime', [ $endDate ] ) : null;
	}

	/**
	 * Get form fields.
	 *
	 * @return Field[]
	 * @since 2.0.0
	 */
	public function getFormFields() {
		$fields = [];
		foreach ( $this->getForm() as $page ):
			foreach ( $page as $field ):
				$fields[ $field->getName() ] = $field;
			endforeach;
		endforeach;

		/**
		 * Filters the contest form fields.
		 *
		 * @param array                                 $fields  Contest custom fields.
		 * @param \TotalContest\Contracts\Contest\Model $contest Contest model object.
		 *
		 * @return array
		 * @since 2.0.0
		 */
		return apply_filters( 'totalcontest/filters/contest/fields', $fields, $this );
	}

	/**
	 * Get form fields definitions.
	 *
	 * @return array
	 * @since 2.0.0
	 */
	public function getFormFieldsDefinitions() {
		$fields = [];
		foreach ( $this->getSettingsItem( 'contest.form.fields', [] ) as $field ):
			$fields[ $field['name'] ] = $field;
		endforeach;

		return $fields;
	}

	/**
	 * Get upload form.
	 *
	 * @return ParticipateForm Form object
	 * @since 1.0.0
	 */
	public function getForm() {
		return $this->form;
	}

	/**
	 * Set upload form.
	 *
	 * @param Form $form
	 *
	 * @return Form Form object
	 * @since 1.0.0
	 */
	public function setForm( Form $form ) {
		return $this->form = $form;
	}

	/**
	 * @return array|mixed|null|\WP_Post
	 */
	public function getContestPost() {
		return $this->contestPost;
	}

	/**
	 * Get categories terms objects.
	 *
	 * @return \WP_Term[]|int|\WP_Error
	 * @since 2.0.0
	 */
	public function getCategories() {
		$categories = [];

		foreach ( $this->getFormFieldsDefinitions() as $field ):
			if ( $field['type'] === 'category' ):
				$categories = get_terms( [
					'taxonomy'   => TC_SUBMISSION_CATEGORY_TAX_NAME,
					'fields'     => 'id=>name',
					'hide_empty' => false,
					'include'    => $field['options'],
				] );

				break;
			endif;
		endforeach;

		/**
		 * Filters the contest categories.
		 *
		 * @param array                                 $categories Contest categories.
		 * @param \TotalContest\Contracts\Contest\Model $contest    Contest model object.
		 *
		 * @return array
		 * @since 2.0.0
		 */
		return apply_filters( 'totalcontest/filters/contest/categories', $categories, $this );
	}

	/**
	 * Get menu items.
	 *
	 * @return array
	 * @since 1.0.0
	 */
	public function getMenuItems() {
		$items = [];

		if ( $this->hasLandingPage() && $this->getMenuItemVisibility( 'landing' ) ):
			$items['landing'] = [
				'label'  => $this->getSettingsItem( 'pages.landing.title' ) ?: __( 'Home', 'totalcontest' ),
				'url'    => $this->getLandingUrl(),
				'ajax'   => $this->getLandingAjaxUrl(),
				'active' => $this->isLandingScreen(),
			];
		endif;

		if ( $this->getMenuItemVisibility( 'participate' ) ):
			$items['participate'] = [
				'label'  => $this->getSettingsItem( 'pages.participate.title' ) ?: __( 'Participate', 'totalcontest' ),
				'url'    => $this->getParticipateUrl(),
				'ajax'   => $this->getParticipateAjaxUrl(),
				'active' => $this->isParticipateScreen(),
			];
		endif;

		if ( $this->getMenuItemVisibility( 'submissions' ) ):
			$items['submissions'] = [
				'label'  => $this->getSettingsItem( 'pages.submissions.title' ) ?: __( 'Submissions', 'totalcontest' ),
				'url'    => $this->getSubmissionsUrl(),
				'ajax'   => $this->getSubmissionsAjaxUrl(),
				'active' => $this->isSubmissionsScreen() || $this->isSubmissionScreen(),
			];
		endif;

		if ( $this->getMenuItemVisibility( 'pages' ) ):
			$pages = (array) $this->getSettingsItem( 'pages.other' );
			foreach ( $pages as $page ):

				$items[ $page['id'] ] = [
					'label'  => $page['title'],
					'url'    => $this->getCustomPageUrl( $page['id'] ),
					'ajax'   => $this->getCustomPageAjaxUrl( $page['id'] ),
					'active' => $this->isCustomPageScreen( $page['id'] ),
				];

			endforeach;
		endif;


		/**
		 * Filters the contest menu items.
		 *
		 * @param array                                 $items   Contest menu items.
		 * @param \TotalContest\Contracts\Contest\Model $contest Contest model object.
		 *
		 * @return array
		 * @since 2.0.0
		 */
		return apply_filters( 'totalcontest/filters/contest/menu', $items, $this );
	}

	/**
	 * Get menu item visibility.
	 *
	 * @param $item
	 *
	 * @return boolean
	 * @since    1.1.0
	 */
	public function getMenuItemVisibility( $item ) {
		return ! empty( $this->menuItemsVisibility[ $item ] );
	}

	/**
	 * Get url.
	 *
	 * @param array $args
	 *
	 * @return string
	 */
	public function getUrl( $args = [] ) {
		$args = Arrays::parse( $args, [ 'contestId' => $this->id, 'action' => $this->getAction() ] );
		$base = Misc::isDoingAjax() ? wp_get_referer() : home_url( $_SERVER['REQUEST_URI'] );

		$prettyPermalinks = (bool) get_option( 'permalink_structure' );
		$urlParameters    = [ 'totalcontest' => $args ];

		if ( ! Misc::isDoingAjax() && is_admin() ):
			$base = $this->getPermalink();

			if ( strpos( $this->getPermalink(), '&' ) !== false ):
				$prettyPermalinks = false;
			endif;
		endif;

		if ( $prettyPermalinks ):
			if ( is_singular( TC_CONTEST_CPT_NAME ) || is_singular( TC_SUBMISSION_CPT_NAME ) || Misc::isRestRequest() ):
				$base = $this->getPermalink();
			endif;

			$currentUrl = wp_parse_url( $base );
			$basePath   = preg_replace( '#(landing|participate|submissions|submission|content)/(.*)/?#i', '', $currentUrl['path'] );

			$path   = array_filter( explode( '/', $basePath ) );
			$path[] = $args['action'];
			if ( $args['action'] === 'content' && ! empty( $args['customPage'] ) ):
				$path[] = $args['customPage'];
				unset( $urlParameters['totalcontest']['customPage'] );
			elseif ( $args['action'] === 'submission' && ! empty( $args['submissionId'] ) ):
				$path[] = $args['submissionId'];
				unset( $urlParameters['totalcontest']['submissionId'] );
			elseif ( $args['action'] === 'submissions' ):
				$urlParameters['totalcontest']['sortDirection'] = empty( $args['sortDirection'] ) ? $this->sortDirection : $args['sortDirection'];
				$urlParameters['totalcontest']['sortBy']        = empty( $args['sortBy'] ) ? $this->sortBy : $args['sortBy'];
				$urlParameters['totalcontest']['filter']        = empty( $args['filter'] ) ? $this->filter : $args['filter'];
				$urlParameters['totalcontest']['filterBy']      = empty( $args['filterBy'] ) ? $this->filterBy : $args['filterBy'];
				$urlParameters['totalcontest']['page']          = empty( $args['page'] ) ? $this->currentPage : $args['page'];

				$urlParameters['totalcontest'] = array_filter( $urlParameters['totalcontest'] );
			endif;

			$path = '/' . implode( '/', $path ) . '/';
			$base = "${currentUrl['scheme']}://{$currentUrl['host']}{$path}";
			$base = user_trailingslashit( $base );

			unset( $urlParameters['totalcontest']['contestId'], $urlParameters['totalcontest']['action'] );
		endif;

		$urlParameters = TotalContest( 'url' )->compactParameters( $urlParameters );

		$url = add_query_arg( $urlParameters, $base );


		/**
		 * Filters the contest urls.
		 *
		 * @param string                                $url     URL.
		 * @param array                                 $args    Arguments.
		 * @param \TotalContest\Contracts\Contest\Model $contest Contest model object.
		 *
		 * @return string
		 * @since 2.0.0
		 */
		return apply_filters( 'totalcontest/filters/contest/url', $url, $args, $this );
	}

	/**
	 * Get AJAX url.
	 *
	 * @param array $args
	 *
	 * @return string
	 */
	public function getAjaxUrl( $args = [] ) {
		$args          = Arrays::parse( $args, [ 'contestId' => $this->id, 'action' => $this->getAction() ] );
		$base          = admin_url( 'admin-ajax.php' );
		$urlParameters = [ 'action' => 'totalcontest', 'totalcontest' => $args ];

		if ( $args['action'] === 'submissions' ):
			$urlParameters['totalcontest']['sortDirection'] = empty( $args['sortDirection'] ) ? $this->sortDirection : $args['sortDirection'];
			$urlParameters['totalcontest']['sortBy']        = empty( $args['sortBy'] ) ? $this->sortBy : $args['sortBy'];
			$urlParameters['totalcontest']['filter']        = empty( $args['filter'] ) ? $this->filter : $args['filter'];
			$urlParameters['totalcontest']['filterBy']      = empty( $args['filterBy'] ) ? $this->filterBy : $args['filterBy'];
			$urlParameters['totalcontest']['page']          = empty( $args['page'] ) ? $this->currentPage : $args['page'];

			$urlParameters['totalcontest'] = array_filter( $urlParameters['totalcontest'] );
		endif;

		$url = add_query_arg( $urlParameters, $base );

		/**
		 * Filters the contest urls.
		 *
		 * @param string                                $url     URL.
		 * @param array                                 $args    Arguments.
		 * @param \TotalContest\Contracts\Contest\Model $contest Contest model object.
		 *
		 * @return string
		 * @since 2.0.0
		 */
		return apply_filters( 'totalcontest/filters/contest/ajax-url', $url, $args, $this );
	}


	/**
	 * Get limitations bag.
	 *
	 * @return \TotalContestVendors\TotalCore\Contracts\Limitations\Bag
	 */
	public function getLimitations() {
		return $this->limitations;
	}

	/**
	 * Get restrictions bag.
	 *
	 * @return \TotalContestVendors\TotalCore\Contracts\Restrictions\Bag
	 */
	public function getRestrictions() {
		return $this->restrictions;
	}

	/**
	 * Get submissions divided per row.
	 *
	 * @param array $args
	 *
	 * @return array
	 */
	public function getSubmissionsRows( $args = [] ) {
		$perRow      = $this->getSettingsItem( 'design.layout.columns' ) ?: 4;
		$submissions = $this->getSubmissions( $args );

		return $perRow === 0 ? [ $submissions ] : array_chunk( $submissions, $perRow, true );
	}

	/**
	 * Get submissions with pagination.
	 *
	 * @param array $args
	 *
	 * @return mixed
	 */
	public function getSubmissionsWithPagination( $args = [] ) {
		if ( $this->filterBy === 'category' && ! empty( $this->filter ) ):
			$ids = array_keys( $this->getCategories() );

			if ( in_array( (int) $this->filter, $ids, true ) ):
				$args['category'] = $this->filter;
			endif;
		endif;

		$result = TotalContest( 'submissions.repository' )->paginate( [
			'contest'        => $this->id,
			'perPage'        => $this->submissionsPerPage,
			'page'           => $this->currentPage,
			'orderBy'        => $this->sortBy,
			'orderDirection' => $this->sortDirection,
		], $args );

		// Count submissions
		$this->submissionsCount = (int) $result['count'];

		// Count available pages
		$this->pagesCount = (int) $result['pages'];

		$result ['pagination'] = $this->getPaginationItems();

		// Return back the submissions
		return $result;
	}

	/**
	 * Get submissions.
	 *
	 * @param array $args
	 *
	 * @return mixed
	 */
	public function getSubmissions( $args = [] ) {
		if ( $this->filterBy === 'category' && ! empty( $this->filter ) ):
			$ids = array_keys( $this->getCategories() );

			if ( in_array( (int) $this->filter, $ids, true ) ):
				$args['category'] = $this->filter;
			endif;
		endif;

		$result = TotalContest( 'submissions.repository' )->paginate( [
			'contest'        => $this->id,
			'perPage'        => $this->submissionsPerPage,
			'page'           => $this->currentPage,
			'orderBy'        => $this->sortBy,
			'orderDirection' => $this->sortDirection,
		], $args );

		// Count submissions
		$this->submissionsCount = (int) $result['count'];

		// Count available pages
		$this->pagesCount = (int) $result['pages'];

		$result['pagination'] = $this->getPaginationItems();

		// Return back the submissions
		return $result['items'];
	}

	/**
	 * Get column width in percentage.
	 *
	 * @return float|int
	 */
	public function getColumnWidth() {
		$perRow = $this->getSettingsItem( 'design.layout.columns' ) ?: 4;

		return 100 / $perRow;
	}

	/**
	 * Get pagination for current query.
	 *
	 * @return array
	 */
	public function getPaginationItems() {
		$pages = [];

		for ( $page = 1; $page <= $this->pagesCount; $page ++ ):
			$pages[ $page ] = [
				'url'    => $this->getUrl( [ 'action' => 'submissions', 'page' => $page ] ),
				'ajax'   => $this->getAjaxUrl( [ 'action' => 'submissions', 'page' => $page ] ),
				'active' => $page === $this->currentPage,
				'number' => $page,
				'label'  => $page,
			];
			if ( $page === $this->currentPage ):
				if ( $page !== 1 ):
					$previous = $page - 1;
				endif;
				if ( $page + 1 < $this->pagesCount ):
					$next = $page;
				endif;
			endif;
		endfor;

		return $pages;
	}

	/**
	 * Check if there is previous page for current query.
	 *
	 * @return bool
	 */
	public function hasPreviousPage() {
		return $this->currentPage > 1;
	}

	/**
	 * Get previous page for current query.
	 *
	 * @return array
	 */
	public function getPreviousPagePaginationItem() {
		return [
			'url'    => $this->getUrl( [ 'action' => 'submissions', 'page' => $this->currentPage - 1 ] ),
			'ajax'   => $this->getAjaxUrl( [ 'action' => 'submissions', 'page' => $this->currentPage - 1 ] ),
			'active' => $this->hasPreviousPage(),
			'number' => $this->currentPage - 1,
			'label'  => __( 'Previous', 'totalcontest' ),
		];
	}

	/**
	 * Check if there is next page for current query.
	 *
	 * @return bool
	 */
	public function hasNextPage() {
		return $this->currentPage + 1 <= $this->pagesCount;
	}

	/**
	 * Get next page for current query.
	 *
	 * @return array
	 */
	public function getNextPagePaginationItem() {
		return [
			'url'    => $this->getUrl( [ 'action' => 'submissions', 'page' => $this->currentPage + 1 ] ),
			'ajax'   => $this->getAjaxUrl( [ 'action' => 'submissions', 'page' => $this->currentPage + 1 ] ),
			'active' => $this->hasNextPage(),
			'number' => $this->currentPage + 1,
			'label'  => __( 'Next', 'totalcontest' ),
		];
	}

	/**
	 * Get error object.
	 *
	 * @return null|\WP_Error
	 */
	public function getError() {
		return $this->error;
	}

	/**
	 * @param string|\WP_Error $error
	 */
	public function setError( $error ) {
		$this->error = is_wp_error( $error ) ? $error : new \WP_Error( 'error', $error );
	}

	/**
	 * @return bool
	 */
	public function hasError() {
		return ! empty( $this->error );
	}

	/**
	 * Get error message.
	 *
	 * @return null|string
	 */
	public function getErrorMessage() {
		return $this->error instanceof \WP_Error ? $this->error->get_error_message() : null;
	}

	/**
	 * Get sort by items.
	 *
	 * @return array
	 */
	public function getSortByItems() {
		$sortByItems = [
			'date'  => [
				'label'  => __( 'Date', 'totalcontest' ),
				'url'    => $this->getUrl( [
					'action'        => 'submissions',
					'sortDirection' => $this->sortDirection,
					'sortBy'        => 'date',
				] ),
				'ajax'   => $this->getAjaxUrl( [
					'action'        => 'submissions',
					'sortDirection' => $this->sortDirection,
					'sortBy'        => 'date',
				] ),
				'active' => $this->sortBy === 'date',
			],
			'views' => [
				'label'  => __( 'Views', 'totalcontest' ),
				'url'    => $this->getUrl( [
					'action'        => 'submissions',
					'sortDirection' => $this->sortDirection,
					'sortBy'        => 'views',
				] ),
				'ajax'   => $this->getAjaxUrl( [
					'action'        => 'submissions',
					'sortDirection' => $this->sortDirection,
					'sortBy'        => 'views',
				] ),
				'active' => $this->sortBy === 'views',
			],
			'votes' => [
				'label'  => __( 'Votes', 'totalcontest' ),
				'url'    => $this->getUrl( [
					'action'        => 'submissions',
					'sortDirection' => $this->sortDirection,
					'sortBy'        => 'votes',
				] ),
				'ajax'   => $this->getAjaxUrl( [
					'action'        => 'submissions',
					'sortDirection' => $this->sortDirection,
					'sortBy'        => 'votes',
				] ),
				'active' => $this->sortBy === 'votes',
			],
		];

		/**
		 * Filters the contest sort by items.
		 *
		 * @param array                                 $items   Contest sort-by items.
		 * @param \TotalContest\Contracts\Contest\Model $contest Contest model object.
		 *
		 * @return array
		 * @since 2.0.0
		 */
		return apply_filters( 'totalcontest/filters/contest/sort-by', $sortByItems, $this );
	}

	/**
	 * Get sort directions items.
	 *
	 * @return array
	 */
	public function getSortDirectionItems() {
		$sortDirectionItems = [
			'asc'  => [
				'label'  => __( 'Ascending', 'totalcontest' ),
				'url'    => $this->getUrl( [
					'action'        => 'submissions',
					'sortDirection' => 'asc',
					'sortBy'        => $this->sortBy,
				] ),
				'ajax'   => $this->getAjaxUrl( [
					'action'        => 'submissions',
					'sortDirection' => 'asc',
					'sortBy'        => $this->sortBy,
				] ),
				'active' => $this->sortDirection === 'asc',
			],
			'desc' => [
				'label'  => __( 'Descending', 'totalcontest' ),
				'url'    => $this->getUrl( [
					'action'        => 'submissions',
					'sortDirection' => 'desc',
					'sortBy'        => $this->sortBy,
				] ),
				'ajax'   => $this->getAjaxUrl( [
					'action'        => 'submissions',
					'sortDirection' => 'desc',
					'sortBy'        => $this->sortBy,
				] ),
				'active' => $this->sortDirection === 'desc',
			],
		];

		return $sortDirectionItems;
	}

	/**
	 * Get filter by items.
	 *
	 * @return array
	 */
	public function getFilterByItems() {
		$filterByItems = [
			[
				'label'  => __( 'Choose', 'totalcontest' ),
				'url'    => $this->getUrl( [
					'action'        => 'submissions',
					'sortDirection' => $this->sortDirection,
					'sortBy'        => $this->sortBy,
					'filter'        => '',
				] ),
				'ajax'   => $this->getAjaxUrl( [
					'action'        => 'submissions',
					'sortDirection' => $this->sortDirection,
					'sortBy'        => $this->sortBy,
					'filter'        => '',
				] ),
				'active' => false,
			],
		];
		$categories    = $this->getCategories();
		foreach ( $categories as $categoryId => $category ):
			$filterByCategorySlug                   = "category:{$categoryId}";
			$filterByItems[ $filterByCategorySlug ] = [
				'label'  => $category,
				'url'    => $this->getUrl( [
					'action'        => 'submissions',
					'sortDirection' => $this->sortDirection,
					'sortBy'        => $this->sortBy,
					'filterBy'      => 'category',
					'filter'        => $categoryId,
				] ),
				'ajax'   => $this->getAjaxUrl( [
					'action'        => 'submissions',
					'sortDirection' => $this->sortDirection,
					'sortBy'        => $this->sortBy,
					'filterBy'      => 'category',
					'filter'        => $categoryId,
				] ),
				'active' => $this->filterBy === 'category' && $this->filter == $categoryId,
			];
		endforeach;


		/**
		 * Filters the contest filter by items.
		 *
		 * @param array                                 $items   Contest sort-by items.
		 * @param \TotalContest\Contracts\Contest\Model $contest Contest model object.
		 *
		 * @return array
		 * @since 2.0.0
		 */
		return apply_filters( 'totalcontest/filters/contest/filter-by', $filterByItems, $this );
	}

	/**
	 * Get current page.
	 *
	 * @return int
	 */
	public function getCurrentPage() {
		return $this->currentPage;
	}

	/**
	 * Set current page.
	 *
	 * @param $page
	 */
	public function setCurrentPage( $page ) {
		$this->currentPage = absint( $page ) ?: 1;
	}

	/**
	 * Set action.
	 *
	 * @param string $action
	 */
	public function setAction( $action ) {
		$this->action = (string) $action;
	}

	/**
	 * Get prefix.
	 *
	 * @param string $append
	 *
	 * @return string
	 * @since 1.0.0
	 */
	public function getPrefix( $append = '' ) {
		return TotalContest()->env( 'prefix' ) . "{$this->id}_{$append}";
	}

	/**
	 * Get current screen.
	 *
	 * @return string Current screen.
	 * @since 1.0.0
	 */
	public function getScreen() {
		return $this->screen;
	}

	/**
	 * Set current screen.
	 *
	 * @param $screen string Screen name.
	 *
	 * @return $this
	 * @since 1.0.0
	 */
	public function setScreen( $screen ) {
		$this->screen = (string) $screen;

		return $this;
	}

	/**
	 * Get content pages
	 *
	 * @return array
	 * @since 2.0.0
	 */
	public function getCustomPages() {
		$pages = array_reduce(
			(array) $this->getSettingsItem( 'pages.other', [] ),
			function ( $pages, $item ) {
				$pages[ $item['id'] ] = $item;

				return $pages;
			}, [] );

		/**
		 * Filters the contest custom pages.
		 *
		 * @param array                                 $items   Contest custom pages.
		 * @param \TotalContest\Contracts\Contest\Model $contest Contest model object.
		 *
		 * @return array
		 * @since 2.0.0
		 */
		return apply_filters( 'totalcontest/filters/contest/custom-pages', $pages, $this );
	}

	/**
	 * Get current content page.
	 *
	 * @param null $id
	 *
	 * @return array|null
	 * @since 1.0.0
	 */
	public function getCustomPage( $id = null ) {
		if ( ! $id ):
			$id = $this->customPage;
		endif;

		$pages = (array) $this->getCustomPages();
		foreach ( $pages as $page ):
			if ( $id === $page['id'] ):
				return $page;
			endif;
		endforeach;

		return null;
	}

	/**
	 * Get current content page Id.
	 *
	 * @return string|null
	 * @since 1.0.0
	 */
	public function getCustomPageId() {
		return $this->customPage;
	}

	/**
	 * Set current content Id.
	 *
	 * @param $customPage
	 *
	 * @return $this
	 * @since 1.0.0
	 */
	public function setCustomPageId( $customPage ) {
		$this->customPage = (string) $customPage;

		return $this;
	}

	/**
	 * Get menu visibility.
	 *
	 * @return string Current screen.
	 * @since 1.0.0
	 */
	public function getMenuVisibility() {
		return $this->menu;
	}

	/**
	 * Set menu visibility.
	 *
	 * @param $visible
	 *
	 * @return $this
	 * @since 1.0.0
	 */
	public function setMenuVisibility( $visible ) {
		$this->menu = (boolean) $visible;

		return $this;
	}

	/**
	 * Set menu item visibility.
	 *
	 * @param $slug
	 *
	 * @return $this
	 * @since    1.1.0
	 */
	public function setMenuItemVisibility( $slug ) {
		$this->menuItemsVisibility[ $slug ] = true;

		return $this;
	}

	/**
	 * Get menu item visibility.
	 *
	 * @return array
	 * @since    1.1.0
	 */
	public function getMenuItemsVisibility() {
		return $this->menuItemsVisibility;
	}

	/**
	 * Set menu item visibility.
	 *
	 * @param $items
	 *
	 * @return $this
	 * @since    1.1.0
	 */
	public function setMenuItemsVisibility( $items ) {
		$this->menuItemsVisibility = (array) $items;

		return $this;
	}

	/**
	 * Get template id.
	 *
	 * @return mixed
	 * @since 2.0.0
	 */
	public function getTemplateId() {
		return $this->getSettingsItem( 'design.template', 'basic-template' );
	}

	/**
	 * Get preset id.
	 *
	 * @return string
	 * @since 2.0.0
	 */
	public function getPresetUid() {
		return $this->getSettingsItem( 'presetUid', md5( $this->getId() ) );
	}

	/**
	 * Get vote scale.
	 *
	 * @return int
	 * @since 2.0.0
	 */
	public function getVoteScale() {
		return (int) $this->getSettingsItem( 'vote.scale', 5 );
	}

	/**
	 * Get vote type.
	 *
	 * @return string
	 * @since 2.0.0
	 */
	public function getVoteType() {
		return $this->getSettingsItem( 'vote.type', 'count' );
	}

	/**
	 * Get vote criteria.
	 *
	 * @return array
	 * @since 2.0.0
	 */
	public function getVoteCriteria() {
		return (array) $this->getSettingsItem( 'vote.criteria', [] );
	}

	/**
	 * Get votes count from log.
	 *
	 * @return int
	 * @since 2.0.0
	 */
	public function getVotesFromLogs() {
		return TotalContest( 'log.repository' )->count( [ 'conditions' => [ 'contest_id' => $this->getId(), 'action' => 'vote', 'status' => 'accepted' ] ] );
	}

	/**
	 * Get contest votes count.
	 *
	 * @return int
	 * @since 1.0.0
	 */
	public function getVotes() {
		return (int) $this->getMetadata( '_tc_votes' );
	}

	/**
	 * Get contest votes formatted number.
	 *
	 * @return string
	 * @since 2.0.0
	 */
	public function getVotesNumber() {
		return number_format( $this->getVotes() );
	}

	/**
	 * Get contest votes with label.
	 * @return string
	 * @since 2.0.0
	 */
	public function getVotesWithLabel() {
		return sprintf( _n( '%s Vote', '%s Votes', $this->getVotes(), 'totalcontest' ), number_format( $this->getVotes() ) );
	}

	/**
	 * Is contest vote is rate.
	 *
	 * @return bool
	 * @since 2.0.0
	 */
	public function isRateVoting() {
		return $this->getVoteType() === 'rate';
	}

	/**
	 * Is contest vote is count.
	 *
	 * @return bool
	 * @since 2.0.0
	 */
	public function isCountVoting() {
		return $this->getVoteType() === 'count';
	}

	/**
	 * Is contest accepting submissions.
	 *
	 * @return bool
	 * @since 1.0.0
	 */
	public function isAcceptingSubmissions() {
		$limited = $this->getLimitations()->check();
		if ( $limited instanceof \WP_Error ):
			$this->error = $limited;

			return false;
		else:
			$restricted = $this->getRestrictions()->check();
			if ( $restricted instanceof \WP_Error ):
				$this->error = $restricted;

				return false;
			endif;
		endif;

		/**
		 * Filters whether the contest is accepting new submissions or not.
		 *
		 * @param bool                                  $acceptSubmission True when new submissions are accepted otherwise false.
		 * @param \TotalContest\Contracts\Contest\Model $contest          Contest model object.
		 *
		 * @return bool
		 * @since 2.0.0
		 */
		return apply_filters( 'totalcontest/filters/contest/accept-submissions', ! is_wp_error( $this->error ), $this );
	}

	/**
	 * Render contest.
	 *
	 * @return string
	 * @since 1.0.0
	 */
	public function render() {
		if ( $this->isPasswordProtected() ):
			return '';
		endif;

		$renderer = TotalContest( 'contests.renderer' );
		$renderer->setContest( $this );

		return $renderer;
	}

	/**
	 * Get contest permalink.
	 *
	 * @return string
	 * @since 2.0.0
	 */
	public function getPermalink() {
		if ( Misc::isRestRequest() ):
			return get_rest_url( null, TotalContest()->env( 'rest-namespace' ) . '/contest/' . $this->getId() . '/' );
		endif;

		return get_permalink( $this->contestPost );
	}

	/**
	 * JSON representation of contest.
	 *
	 * @return array
	 * @since 2.0.0
	 */
	public function jsonSerialize() {
		return $this->toArray();
	}

	/**
	 * @return bool
	 */
	public function isPasswordProtected() {
		return post_password_required( $this->contestPost );
	}

	/**
	 * Get the instance as an array.
	 *
	 * @return array
	 * @since 2.0.0
	 */
	public function toArray() {
		$contestAsArray = [
			'id'        => $this->getId(),
			'permalink' => $this->getPermalink(),
			'title'     => $this->getTitle() ?: 'Contest #' . $this->getId(),
			'menu'      => $this->getMenuItems(),
			'pages'     => $this->getCustomPages(),
			'forms'     => [
				'participate' => $this->getFormFields(),
			],
			'settings'  => [
				'design'  => $this->getSettingsItem( 'design' ),
				'vote'    => $this->isCountVoting() ? [ 'type' => 'count' ] : [
					'type'     => 'rate',
					'scale'    => $this->getVoteScale(),
					'criteria' => $this->getVoteCriteria(),
				],
				'sharing' => $this->getShareAttributes(),
			],
		];

		if ( is_admin() ):
			$contestAsArray['admin'] = [
				'editLink' => $this->getAdminEditLink(),
			];
		endif;

		/**
		 * Filters the array representation of contest.
		 *
		 * @param bool                                  $contestAsArray Contest as array.
		 * @param \TotalContest\Contracts\Contest\Model $contest        Contest model object.
		 *
		 * @return bool
		 * @since 2.0.0
		 */
		return apply_filters( 'totalcontest/filters/contest/array', $contestAsArray, $this );
	}

	/**
	 * @return string
	 */
	public function __toString() {
		return (string) $this->render();
	}

	/**
	 * Get landing url.
	 *
	 * @return mixed
	 */
	public function getLandingUrl( $args = [] ) {
		return $this->getUrl( Arrays::parse( [ 'action' => 'landing' ], $args ) );
	}

	/**
	 * Get landing ajax url.
	 *
	 * @param array $args
	 *
	 * @return mixed
	 */
	public function getLandingAjaxUrl( $args = [] ) {
		return $this->getAjaxUrl( Arrays::parse( [ 'action' => 'landing' ], $args ) );
	}

	/**
	 * Get participate url.
	 *
	 * @param array $args
	 *
	 * @return mixed
	 */
	public function getParticipateUrl( $args = [] ) {
		return $this->getUrl( Arrays::parse( [ 'action' => 'participate' ], $args ) );
	}

	/**
	 * Get participate ajax url.
	 *
	 * @param array $args
	 *
	 * @return mixed
	 */
	public function getParticipateAjaxUrl( $args = [] ) {
		return $this->getAjaxUrl( Arrays::parse( [ 'action' => 'participate' ], $args ) );
	}

	/**
	 * Get submissions url.
	 *
	 * @param array $args
	 *
	 * @return mixed
	 */
	public function getSubmissionsUrl( $args = [] ) {
		return $this->getUrl( Arrays::parse( [ 'action' => 'submissions' ], $args ) );
	}

	/**
	 * Get submissions ajax url.
	 *
	 * @param array $args
	 *
	 * @return mixed
	 */
	public function getSubmissionsAjaxUrl( $args = [] ) {
		return $this->getAjaxUrl( Arrays::parse( [ 'action' => 'submissions' ], $args ) );
	}

	/**
	 * Get custom page url.
	 *
	 * @param $pageId
	 *
	 * @return mixed
	 */
	public function getCustomPageUrl( $pageId, $args = [] ) {
		return $this->getUrl( Arrays::parse( [ 'action' => 'content', 'customPage' => $pageId ], $args ) );
	}

	/**
	 * Get custom page ajax url.
	 *
	 * @param $pageId
	 *
	 * @return mixed
	 */
	public function getCustomPageAjaxUrl( $pageId, $args = [] ) {
		return $this->getAjaxUrl( Arrays::parse( [ 'action' => 'content', 'customPage' => $pageId ], $args ) );
	}

	/**
	 * Get log page in WordPress dashboard.
	 * @return string
	 */
	public function getAdminLogLink() {
		return admin_url( "edit.php?post_type=contest&page=log&contest={$this->getId()}" );
	}

	/**
	 * Get submissions page in WordPress dashboard.
	 * @return string
	 */
	public function getAdminSubmissionsLink() {
		return admin_url( "edit.php?post_type=contest_submission&contest={$this->getId()}" );
	}

	/**
	 * Edit link in WordPress dashboard.
	 * @return string
	 */
	public function getAdminEditLink() {
		return get_edit_post_link( $this->getId(), '' );
	}

	/**
	 * Is current screen.
	 *
	 * @param $screen string Screen name.
	 *
	 * @return bool
	 * @since 1.0.0
	 */
	public function isScreen( $screen ) {
		return $this->screen === $screen;
	}

	/**
	 * Is landing screen.
	 *
	 * @return bool
	 * @since 1.0.0
	 */
	public function isLandingScreen() {
		return $this->isScreen( 'contest.landing' );
	}

	/**
	 * Is participate screen.
	 *
	 * @return bool
	 * @since 1.0.0
	 */
	public function isParticipateScreen() {
		return $this->isScreen( 'contest.participate' );
	}

	/**
	 * Is submissions screen.
	 *
	 * @return bool
	 * @since 1.0.0
	 */
	public function isSubmissionsScreen() {
		return $this->isScreen( 'contest.submissions' );
	}

	/**
	 * Is submission screen.
	 *
	 * @return bool
	 * @since 1.0.0
	 */
	public function isSubmissionScreen() {
		return $this->isScreen( 'submission.view' );
	}

	/**
	 * Is custom page screen.
	 *
	 * @param string $pageId
	 *
	 * @return bool
	 * @since 1.0.0
	 */
	public function isCustomPageScreen( $pageId = null ) {
		return $this->isScreen( 'contest.content' ) && ( empty( $pageId ) || $this->isCustomPage( $pageId ) );
	}

	/**
	 * Is content page.
	 *
	 * @param $pageId
	 *
	 * @return bool
	 */
	public function isCustomPage( $pageId ) {
		return $this->customPage && $this->customPage === $pageId;
	}

	/**
	 * Get received votes.
	 *
	 * @return int
	 * @since 2.0.0
	 */
	public function getReceivedVotes() {
		return $this->receivedVotes;
	}

	/**
	 * Increment votes.
	 *
	 * @param int $by
	 *
	 * @return int
	 * @since 2.0.0
	 */
	public function incrementVotes( $by = 1 ) {
		$this->receivedVotes += (int) $by;
		$this->incrementMetadata( '_tc_votes', $by );

		return $this->receivedVotes;
	}


	/**
	 * @return bool
	 */
	public function save() {
		return ! is_wp_error(
			wp_update_post( [
				'ID'           => $this->getId(),
				'post_content' => wp_slash( json_encode( $this->attributes ) ),
			] )
		);
	}
}
