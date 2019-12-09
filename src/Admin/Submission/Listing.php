<?php

namespace TotalContest\Admin\Submission;


use TotalContest\Contest\Repository as ContestsRepository;
use TotalContest\Submission\Repository as SubmissionsRepository;
use TotalContestVendors\TotalCore\Contracts\Http\Request;
use TotalContestVendors\TotalCore\Export\ColumnTypes\DateColumn;
use TotalContestVendors\TotalCore\Export\ColumnTypes\NumericColumn;
use TotalContestVendors\TotalCore\Export\ColumnTypes\TextColumn;
use TotalContestVendors\TotalCore\Export\Spreadsheet;
use TotalContestVendors\TotalCore\Export\Writers\CsvWriter;
use TotalContestVendors\TotalCore\Export\Writers\HTMLWriter;
use TotalContestVendors\TotalCore\Export\Writers\JsonWriter;

/**
 * Class Listing
 * @package TotalContest\Admin\Submission
 */
class Listing {
	public $contestId;
	public $contestRepository;
	public $submissionsRepository;
	public $contest;
	public $request;

	public function __construct( Request $request, ContestsRepository $contestRepository, SubmissionsRepository $submissionsRepository ) {
		$this->request               = $request;
		$this->contestRepository     = $contestRepository;
		$this->submissionsRepository = $submissionsRepository;
		// Get current contest ID
		$this->contestId = $this->request->query( 'contest' );
		$this->contest   = $contestRepository->getById( $this->contestId );

		// Setup hooks
		add_filter( 'parse_query', [ $this, 'filter' ] );
		add_filter( 'admin_url', [ $this, 'addNewUrl' ], 10, 3 );
		add_filter( 'restrict_manage_posts', [ $this, 'managePosts' ] );
		add_filter( 'manage_contest_submission_posts_columns', [ $this, 'columns' ] );
		add_action( 'manage_contest_submission_posts_custom_column', [ $this, 'columnsContent' ], 10, 2 );
		add_filter( 'contest_submission_sortable_columns', [ $this, 'columnsSortable' ] );
		add_filter( 'parent_file', [ $this, 'parentMenu' ] );
		add_filter( 'submenu_file', [ $this, 'subMenu' ] );
		add_filter( 'post_row_actions', [ $this, 'actions' ], 10, 2 );
		add_filter( 'display_post_states', [ $this, 'states' ], 10, 2 );
		add_filter( 'pre_get_posts', [ $this, 'scope' ] );

		add_action( 'admin_enqueue_scripts', [ $this, 'assets' ] );
		add_action( 'admin_footer', [ $this, 'templates' ] );

		if ( $this->contestId ):
			add_action( 'manage_posts_extra_tablenav', [ $this, 'exportButtons' ] );
		endif;
	}

	public function filter( $query ) {
		if ( $this->contestId ):
			$query->query_vars['post_parent'] = $this->contestId;
		endif;
	}

	public function exportButtons( $position ) {
		if ( $position === 'top' ):
			require_once __DIR__ . '/views/export.php';
		endif;
	}

	public function managePosts() {
		echo "<input type=\"hidden\" name=\"contest\" value=\"{$this->contestId}\">";
	}

	public function addNewUrl( $url, $path, $blogId ) {
		if ( $path === 'post-new.php?post_type=contest_submission' ):
			return "post-new.php?post_type=contest_submission&contest={$this->contestId}";
		endif;

		return $url;
	}

	public function parentMenu() {
		return 'edit.php?post_type=' . TC_CONTEST_CPT_NAME;
	}

	public function subMenu() {
		return 'edit.php?post_type=' . TC_SUBMISSION_CPT_NAME;
	}

	public function columns( $columns ) {
		return [
			'inline-preview'                              => __( 'Preview', 'totalcontest' ),
			'cb'                                          => '<input type="checkbox" />',
			'title'                                       => __( 'Title', 'totalcontest' ),
			'taxonomy-' . TC_SUBMISSION_CATEGORY_TAX_NAME => __( 'Category', 'totalcontest' ),
			'author'                                      => __( 'Author', 'totalcontest' ),
			'date'                                        => __( 'Date', 'totalcontest' ),
		];
	}

	public function columnsContent( $column, $id ) {
		$submission = $this->submissionsRepository->getById( $id );
		$submission = $submission->toArray() + [ 'fields' => $submission->getVisibleFields() ];
		foreach ( $submission['fields'] as $fieldIndex => $field ):
			if ( is_array( $field ) ):
				$submission['fields'][ $fieldIndex ] = implode( ', ', $field );
			endif;
		endforeach;

		$submission['preview'] = do_shortcode( $submission['preview'] );

		if ( $column === 'inline-preview' ):
			printf( '<script type="text/javascript">TotalContest.submissions[%d] = %s</script>', $id, json_encode( $submission ) );
			printf( '<button class="button" type="button"><span class="dashicons dashicons-visibility"></span></button>' );
		endif;
	}

	public function columnsSortable( $columns ) {
		$columns['id'] = 'id';

		return $columns;
	}

	public function assets() {
		wp_enqueue_script( 'totalcontest-admin-submissions-listing' );
		wp_enqueue_style( 'totalcontest-admin-submissions-listing');
	}

	public function actions( $actions, $post ) {
		$contestPostType = TC_CONTEST_CPT_NAME;
		if ( $post->post_status != 'publish' && current_user_can( 'publish_contest_submissions', $post->ID, $post->post_parent ) ):
			$actions = [ 'confirm' => sprintf( '<a href="%s" target="_blank">%s</a>', esc_attr( admin_url( "admin-ajax.php?action=totalcontest_contests_approve_submission&submission={$post->ID}" ) ), esc_html( __( 'Approve', 'totalcontest' ) ) ) ] + $actions;
		endif;

		$actions['contest'] = sprintf( '<a href="%s">%s</a>', esc_attr( admin_url( "post.php?post={$post->post_parent}&action=edit" ) ), esc_html( __( 'Contest', 'totalcontest' ) ) );

		if ( current_user_can( 'manage_options' ) ):
			$actions['log'] = sprintf( '<a href="%s">%s</a>', esc_attr( admin_url( "edit.php?post_type={$contestPostType}&page=log&contest={$post->post_parent}&submission={$post->ID}" ) ), esc_html( __( 'Log', 'totalcontest' ) ) );
		endif;

		return $actions;
	}

	public function states( $states, $post ) {
		$submission = $this->submissionsRepository->getById( $post );
		$states[]   = $submission->getVotesWithLabel();

		if ( $this->contest && $this->contest->isRateVoting() ):
			$states[] = $submission->getRateWithLabel();
		endif;

		return $states;
	}

	public function templates() {
		include __DIR__ . '/views/templates.php';
	}

	public function download() {
		if ( ! $this->contest ):
			exit;
		endif;

		$submissions = $this->contest->getSubmissions( [ 'posts_per_page' => - 1, 'paged' => 1 ] );

		$export = new Spreadsheet();

		$export->addColumn( new TextColumn( __( 'ID', 'totalcontest' ) ) );
		$export->addColumn( new TextColumn( __( 'Title', 'totalcontest' ) ) );
		$export->addColumn( new DateColumn( __( 'Date', 'totalcontest' ) ) );
		$export->addColumn( new TextColumn( __( 'Status', 'totalcontest' ) ) );
		$export->addColumn( new TextColumn( __( 'User', 'totalcontest' ) ) );
		$export->addColumn( new NumericColumn( __( 'Views', 'totalcontest' ) ) );
		$export->addColumn( new NumericColumn( __( 'Votes', 'totalcontest' ) ) );
		$export->addColumn( new NumericColumn( __( 'Rate', 'totalcontest' ) ) );

		$fields = (array) $this->contest->getFormFieldsDefinitions();

		foreach ( $fields as $field ):
			$export->addColumn( new TextColumn( __( 'Form: ', 'totalcontest' ) . ( empty( $field['label'] ) ? $field['name'] : $field['label'] ) ) );
		endforeach;

		foreach ( $submissions as $submission ):
			$row = [
				$submission->getId(),
				$submission->getTitle(),
				$submission->getDate()->format( '' ),
				$submission->isApproved() ? __( 'Approved', 'totalcontest' ) : __( 'In Review', 'totalcontest' ),
				$submission->getAuthor()->display_name,
				$submission->getViews(),
				$submission->getVotes(),
				$submission->getRate(),
			];

			foreach ( $fields as $field ):
				$row[] = $submission->getField( $field['name'], __( 'N/A', 'totalcontest' ) );
			endforeach;

			$export->addRow( $row );
		endforeach;

		$format = $this->request->request( 'export', 'html' );

		
			$writer = new HTMLWriter();
		
		

		$writer->includeColumnHeaders = true;

		$export->download( $writer, 'totalcontest-submissions-export-' . date( 'Y-m-d H:i:s' ) );

		exit;
	}

	/**
	 * @param \WP_Query $query
	 *
	 * @return mixed
	 */
	public function scope( $query ) {
		if ( ! current_user_can( 'edit_others_contest_submissions' ) ):
			if ( $query->get( 'post_type' ) === TC_SUBMISSION_CPT_NAME ):
				$subquery = new \WP_Query( [
					'post_type' => TC_CONTEST_CPT_NAME,
					'fields'    => 'ids',
					'nopaging'  => true,
					'author'    => get_current_user_id(),
				] );
				$ids      = $subquery->get_posts();
				if ( empty( $ids ) ):
					$ids[] = '';
				endif;
				$query->set( 'post_parent__in', $ids );
			endif;

			$query->set( 'no_found_rows', true );
		endif;

		return $query;
	}
}