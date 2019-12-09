<?php

namespace TotalContest\Submission;

use TotalContestVendors\TotalCore\PostTypes\PostType as AbstractPostType;

/**
 * Submission post type.
 * @package TotalContest\Submission
 * @since   1.0.0
 */
class PostType extends AbstractPostType {
	/**
	 * Submission post type constructor.
	 */
	public function __construct() {
		parent::__construct();
		// Hook into WordPress to add some rewrite rules.
		add_action( 'init', [ $this, 'rewriteRules' ], 99 );
		// Hook into WordPress to support custom post type URL.
		add_filter( 'post_type_link', [ $this, 'postTypeLink' ], 10, 2 );
		add_filter( 'user_has_cap', [ $this, 'userHasCapability' ], 10, 4 );
		// Capabilities
		add_action( 'totalcontest/actions/activated', [ $this, 'capabilities' ] );
	}

	/**
	 * Submissions rewrite rules.
	 * @since 1.0.0
	 */
	public function rewriteRules() {
		// Add a custom rewrite rule to support submissions under contest url.
		add_rewrite_rule( '^contest/(.*)/submission/([^/]+)/?$', 'index.php?post_type=' . $this->getName() . '&p=$matches[2]', 'top' );
	}

	/**
	 * Capabilities mapping.
	 */
	public function capabilities() {
		$map = [
			'edit_contest_submission'   => [ 'administrator', 'editor', 'author' ],
			'read_contest_submission'   => [ 'administrator', 'editor', 'author' ],
			'delete_contest_submission' => [ 'administrator', 'editor', 'author' ],

			'edit_contest_submissions'    => [ 'administrator', 'editor', 'author' ],
			'delete_contest_submissions'  => [ 'administrator', 'editor', 'author' ],
			'publish_contest_submissions' => [ 'administrator', 'editor', 'author' ],

			'edit_others_contest_submissions'   => [ 'administrator', 'editor' ],
			'delete_others_contest_submissions' => [ 'administrator', 'editor' ],

			'edit_published_contest_submissions'   => [ 'administrator', 'editor', 'author' ],
			'delete_published_contest_submissions' => [ 'administrator', 'editor', 'author' ],

			'read_private_contest_submissions'   => [ 'administrator', 'editor' ],
			'edit_private_contest_submissions'   => [ 'administrator', 'editor' ],
			'delete_private_contest_submissions' => [ 'administrator', 'editor' ],
			'create_contest_submissions'         => [ 'administrator', 'editor', 'author' ],
		];

		foreach ( $map as $capability => $roles ):
			foreach ( $roles as $role ):
				$role = get_role( $role );
				if ( $role ):
					$role->add_cap( $capability );
				endif;
			endforeach;
		endforeach;
	}

	public function userHasCapability( $allcaps, $caps, $args, $user ) {
		$capability = empty( $caps[0] ) ? $args[0] : $caps[0];
		$authorId   = empty( $args[1] ) ? null : $args[1];
		$postId     = empty( $args[2] ) ? null : $args[2];

		$post = $postId ? get_post( $postId ) : null;

		if ( $post && ( $post->post_type !== $this->getName() || $post->post_author === $authorId ) ):
			return $allcaps;
		endif;

		if ( $post ):
			if ( $capability === 'edit_others_contest_submissions' && current_user_can( 'edit_post', $post->post_parent ) ):
				$allcaps['edit_others_contest_submissions'] = true;
			endif;

			if ( $capability === 'delete_others_posts' && current_user_can( 'delete_post', $post->post_parent ) ):
				$allcaps['delete_others_posts'] = true;
			endif;

			if ( $capability === 'publish_contest_submissions' && current_user_can( 'publish_contest', $post->post_parent ) ):
				$allcaps['publish_contest_submissions'] = true;
			endif;
		endif;


		return $allcaps;
	}

	/**
	 * Build submission url.
	 *
	 * @param $link
	 * @param $post
	 *
	 * @return mixed
	 * @since 1.0.0
	 */
	public function postTypeLink( $link, $post ) {
		if ( $this->getName() === get_post_type( $post ) ):
			if ( $post->post_parent ) :

				$parent = get_post( $post->post_parent );
				if ( ! empty( $parent->post_name ) && strpos( $link, '%post_id%' ) !== false ) :
					$link = substr( $link, 0, strpos( $link, '%post_id%' ) + 10 );

					return str_replace( [ '%contest%', '%post_id%' ], [ $parent->post_name, $post->ID ], $link );
				endif;

			endif;
		endif;

		return $link;
	}

	/**
	 * @return string
	 */
	public function getName() {
		/**
		 * Filters the name of submission CPT.
		 *
		 * @param string $name CPT name.
		 *
		 * @since 2.0.0
		 * @return string
		 */
		return apply_filters( 'totalcontest/filters/submission/cpt/name', 'contest_submission' );
	}

	/**
	 * Get CPT args.
	 *
	 * @return array
	 */
	public function getArguments() {
		/**
		 * Filters the arguments of submission CPT.
		 *
		 * @param array $args CPT arguments.
		 *
		 * @since 2.0.0
		 * @return array
		 */
		return apply_filters( 'totalcontest/filters/submission/cpt/args', [
				'labels'             => [
					'name'               => __( 'Submissions', 'totalcontest' ),
					'singular_name'      => __( 'Submission', 'totalcontest' ),
					'add_new'            => __( 'Create Submission', 'totalcontest' ),
					'add_new_item'       => __( 'New Submission', 'totalcontest' ),
					'edit_item'          => __( 'Edit Submission', 'totalcontest' ),
					'new_item'           => __( 'New Submission', 'totalcontest' ),
					'all_items'          => __( 'All submissions', 'totalcontest' ),
					'view_item'          => __( 'View Submission', 'totalcontest' ),
					'search_items'       => __( 'Search submissions', 'totalcontest' ),
					'not_found'          => __( 'No contests found', 'totalcontest' ),
					'not_found_in_trash' => __( 'No contests found in Trash', 'totalcontest' ),
					'menu_name'          => __( 'Submissions', 'totalcontest' ),
				],
				'public'             => true,
				'publicly_queryable' => true,
				'show_ui'            => true,
				'show_in_menu'       => false,
				'query_var'          => true,
				'rewrite'            => [
					'slug'       => _x( 'contest/%contest%/submission/%post_id%', 'slug', 'totalcontest' ),
					'with_front' => true,
					'feeds'      => false,
					'pages'      => false,
				],
				'capabilities'       => [
					'edit_post'              => 'edit_contest_submission',
					'read_post'              => 'read_contest_submission',
					'delete_post'            => 'delete_contest_submission',
					'edit_posts'             => 'edit_contest_submissions',
					'edit_others_posts'      => 'edit_others_contest_submissions',
					'publish_posts'          => 'publish_contest_submissions',
					'read_private_posts'     => 'read_private_contest_submissions',
					'create_posts'           => 'do_not_allow',
					'edit_published_posts'   => 'edit_published_contest_submissions',
					'delete_published_posts' => 'delete_published_contest_submissions',
				],
				'map_meta_cap'       => true,
				'has_archive'        => false,
				'hierarchical'       => false,
				'menu_position'      => null,
				'menu_icon'          => 'dashicons-external',
				'supports'           => [ 'title', 'thumbnail', 'revisions' ],
				'show_in_admin_bar'  => true,
				'show_in_rest'       => false,
			]
		);
	}

	/**
	 * Setup submission post type messages.
	 *
	 * @param \WP_Post $post
	 *
	 * @return array
	 * @since 1.0.0
	 */
	public function getMessages( $post ) {
		return [
			0  => '', // Unused. Messages start at index 1.
			1  => sprintf( __( 'Submission updated. <a href="%s">View submission</a>', 'totalcontest' ), esc_url( get_permalink( $post->ID ) ) ),
			2  => __( 'Custom field updated.', 'totalcontest' ),
			3  => __( 'Custom field deleted.', 'totalcontest' ),
			4  => __( 'Submission updated.', 'totalcontest' ),
			5  => isset( $_GET['revision'] ) ? sprintf( __( 'Submission restored to revision from %s', 'totalcontest' ), wp_post_revision_title( (int) $_GET['revision'], false ) ) : false,
			6  => sprintf( __( 'Submission published. <a href="%s">View submission</a>', 'totalcontest' ), esc_url( get_permalink( $post->ID ) ) ),
			7  => __( 'Submission saved.', 'totalcontest' ),
			8  => sprintf( __( 'Submission submitted. <a target="_blank" href="%s">Preview submission</a>', 'totalcontest' ), esc_url( add_query_arg( 'preview', 'true', get_permalink( $post->ID ) ) ) ),
			9  => sprintf( __( 'Submission scheduled for: <strong>%1$s</strong>. <a target="_blank" href="%2$s">Preview submission</a>', 'totalcontest' ), date_i18n( __( 'M j, Y @ G:i' ), strtotime( $post->post_date ) ), esc_url( get_permalink( $post->ID ) ) ),
			10 => sprintf( __( 'Submission draft updated. <a target="_blank" href="%s">Preview submission</a>', 'totalcontest' ), esc_url( add_query_arg( 'preview', 'true', get_permalink( $post->ID ) ) ) ),
		];
	}

	/**
	 * Register post type.
	 *
	 * @return \WP_Error|\WP_Post_Type WP_Post_Type on success, WP_Error otherwise.
	 * @since 1.0.0
	 */
	public function register() {
		define( 'TC_SUBMISSION_CPT_NAME', $this->getName() );

		return parent::register();
	}

}