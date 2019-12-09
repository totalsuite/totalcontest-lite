<?php

namespace TotalContest\Render;

use TotalContest\Contracts\Contest\Model;
use TotalContest\Contracts\Submission\Model as SubmissionModel;
use TotalContest\Modules\Template;
use TotalContestVendors\TotalCore\Contracts\Foundation\Environment;
use TotalContestVendors\TotalCore\Contracts\Modules\Repository as ModulesRepository;
use TotalContestVendors\TotalCore\Helpers\Misc;
use TotalContestVendors\TotalCore\Helpers\Strings;

/**
 * Class Renderer
 * @package TotalContest\Render
 */
class Renderer {
	/**
	 * @var Model
	 */
	protected $contest;
	/**
	 * @var \TotalContest\Submission\Model
	 */
	protected $submission;
	/**
	 * @var ModulesRepository
	 */
	protected $modulesRepository;
	/**
	 * @var Template
	 */
	protected $templateInstance;
	/**
	 * @var Environment
	 */
	protected $env;
	/**
	 * @var \WP_Filesystem_Base
	 */
	protected $filesystem;

	public function __construct( ModulesRepository $modulesRepository, \WP_Filesystem_Base $filesystem, Environment $env ) {
		$this->modulesRepository = $modulesRepository;
		$this->env               = $env;
		$this->filesystem        = $filesystem;
	}

	/**
	 * Render shortcut.
	 *
	 * @return string
	 */
	public function __toString() {
		return (string) $this->render();
	}

	/**
	 * @param $templateId
	 *
	 * @return Template|\TotalContest\Modules\Templates\Basic\Template
	 */
	public function loadTemplate( $templateId ) {
		if ( $this->templateInstance === null ):

			// Theme template
			$themeTemplateFile = get_template_directory() . '/totalcontest/Template.php';
			if ( file_exists( $themeTemplateFile ) ):
				include_once $themeTemplateFile;

				$themeTemplateClass = '\\TotalContest\\Modules\\Templates\\ThemeTemplate\\Template';
				if ( class_exists( $themeTemplateClass ) ):
					$this->templateInstance = new $themeTemplateClass;
				endif;
			// Regular template
			else:
				$module = $this->modulesRepository->get( $templateId );

				if ( $module && class_exists( $module['class'] ) ):
					$this->templateInstance = new $module['class'];
				else:
					$this->templateInstance = new \TotalContest\Modules\Templates\Basic\Template();
				endif;
			endif;
		endif;

		return $this->templateInstance;
	}


	/**
	 * @return string
	 */
	public function render() {
		TotalContest( 'utils.purge.cache' );

		$template = $this->loadTemplate( $this->contest->getTemplateId() );
		/**
		 * Filters the template used for contest rendering.
		 *
		 * @param Template        $template   Template object.
		 * @param Model           $contest    Contest model object.
		 * @param SubmissionModel $submission Submission model object.
		 * @param Renderer        $render     Renderer object.
		 *
		 * @since 2.0.0
		 * @return Template
		 */
		$template = apply_filters( 'totalcontest/filters/render/template', $template, $this->contest, $this->submission, $this );

		$screen       = $this->submission ? $this->submission->getScreen() : $this->contest->getScreen();
		$templateVars = [ 'contest' => $this->contest, 'submission' => $this->submission, 'screen' => $screen, 'template' => $template ];

		if ( $screen === 'contest.content' ):
			$customPage = $this->contest->getCustomPage();

			if ( ! empty( $customPage['content'] ) ):
				$customPage['content'] = wpautop( do_shortcode( $customPage['content'] ) );
			endif;

			$templateVars['customPage'] = $customPage;
		endif;

		if ( $screen === 'contest.landing' ):
			$templateVars['content'] = $this->contest->getSettingsItem( 'pages.landing.content' );

			if ( ! empty( $templateVars['content'] ) ):
				$templateVars['content'] = wpautop( do_shortcode( $templateVars['content'] ) );
			endif;
		endif;

		if ( $screen === 'contest.thankyou' ):
			$templateVars['content'] = $this->contest->getSettingsItem( 'pages.thankyou.submission.content' );

			if ( ! empty( $templateVars['content'] ) ):
				$templateVars['content'] = wpautop( do_shortcode( $templateVars['content'] ) );
			endif;
		endif;

		if ( $screen === 'contest.participate' ):
			$templateVars['form'] = $this->contest->getForm();
			! defined( 'DONOTCACHEPAGE' ) && define( 'DONOTCACHEPAGE', true );
		endif;

		if ( $screen === 'submission.thankyou' ):
			$templateVars['voteCasted'] = true;
			$templateVars['content']    = $this->contest->getSettingsItem( 'pages.thankyou.voting.content' );

			if ( ! empty( $templateVars['content'] ) ):
				$templateVars['content'] = wpautop( do_shortcode( $templateVars['content'] ) );
			endif;

			$screen = 'submission.view';
		endif;

		if ( $screen === 'submission.view' ):
			if ( ! $this->submission->hasVoted() && $this->submission->isAcceptingVotes() ):
				$templateVars['form'] = $this->submission->getForm();
			endif;
			! defined( 'DONOTCACHEPAGE' ) && define( 'DONOTCACHEPAGE', true );
		endif;

		/**
		 * Filters the contest screen when rendering.
		 *
		 * @param string          $screen     Contest screen name.
		 * @param Model           $contest    Contest model object.
		 * @param SubmissionModel $submission Submission model object.
		 * @param Renderer        $render     Renderer object.
		 *
		 * @since 2.0.0
		 * @return string
		 */
		$screen = apply_filters( 'totalcontest/filters/render/screen', $screen, $this->contest, $this->submission, $this );

		$templateVars['screen'] = $screen;

		/**
		 * Filters template variables passed to views.
		 *
		 * @param array           $templateVars Template variables.
		 * @param Model           $contest      Contest model object.
		 * @param SubmissionModel $submission   Submission model object.
		 * @param Renderer        $render       Renderer object.
		 *
		 * @since 2.0.0
		 * @return array
		 */
		$templateVars = apply_filters( 'totalcontest/filters/render/vars', $templateVars, $this->contest, $this );

		$cssClasses   = [];
		$cssClasses[] = is_rtl() ? 'is-rtl' : 'is-ltr';

		if ( function_exists( 'is_embed' ) && is_embed() ):
			$cssClasses[] = 'is-embed';
		endif;

		if ( is_preview() ):
			$cssClasses[] = 'is-preview';
		endif;

		if ( is_user_logged_in() ):
			$cssClasses[] = 'is-logged-in';
		endif;

		/**
		 * Filters css classes of contest container.
		 *
		 * @param array           $cssClasses Css classes.
		 * @param Model           $contest    Contest model object.
		 * @param SubmissionModel $submission Submission model object.
		 * @param Renderer        $render     Renderer object.
		 *
		 * @since 2.0.0
		 * @return array
		 */
		$cssClasses = apply_filters( 'totalcontest/filters/render/classes', $cssClasses, $this->contest, $this );


		/**
		 * Filters template markup
		 *
		 * @param string   $view    View.
		 * @param Model    $contest Contest model object.
		 * @param Renderer $render  Renderer object.
		 *
		 * @since 2.0.0
		 * @return string
		 */
		$view = apply_filters( 'totalcontest/filters/render/view', $template->getView( $screen, $templateVars ), $this->contest, $this );

		/**
		 * Filters template markup
		 *
		 * @param string          $markup     Contest wrapper markup.
		 * @param Model           $contest    Contest model object.
		 * @param SubmissionModel $submission Submission model object.
		 * @param Renderer        $render     Renderer object.
		 *
		 * @since 2.0.0
		 * @return string
		 */
		$markup = apply_filters(
			'totalcontest/filters/render/markup',
			'<div id="totalcontest" class="totalcontest-wrapper totalcontest-uid-{{uid}} {{container.classes}}" totalcontest="{{contest.id}}" totalcontest-uid="{{uid||\'none\'}}" totalcontest-screen="{{contest.screen}}" totalcontest-ajax-url="{{ajax}}">{{before}}{{config}}{{css}}{{js}}<div id="totalcontest-contest-{{contest.id}}" class="totalcontest-container">{{view}}</div>{{after}}</div>',
			$this->contest,
			$this->submission,
			$this
		);

		$rendered = Strings::template( $markup,
			apply_filters(
				'totalcontest/filters/render/vars',
				[
					'container'  => [
						'classes' => implode( ' ', $cssClasses ),
					],
					'uid'        => $this->contest->getPresetUid(),
					'contest'    => [
						'id'     => $this->contest->getId(),
						'screen' => $screen,
					],
					'submission' => [
						'id'     => $this->submission ? $this->submission->getId() : null,
						'screen' => $this->submission ? $this->submission->getScreen() : null,
					],
					'css'        => $this->getCss(),
					'js'         => $this->getJs(),
					'config'     => $this->getConfig(),
					'view'       => $view,
					'ajax'       => $this->contest->getAjaxUrl(),
					'before'     => '',
					'after'      => '',
				],
				$this->contest,
				$this->submission,
				$screen,
				$this
			)
		);

		/**
		 * Filters the rendered output.
		 *
		 * @param string          $rendered   Rendered contest.
		 * @param string          $screen     Current screen.
		 * @param Model           $contest    Contest model object.
		 * @param SubmissionModel $submission Submission model object.
		 * @param Renderer        $render     Renderer object.
		 *
		 * @since 2.0.0
		 * @return string
		 */
		return apply_filters( 'totalcontest/filters/render/output', $rendered, $screen, $this->contest, $this->submission, $this );
	}

	/**
	 * @return string
	 */
	public function getConfig() {
		$config = [
			'ajaxEndpoint' => add_query_arg( [ 'action' => 'totalcontest' ], admin_url( 'admin-ajax.php' ) ),
			'behaviours'   => $this->contest->getSettingsItem( 'design.behaviours', [] ) + [ 'async' => ! Misc::isDoingAjax() && defined( 'TC_ASYNC' ) && TC_ASYNC ],
			'effects'      => $this->contest->getSettingsItem( 'design.effects', [] ),
		];

		/**
		 * Filters contest config that will passed to frontend controller.
		 *
		 * @param array           $config     Config variables.
		 * @param Model           $contest    Contest model object.
		 * @param SubmissionModel $submission Submission model object.
		 * @param Renderer        $render     Renderer object.
		 *
		 * @since 2.0.0
		 * @return array
		 */
		$config = apply_filters( 'totalcontest/filters/render/config', $config, $this->contest, $this->submission, $this );

		return sprintf( '<script type="text/totalcontest-config" totalcontest-config="%1$d">%2$s</script>', $this->contest->getId(), json_encode( $config ) );
	}

	/**
	 * Get JS.
	 */
	public function getJs() {
		wp_enqueue_script( 'jquery-validation', $this->env['url'] . 'assets/dist/scripts/vendor/jquery.validate.min.js', [ 'jquery' ], ( Misc::isDevelopmentMode() ? time() : $this->env['version'] ) );
		wp_enqueue_script( 'totalcontest-frontend', $this->env['url'] . 'assets/dist/scripts/frontend.js', [ 'jquery-validation' ], ( Misc::isDevelopmentMode() ? time() : $this->env['version'] ) );
		wp_localize_script( 'jquery-validation', 'jqValidationMessages', [
			'required'    => __( '{{label}} must be filled.', 'totalcontest' ),
			'email'       => __( '{{label}} must be a valid email address.', 'totalcontest' ),
			'url'         => __( '{{label}} must be a valid URL.', 'totalcontest' ),
			'number'      => __( '{{label}} must be a number.', 'totalcontest' ),
			'maxlength'   => __( '{{label}} must be less than %d characters.', 'totalcontest' ),
			'minlength'   => __( '{{label}} must be at least %d characters.', 'totalcontest' ),
			'maxfilesize' => __( '{{label}} file size must be less than %s.', 'totalcontest' ),
			'minfilesize' => __( '{{label}} file size must be at least %s.', 'totalcontest' ),
			'formats'     => __( 'Only files with these extensions are allowed: %s.', 'totalcontest' ),
			'left'        => __( '%d Characters left', 'totalcontest' ),
		] );

		/**
		 * Filters contest JS.
		 *
		 * @param string          $js         JS code.
		 * @param Model           $contest    Contest model object.
		 * @param SubmissionModel $submission Submission model object.
		 * @param Renderer        $render     Renderer object.
		 *
		 * @since 2.0.0
		 * @return string
		 */
		return apply_filters( 'totalcontest/filters/render/js', '', $this->contest, $this->submission, $this );
	}

	/**
	 * Get CSS.
	 *
	 * @return string
	 */
	public function getCss() {
		$cachedCssFile = "css/{$this->contest->getPresetUid()}.css";
		wp_enqueue_style( 'totalcontest-contest', "{$this->env['cache']['url']}{$cachedCssFile}" );

		ob_start();
		wp_print_styles( 'totalcontest-contest' );
		$css = ob_get_clean();

		$inlineCss = TotalContest()->option( 'advanced.inlineCss' );
		if ( $inlineCss || Misc::isDevelopmentMode() || ! $this->filesystem->is_readable( $this->env['cache']['path'] . $cachedCssFile ) ):
			TotalContest( 'utils.create.cache' );
			$compileArgs = $this->contest->getSettingsItem( 'design' ) + [ 'uid' => $this->contest->getPresetUid() ];

			/**
			 * Filters the arguments passed for CSS compiling.
			 *
			 * @param array    $args     Arguments.
			 * @param Renderer $renderer Renderer.
			 * @param Model    $contest  Contest model.
			 *
			 * @since 2.0.0
			 * @return array
			 */
			$compileArgs = apply_filters( 'totalcontest/filters/render/css-args', $compileArgs, $this, $this->contest );
			$compiledCss = $this->templateInstance->getCompiledCss( $compileArgs ) . $this->contest->getSettingsItem( 'design.css' );

			if ( ! $inlineCss && $this->filesystem->is_writable( "{$this->env['cache']['path']}css/" ) ):
				$this->filesystem->put_contents( "{$this->env['cache']['path']}$cachedCssFile", $compiledCss );
			else:
				$css = "<style>{$compiledCss}</style>";
			endif;
		endif;

		/**
		 * Filters contest CSS.
		 *
		 * @param string          $css        CSS Code.
		 * @param Model           $contest    Contest model object.
		 * @param SubmissionModel $submission Submission model object.
		 * @param Renderer        $render     Renderer object.
		 *
		 * @since 2.0.0
		 * @return string
		 */
		$css = apply_filters( 'totalcontest/filters/render/css', $css, $this->contest, $this->submission, $this );

		return $css;
	}

	/**
	 * @return Model
	 */
	public function getContest() {
		return $this->contest;
	}

	/**
	 * @param Model $contest
	 */
	public function setContest( $contest ) {
		$this->contest = $contest;
	}

	/**
	 * @return \TotalContest\Submission\Model
	 */
	public function getSubmission() {
		return $this->submission;
	}

	/**
	 * @param \TotalContest\Submission\Model $submission
	 */
	public function setSubmission( $submission ) {
		$this->submission = $submission;
	}
}