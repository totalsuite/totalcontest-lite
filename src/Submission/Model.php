<?php

namespace TotalContest\Submission;

use DateInterval;
use TotalContest\Contracts\Submission\Model as ModelContract;
use TotalContestVendors\TotalCore\Contracts\Form\Field;
use TotalContestVendors\TotalCore\Contracts\Form\Form;
use TotalContestVendors\TotalCore\Contracts\Helpers\DateTime;
use TotalContestVendors\TotalCore\Helpers\Arrays;
use TotalContestVendors\TotalCore\Helpers\Misc;
use TotalContestVendors\TotalCore\Helpers\Strings;
use TotalContestVendors\TotalCore\Traits\Cookies;
use TotalContestVendors\TotalCore\Traits\Metadata;

/**
 * Submission model
 * @package TotalContest\Submission
 */
class Model implements ModelContract {
	use Metadata, Cookies;

	/**
	 * Submission ID.
	 *
	 * @var int|null
	 * @since 2.0.0
	 */
	protected $id = null;

	/**
	 * Submission attributes.
	 *
	 * @var array|null
	 * @since 2.0.0
	 */
	protected $attributes = null;

	/**
	 * Submission date.
	 *
	 * @var DateTime
	 * @since 2.0.0
	 */
	protected $date = null;

	/**
	 * Contest seo attributes.
	 *
	 * @var array|null
	 * @since 2.0.0
	 */
	protected $seo = null;

	/**
	 * Received votes.
	 *
	 * @var int
	 * @since 2.0.0
	 */
	protected $receivedVotes = 0;

	/**
	 * Received views.
	 *
	 * @var int
	 * @since 2.0.0
	 */
	protected $receivedViews = 0;

	/**
	 * Submission WordPress post.
	 *
	 * @var array|null|\WP_Post
	 * @since 2.0.0
	 */
	protected $submissionPost = null;

	/**
	 * Submission contest.
	 * @var \TotalContest\Contracts\Contest\Model
	 * @since 2.0.0
	 */
	protected $contest = null;

	/**
	 * Submission vote form.
	 *
	 * @var Form $form
	 * @since 2.0.0
	 */
	protected $form = null;

	/**
	 * Submission current screen
	 * @var string
	 * @since 2.0.0
	 */
	protected $screen = 'submission.view';

	/**
	 * Submission current action
	 * @var string
	 * @since 2.0.0
	 */
	protected $action = 'view';

	/**
	 * Limitations
	 *
	 * @var \TotalContestVendors\TotalCore\Contracts\Limitations\Bag
	 * @since 2.0.0
	 */
	protected $limitations;

	/**
	 * Restrictions
	 *
	 * @var \TotalContestVendors\TotalCore\Contracts\Restrictions\Bag
	 * @since 2.0.0
	 */
	protected $restrictions;

	/**
	 * Error.
	 * @var null|\WP_Error
	 * @since 2.0.0
	 */
	protected $error;

	/**
	 * Model constructor.
	 *
	 * @param array                                 $attributes
	 * @param \TotalContest\Contracts\Contest\Model $contest
	 *
	 * @since 2.0.0
	 */
	public function __construct( $attributes, $contest ) {
		$this->id             = $attributes['id'];
		$this->submissionPost = $attributes['post'];
		$this->date           = TotalContest( 'datetime', [ $this->submissionPost->post_date_gmt ] );
		$this->contest        = $contest;

		// Parse attributes JSON.
		$this->attributes = (array) json_decode( $this->submissionPost->post_content, true );

		/**
		 * Filters the submission attributes.
		 *
		 * @param array                                    $attributes Submission model attributes.
		 * @param \TotalContest\Contracts\Submission\Model $submission Submission model object.
		 * @param \TotalContest\Contracts\Contest\Model    $contest    Contest model object.
		 *
		 * @return array
		 * @since 2.0.0
		 */
		$this->attributes = apply_filters( 'totalcontest/filters/submission/attributes', $this->attributes, $this, $contest );

		// Limitations
		$this->limitations = new \TotalContestVendors\TotalCore\Limitations\Bag();

		$periodArgs = $contest->getSettingsItem( 'vote.limitations.period' );
		if ( ! empty( $periodArgs['enabled'] ) ):
			$this->limitations->add( 'period', new \TotalContest\Limitations\Period( $periodArgs ) );
		endif;

		$membershipArgs = $contest->getSettingsItem( 'vote.limitations.membership' );
		if ( ! empty( $membershipArgs['enabled'] ) ):
			$this->limitations->add( 'membership', new \TotalContest\Limitations\Membership( $membershipArgs ) );
		endif;

		$quotaArgs = $contest->getSettingsItem( 'vote.limitations.quota' );
		if ( ! empty( $quotaArgs['enabled'] ) ):
			$quotaArgs['currentValue'] = $this->getVotes();
			$this->limitations->add( 'quota', new \TotalContest\Limitations\Quota( $quotaArgs ) );
		endif;

		/**
		 * Fires after limitations setup.
		 *
		 * @param \TotalContest\Contracts\Submission\Model $contest Submission model object.
		 *
		 * @since 2.0.0
		 */
		do_action( 'totalcontest/actions/submission/limitations', $this );

		$this->restrictions = new \TotalContestVendors\TotalCore\Restrictions\Bag();

		$frequencyArgs               = $contest->getSettingsItem( 'vote.frequency', [ 'timeout' => 3600 ] );
		$frequencyArgs['contest']    = $contest;
		$frequencyArgs['submission'] = $this;
		$frequencyArgs['action']     = 'vote';
		$frequencyArgs['fullCheck']  = TotalContest()->option( 'performance.fullChecks.enabled' );
		$frequencyArgs['message']    = __( 'You cannot vote again.', 'totalcontest' );

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
		 * @param \TotalContest\Contracts\Submission\Model $submission    Submission model object.
		 * @param array                                    $frequencyArgs Frequency arguments.
		 *
		 * @since 2.0.0
		 */
		do_action( 'totalcontest/actions/submission/restrictions', $this, $frequencyArgs );

		/**
		 * Fires after submission model setup is completed.
		 *
		 * @param \TotalContest\Contracts\Submission\Model $submission Submission model object.
		 *
		 * @since 2.0.0
		 */
		do_action( 'totalcontest/actions/submission/setup', $this );
	}

	/**
	 * Get submissions votes count.
	 *
	 * @return int
	 * @since 2.0.0
	 */
	public function getVotes() {
		return absint( $this->getMetadata( '_tc_votes' ) );
	}

	/**
	 * Get submission votes formatted number.
	 *
	 * @return string
	 * @since 2.0.0
	 */
	public function getVotesNumber() {
		return number_format( $this->getVotes() );
	}

	/**
	 * Get submission votes with label.
	 * @return string
	 * @since 2.0.0
	 */
	public function getVotesWithLabel() {
		return sprintf( _n( '%s Vote', '%s Votes', $this->getVotes(), 'totalcontest' ), number_format( $this->getVotes() ) );
	}

	/**
	 * Get contest id.
	 *
	 * @return int
	 * @since 2.0.0
	 */
	public function getId() {
		return (int) $this->id;
	}

	/**
	 * Get contest date diff.
	 *
	 * @return DateInterval
	 * @since 2.0.0
	 */
	public function getDateDiff() {
		$now = TotalContest( 'datetime', [ 'now' ] );

		return $now->diff( $this->getDate(), true );
	}

	/**
	 * Get contest date diff.
	 *
	 * @return string
	 * @since 2.0.0
	 */
	public function getDateDiffForHuman() {
		$diff = $this->getDateDiff();

		if ( $diff->y > 0 ):
			return $diff->format( _n( '%y Year', '%y Years', $diff->y, 'totalcontest' ) );
		elseif ( $diff->m > 0 ):
			return $diff->format( _n( '%m Month', '%m Months', $diff->m, 'totalcontest' ) );
		elseif ( $diff->d > 0 ):
			return $diff->format( _n( '%d Day', '%d Days', $diff->d, 'totalcontest' ) );
		elseif ( $diff->h > 0 ):
			return $diff->format( _n( '%h Hour', '%h Hours', $diff->h, 'totalcontest' ) );
		elseif ( $diff->i > 0 ):
			return $diff->format( _n( '%i Minute', '%i Minutes', $diff->i, 'totalcontest' ) );
		else:
			return $diff->format( _n( '%s Second', '%s Seconds', $diff->s, 'totalcontest' ) );
		endif;
	}

	/**
	 * Get submission content.
	 *
	 * @return string
	 * @since 2.0.0
	 */
	public function getContent() {
		$content = Strings::template( $this->contest->getSettingsItem( 'contest.submissions.content' ), $this->getBindings() );
		$content = do_shortcode( $GLOBALS['wp_embed']->run_shortcode( $content ) );

		/**
		 * Filters the submission content.
		 *
		 * @param mixed                                    $content    Content.
		 * @param \TotalContest\Contracts\Submission\Model $submission Submission model object.
		 *
		 * @return mixed
		 * @since 2.0.0
		 */
		return apply_filters( 'totalcontest/filters/submission/content', $content, $this );
	}

	/**
	 * Get submission attributes.
	 *
	 * @return array|null
	 * @since 2.0.0
	 */
	public function getAttributes() {
		return $this->attributes;
	}

	/**
	 * Get attributes section or item.
	 *
	 * @param mixed $attribute Attribute name or dot notation representation
	 * @param null  $default   Default value to return when setAttribute is missing
	 *
	 * @return array|mixed|null
	 * @since    2.0.0
	 */
	public function getAttribute( $attribute, $default = null ) {
		/**
		 * Filters the submission attribute.
		 *
		 * @param mixed                                    $value      Attribute value.
		 * @param array                                    $attributes Attributes.
		 * @param string                                   $default    Default value.
		 * @param \TotalContest\Contracts\Submission\Model $submission Submission model object.
		 *
		 * @return mixed
		 * @since 2.0.0
		 */
		return apply_filters( "totalcontest/filters/submission/attribute/{$attribute}", Arrays::getDotNotation( $this->attributes, $attribute, $default ), $this->attributes, $default, $this );
	}

	/**
	 * Get field.
	 *
	 * @param      $field
	 * @param null $default
	 *
	 * @return array|mixed|null
	 */
	public function getField( $field, $default = null ) {
		return $this->getAttribute( "fields.$field", $default );
	}

	/**
	 * Get fields.
	 *
	 * @return array
	 * @since 2.0.0
	 */
	public function getFields() {
		return (array) $this->getAttribute( 'fields', [] );
	}

	/**
	 * Get visible fields.
	 *
	 * @return array
	 * @since 2.0.0
	 */
	public function getVisibleFields() {
		$fields     = array_diff_key( $this->getFields(), [ 'action' => true, 'contestId' => true, 'category' => true ] );
		$formFields = $this->getContest()->getFormFieldsDefinitions();
		foreach ( $formFields as $field ):
			if ( ! empty( $fields[ $field['name'] ] ) && in_array( $field['type'], [ 'image', 'video', 'audio' ] ) ):
				$fields[ $field['name'] ] = wp_get_attachment_url( $fields[ $field['name'] ] );
			endif;

			if ( ! empty( $fields["{$field['name']}_url"] ) ):
				$fields[ $field['name'] ] = $fields["{$field['name']}_url"];
			endif;

			if ( isset( $fields["{$field['name']}_url"] ) ):
				unset( $fields["{$field['name']}_url"] );
			endif;
		endforeach;

		return $fields;
	}

	/**
	 * Get bindings.
	 *
	 * @return array
	 * @since 1.1.0
	 */
	public function getBindings() {
		$bindings = [
			'id'             => $this->getId(),
			'contest'        => $this->getContest()->getTitle(),
			'title'          => $this->getTitle() ?: __( 'Submission #', 'totalcontest' ) . $this->getId(),
			'sitename'       => get_bloginfo( 'name' ),
			'user'           => $this->getAuthor()->to_array(),
			'fields'         => $this->getAttribute( 'fields', [] ),
			'contents'       => $this->getAttribute( 'contents', [] ),
			'date'           => $this->getDate()->toArray(),
			'category'       => get_term( $this->getAttribute( 'category' ), TC_SUBMISSION_CATEGORY_TAX_NAME ),
			'views'          => $this->getViews(),
			'votes'          => $this->getVotes(),
			'votesWithLabel' => $this->getVotesWithLabel(),
			'rate'           => $this->getRate(),
			'rateWithLabel'  => $this->getRateWithLabel(),
		];

		/**
		 * Filters the bindings.
		 *
		 * @param array                                    $bindings   Bindings.
		 * @param \TotalContest\Contracts\Submission\Model $submission Submission model object.
		 *
		 * @return array
		 * @since 2.0.0
		 */
		return apply_filters( 'totalcontest/filters/submission/bindings', $bindings, $this );
	}

	/**
	 * @return \WP_Post
	 */
	public function getSubmissionPost() {
		return $this->submissionPost;
	}

	/**
	 * Get contest.
	 *
	 * @return null|string|\TotalContest\Contracts\Contest\Model
	 * @since 2.0.0
	 */
	public function getContest() {
		return $this->contest;
	}

	/**
	 * Get submission title.
	 *
	 * @return string
	 * @since 2.0.0
	 */
	public function getTitle() {
		return wp_kses_post(
			Strings::template(
				$this->contest->getSettingsItem( 'contest.submissions.title' ),
				[
					'id'             => $this->getId(),
					'contest'        => $this->getContest()->getTitle(),
					'sitename'       => get_bloginfo( 'name' ),
					'user'           => $this->getAuthor()->to_array(),
					'fields'         => $this->getAttribute( 'fields', [] ),
					'date'           => $this->getDate()->toArray(),
					'category'       => get_term( $this->getAttribute( 'category' ), TC_SUBMISSION_CATEGORY_TAX_NAME ),
					'views'          => $this->getViews(),
					'votes'          => $this->getVotes(),
					'votesWithLabel' => $this->getVotesWithLabel(),
					'rate'           => $this->getRate(),
					'rateWithLabel'  => $this->getRateWithLabel(),
				],
				$this->submissionPost->post_title
			)
		);
	}

	/**
	 * Get contest date.
	 *
	 * @return DateTime
	 * @since 2.0.0
	 */
	public function getDate() {
		return $this->date;
	}

	/**
	 * Get submission views count.
	 *
	 * @return int
	 * @since 2.0.0
	 */
	public function getViews() {
		return (int) $this->getMetadata( '_tc_views' );
	}

	/**
	 * Get submission views formatted number.
	 *
	 * @return string
	 * @since 2.0.0
	 */
	public function getViewsNumber() {
		return number_format( $this->getViews() );
	}


	/**
	 * Get submission views with label.
	 *
	 * @return string
	 * @since 2.0.0
	 */
	public function getViewsWithLabel() {
		return sprintf( _n( '%s View', '%s Views', $this->getViews(), 'totalcontest' ), number_format( $this->getViews() ) );
	}

	/**
	 * Get rate.
	 *
	 * @return float
	 * @since 2.0.0
	 */
	public function getRate() {
		return (float) $this->getMetadata( '_tc_rate' );
	}

	/**
	 * Get submission rate formatted number.
	 *
	 * @return string
	 * @since 2.0.0
	 */
	public function getRateNumber() {
		return number_format( $this->getRate(), 1 );
	}

	/**
	 * Get submission rate with label.
	 *
	 * @return string
	 * @since 2.0.0
	 */
	public function getRateWithLabel() {
		$scale = $this->getContest()->getVoteScale();

		return sprintf( __( '%s of %s', 'totalcontest' ), $this->getRateNumber(), $scale );
	}

	/**
	 * Get submission permalink.
	 *
	 * @return false|string
	 * @since 2.0.0
	 */
	public function getPermalink( $args = [] ) {
		if ( Misc::isRestRequest() ):
			return get_rest_url( null, TotalContest()->env( 'rest-namespace' ) . '/submission/' . $this->getId() );
		endif;

		return $this->getUrl();
	}

	/**
	 * Edit link in WordPress dashboard.
	 * @return string
	 */
	public function getAdminEditLink() {
		return get_edit_post_link( $this->getId() );
	}

	/**
	 * Get thumbnail url.
	 *
	 * @return string
	 * @since 2.0.0
	 */
	public function getThumbnailUrl() {
		$contest   = $this->getContest();
		$source    = $contest->getSettingsItem( 'contest.submissions.preview.source' );
		$noPreview = $contest->getSettingsItem( 'contest.submissions.preview.default' ) ?: TotalContest()->env( 'url' ) . 'assets/dist/images/no-preview.png';

		if ( empty( $noPreview ) ):
			$noPreview = wp_get_attachment_url( get_post_thumbnail_id( $this->submissionPost ) );
		endif;

		$thumbnail = $this->getAttribute( "contents.{$source}.thumbnail.url" ) ?: $noPreview;

		/**
		 * Filters the submission thumbnail.
		 *
		 * @param string                                   $thumbnail  Submission thumbnail.
		 * @param \TotalContest\Contracts\Submission\Model $submission Submission  model object.
		 *
		 * @return string
		 * @since 2.0.0
		 */
		return apply_filters( 'totalcontest/filters/submission/thumbnail', $thumbnail, $this );
	}

	/**
	 * Get preview
	 */
	public function getPreview() {
		$source  = $this->getContest()->getSettingsItem( 'contest.submissions.preview.source' );
		$preview = $this->getAttribute( "contents.{$source}.preview" );
		$preview = do_shortcode( $preview );

		if ( empty( $preview ) ):
			$preview = sprintf( '[totalcontest-image src="%s"]', $this->getThumbnailUrl() );
			$preview = do_shortcode( $preview );
		endif;

		/**
		 * Filters the submission preview.
		 *
		 * @param string                                   $thumbnail  Submission preview.
		 * @param \TotalContest\Contracts\Submission\Model $submission Submission  model object.
		 *
		 * @return string
		 * @since 2.0.0
		 */
		return apply_filters( 'totalcontest/filters/submission/preview', $preview, $this );
	}

	/**
	 * Get category term object.
	 *
	 * @return \WP_Term|null
	 */
	public function getCategory() {
		$category = get_term( $this->getAttribute( 'fields.category' ) );
		$category = $category instanceof \WP_Error || ! $category ? null : $category;

		/**
		 * Filters the submission category.
		 *
		 * @param \WP_Term|null                            $category   Submission category term.
		 * @param \TotalContest\Contracts\Submission\Model $submission Submission  model object.
		 *
		 * @return string
		 * @since 2.0.0
		 */
		return apply_filters( 'totalcontest/filters/submission/category', $category, $this );
	}

	/**
	 * Get category name.
	 *
	 * @return string|null
	 */
	public function getCategoryName() {
		$category = $this->getCategory();

		return $category ? $category->name : null;
	}

	/**
	 * Get submission type.
	 *
	 * @return string
	 * @since 2.0.0
	 */
	public function getType() {
		return $this->getAttribute( 'type', $this->contest->getSettingsItem( 'contest.participate.type' ) );
	}

	/**
	 * Get meta information.
	 *
	 * @return string
	 * @since 2.0.0
	 */
	public function getSubtitle() {
		return wp_kses_post(
			Strings::template( $this->contest->getSettingsItem( 'contest.submissions.subtitle' ), $this->getBindings() )
		);
	}

	/**
	 * Get seo attributes.
	 *
	 * @return array
	 * @since 2.0.0
	 */
	public function getSeoAttributes() {
		if ( $this->seo === null ):
			$bindings = $this->getBindings();

			$this->seo = [
				'title'       => Strings::template(
					$this->getContest()->getSettingsItem( 'seo.submission.title' ),
					$bindings
				) ?: $this->getTitle(),
				'description' => Strings::template(
					$this->getContest()->getSettingsItem( 'seo.submission.description' ),
					$bindings
				) ?: $this->getSubtitle(),
			];
			$this->seo = array_map( 'wp_strip_all_tags', $this->seo );
		endif;

		/**
		 * Filters the submission seo attributes.
		 *
		 * @param array                                    $seo        SEO attributes.
		 * @param \TotalContest\Contracts\Submission\Model $submission Submission model object.
		 *
		 * @return array
		 * @since 2.0.0
		 */
		return apply_filters( 'totalcontest/filters/submission/seo', $this->seo, $this );
	}

	/**
	 * Get ratings.
	 *
	 * @return array
	 * @since 2.0.0
	 */
	public function getRatings() {
		$scale    = $this->getContest()->getVoteScale();
		$criteria = $this->getContest()->getVoteCriteria();

		foreach ( $criteria as $criterionIndex => $criterion ):
			$criteria[ $criterionIndex ]['value'] = number_format( (float) $this->getMetadata( "_tc_{$criterionIndex}:{$scale}_rate" ), 1 );
		endforeach;

		/**
		 * Filters the submission ratings.
		 *
		 * @param array                                    $criteria   Submission ratings.
		 * @param \TotalContest\Contracts\Submission\Model $submission Submission model object.
		 *
		 * @return string
		 * @since 2.0.0
		 */
		return apply_filters( 'totalcontest/filters/submission/ratings', $criteria, $this );
	}

	/**
	 * Get detailed ratings.
	 *
	 * @return array
	 * @since 2.0.0
	 */
	public function getDetailedRatings() {
		$scale    = $this->getContest()->getVoteScale();
		$criteria = $this->getContest()->getVoteCriteria();

		foreach ( $criteria as $criterionIndex => $criterion ):
			$criterionVotes      = (int) $this->getMetadata( "_tc_{$criterionIndex}:{$scale}_votes" );
			$criterionCumulative = (int) $this->getMetadata( "_tc_{$criterionIndex}:{$scale}_cumulative" );
			$criterionRate       = (float) $criterionCumulative / ( $criterionVotes === 0 ? 1 : $criterionVotes );

			$criteria[ $criterionIndex ]['scale']      = $scale;
			$criteria[ $criterionIndex ]['votes']      = $criterionVotes;
			$criteria[ $criterionIndex ]['cumulative'] = $criterionCumulative;
			$criteria[ $criterionIndex ]['rate']       = $criterionRate;
		endforeach;

		/**
		 * Filters the submission ratings.
		 *
		 * @param array                                    $criteria   Submission ratings.
		 * @param \TotalContest\Contracts\Submission\Model $submission Submission model object.
		 *
		 * @return string
		 * @since 2.0.0
		 */
		return apply_filters( 'totalcontest/filters/submission/detailed-ratings', $criteria, $this );
	}

	/**
	 * Is submission embeddable.
	 */
	public function isEmbeddable() {
		return strpos( $this->submissionPost->post_content, '[embed]' ) !== false;
	}

	/**
	 * Is submission approved.
	 *
	 * @return bool
	 * @since 2.0.0
	 */
	public function isApproved() {
		$approved = $this->submissionPost->post_status === 'publish';

		/**
		 * Filters the submission approval state.
		 *
		 * @param bool                                     $approved   True when approved otherwise false.
		 * @param \TotalContest\Contracts\Submission\Model $submission Submission  model object.
		 *
		 * @return bool
		 * @since 2.0.0
		 */
		return apply_filters( 'totalcontest/filters/submission/approved', $approved, $this );
	}

	/**
	 * Is submission accepting votes.
	 *
	 * @return bool
	 * @since 2.0.0
	 */
	public function isAcceptingVotes() {
		$limited = $this->getLimitations()->check();
		if ( $limited instanceof \WP_Error ):
			$this->error = $limited;
		else:
			$restricted = $this->getRestrictions()->check();
			if ( $restricted instanceof \WP_Error ):
				$this->error = $restricted;
			endif;
		endif;

		if ( $this->isWinner() ):
			$this->error = new \WP_Error( 'winner', __( 'You cannot vote for winners.', 'totalcontest' ) );
		endif;

		/**
		 * Filters whether the submission is accepting new votes or not.
		 *
		 * @param bool                                     $acceptVotes True when new votes are accepted otherwise false.
		 * @param \TotalContest\Contracts\Submission\Model $submission  Submission  model object.
		 *
		 * @return bool
		 * @since 2.0.0
		 */
		return apply_filters( 'totalcontest/filters/submission/accept-vote', ! is_wp_error( $this->error ), $this );
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
	 * Is a winning submission.
	 *
	 * @return bool
	 * @since 2.0.0
	 */
	public function isWinner() {
		return (bool) $this->getAttribute( 'designation.winner', false );
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
	 * Get error message.
	 *
	 * @return mixed|null|string
	 */
	public function getErrorMessage() {
		return $this->error instanceof \WP_Error ? $this->error->get_error_message() : null;
	}

	/**
	 * @return bool
	 */
	public function hasError() {
		return ! empty( $this->error );
	}

	/**
	 * Whether the current user owns the submission.
	 *
	 * @return bool
	 * @since 2.0.0
	 */
	public function isOwner() {
		$viaUser   = get_current_user_id() === (int) $this->submissionPost->post_author;
		$viaCookie = $this->getAttribute( 'token' ) === $this->getCookie( $this->getContest()->getPrefix( "token_{$this->getId()}" ) );

		/**
		 * Filters whether the submission is owned by the current user or not.
		 *
		 * @param bool                                     $owned      True when is owned by current user otherwise false.
		 * @param \TotalContest\Contracts\Submission\Model $submission Submission  model object.
		 *
		 * @return bool
		 * @since 2.0.0
		 */
		return apply_filters( 'totalcontest/filters/submission/owner', $viaUser || $viaCookie, $this );
	}

	/**
	 * Get submission's author.
	 *
	 * @return \WP_User
	 * @since 1.2.0
	 */
	public function getAuthor() {
		return new \WP_User( $this->submissionPost->post_author );
	}

	/**
	 * Get vote form.
	 *
	 * @return Form Form object
	 * @since 2.0.0
	 */
	public function getForm() {
		return $this->form;
	}

	/**
	 * Set vote form.
	 *
	 * @param Form $form
	 *
	 * @return Form Form object
	 * @since 2.0.0
	 */
	public function setForm( Form $form ) {
		return $this->form = $form;
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
		 * Filters the submission form fields.
		 *
		 * @param array                                    $fields     Submission custom fields.
		 * @param \TotalContest\Contracts\Submission\Model $submission Submission model object.
		 *
		 * @return array
		 * @since 2.0.0
		 */
		return apply_filters( 'totalcontest/filters/submission/fields', $fields, $this );
	}

	/**
	 * Get submission URL with arguments.
	 *
	 * @param array $args
	 *
	 * @return string
	 * @since 2.0.0
	 */
	public function getUrl( $args = [] ) {
		$args['action']       = 'submission';
		$args['submissionId'] = $this->getId();

		return $this->getContest()->getUrl( $args );
	}

	/**
	 * Get AJAX url.
	 *
	 * @param array $args
	 *
	 * @return string
	 */
	public function getAjaxUrl( $args = [] ) {
		$args['action']       = 'view';
		$args['submissionId'] = $this->getId();

		return $this->getContest()->getAjaxUrl( $args );
	}


	/**
	 * Get prefix.
	 *
	 * @param string $append
	 *
	 * @return string
	 * @since 2.0.0
	 */
	public function getPrefix( $append = '' ) {
		return $this->getContest()->getPrefix( "{$this->getId()}_{$append}" );
	}


	/**
	 * Get current screen.
	 *
	 * @return string Current screen.
	 * @since 2.0.0
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
	 * @since 2.0.0
	 */
	public function setScreen( $screen ) {
		$this->screen = (string) $screen;

		return $this;
	}

	/**
	 * Get share attributes.
	 *
	 * @return array
	 * @since 2.0.0
	 */
	public function getShareAttributes() {
		return $this->getContest()->getShareAttributes();
	}

	/**
	 * Get template instance.
	 *
	 * @return mixed
	 * @since 1.4.0
	 */
	public function getTemplateId() {
		return $this->getContest()->getTemplateId();
	}

	/**
	 * Render submission.
	 *
	 * @return string
	 * @since 2.0.0
	 */
	public function render() {
		$contest  = $this->getContest();
		$contest  = $contest->setScreen( 'submission.view' );
		$renderer = $contest->render();

		if ( $renderer ):
			$renderer->setSubmission( $this );
		endif;

		return $renderer;
	}

	/**
	 * JSON representation of submission.
	 *
	 * @return array
	 * @since 2.0.0
	 */
	public function jsonSerialize() {
		return $this->toArray();
	}

	/**
	 * Get the instance as an array.
	 *
	 * @return array
	 * @since 2.0.0
	 */
	public function toArray() {
		$submissionAsArray = [
			'id'        => $this->getId(),
			'permalink' => $this->getPermalink(),
			'title'     => $this->getTitle(),
			'subtitle'  => $this->getSubtitle(),
			'datetime'  => $this->getDate()->toArray(),
			'author'    => $this->getAuthor()->display_name,
			'votes'     => [
				'count'    => $this->getVotes(),
				'rate'     => $this->getRate(),
				'scale'    => $this->getContest()->getVoteScale(),
				'type'     => $this->getContest()->getVoteType(),
				'criteria' => $this->getDetailedRatings(),
			],
			'approved'  => $this->isApproved(),
			'winner'    => $this->isWinner(),
			'owner'     => $this->isOwner(),
			'category'  => $this->getCategoryName(),
			'content'   => $this->getContent(),
			'preview'   => $this->getPreview(),
			'thumbnail' => $this->getThumbnailUrl(),
			'forms'     => [
				'vote' => $this->getFormFields(),
			],
		];

		if ( is_admin() ):
			$submissionAsArray['admin'] = [
				'editLink' => $this->getAdminEditLink(),
			];
		endif;

		/**
		 * Filters the array representation of submission.
		 *
		 * @param bool                                     $submissionAsArray Submission as array.
		 * @param \TotalContest\Contracts\Submission\Model $submission        Submission model object.
		 *
		 * @return bool
		 * @since 2.0.0
		 */
		return apply_filters( 'totalcontest/filters/submission/array', $submissionAsArray, $this );
	}


	/**
	 * @return string
	 */
	public function __toString() {
		return (string) $this->render();
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
	 * Get received views.
	 *
	 * @return int
	 * @since 2.0.0
	 */
	public function getReceivedViews() {
		return $this->receivedViews;
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
		$this->contest->incrementVotes( $by );

		return $this->receivedVotes;
	}

	/**
	 * Increment views.
	 *
	 * @param int $by
	 *
	 * @return int
	 * @since 2.0.0
	 */
	public function incrementViews( $by = 1 ) {
		$this->receivedViews += (int) $by;
		$this->incrementMetadata( '_tc_views', $by );

		return $this->receivedViews;
	}

	/**
	 * Get contest current action.
	 *
	 * @return mixed
	 * @since 2.0.0
	 */
	public function getAction() {
		return $this->action;
	}

	/**
	 * Set contest current action.
	 *
	 * @param $action
	 *
	 * @return void
	 * @since 2.0.0
	 */
	public function setAction( $action ) {
		$this->action = $action;
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

	/**
	 * Increment rate.
	 *
	 * @param array $values
	 *
	 * @return array
	 * @since 2.0.0
	 */
	public function incrementRatings( $values = [] ) {
		$contest = $this->getContest();
		$ratings = [];

		if ( $contest->isRateVoting() ):
			// Rating
			$scale              = $contest->getVoteScale();
			$criteria           = $contest->getVoteCriteria();
			$criteriaCumulative = 0;

			// Bail if values count does not match criteria count
			if ( count( $values ) !== count( $criteria ) ):
				return $ratings;
			endif;

			// Iterate over criteria
			foreach ( $criteria as $criterionIndex => $criterion ):
				$value = isset( $values[ $criterionIndex ] ) ? absint( $values[ $criterionIndex ] ) : 0;
				$value = $value > $scale ? $scale : $value;

				$criterionVotes      = (int) $this->incrementMetadata( "_tc_{$criterionIndex}:{$scale}_votes" );
				$criterionCumulative = $this->incrementMetadata( "_tc_{$criterionIndex}:{$scale}_cumulative", $value );

				$criterionRate = (float) ( $criterionCumulative / $criterionVotes );
				$this->updateMetadata( "_tc_{$criterionIndex}:{$scale}_rate", $criterionRate );

				$criteriaCumulative += $criterionRate;
				$ratings[]          = $criterionRate;
			endforeach;

			$rate = $criteriaCumulative / count( $criteria );
			$this->updateMetadata( '_tc_rate', (float) $rate );
		endif;

		return $ratings;
	}


	/**
	 * @return bool
	 */
	public function hasVoted() {
		/**
		 * Filters whether current user has voted before.
		 *
		 * @param bool                                     $hasVoted   Whether current user has voted or not.
		 * @param \TotalContest\Contracts\Submission\Model $submission Submission model object.
		 *
		 * @return array
		 * @since 2.0.0
		 */
		return apply_filters( 'totalcontest/filters/submissions/has-voted', is_wp_error( $this->getRestrictions()->check() ) || $this->getRestrictions()->isApplied(), $this );
	}
}
