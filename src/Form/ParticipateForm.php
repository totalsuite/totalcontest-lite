<?php

namespace TotalContest\Form;

use TotalContest\Contracts\Contest\Model as ContestModel;
use TotalContest\Contracts\Form\Factory as FormFactory;
use TotalContestVendors\TotalCore\Contracts\Http\Request;
use TotalContestVendors\TotalCore\Form\Field;
use TotalContestVendors\TotalCore\Form\Fields\FileField;
use TotalContestVendors\TotalCore\Form\Form;

/**
 * Class ParticipateForm
 * @package TotalContest\Form
 */
class ParticipateForm extends Form {
	protected $contest;
	protected $request;
	protected $formFactory;

	/**
	 * ParticipateForm constructor.
	 *
	 * @param             $contest
	 * @param Request     $request
	 * @param FormFactory $formFactory
	 */
	public function __construct( ContestModel $contest, Request $request, FormFactory $formFactory ) {
		parent::__construct();

		$this->contest     = $contest;
		$this->request     = $request;
		$this->formFactory = $formFactory;

		$hiddenFieldsPage = $this->formFactory->makePage();
		$customFieldsPage = $this->formFactory->makePage();

		$actionField = $this->formFactory->makeTextField();
		$actionField->setName( 'action' );
		$actionField->setOptions( [
			'type'  => 'hidden',
			'name'  => 'totalcontest[action]',
			'label' => false,
		] );
		$actionField->setValue( 'participate' );

		$contestIdField = $this->formFactory->makeTextField();
		$contestIdField->setName( 'contestId' );
		$contestIdField->setOptions( [
			'type'  => 'hidden',
			'name'  => 'totalcontest[contestId]',
			'label' => false,
		] );
		$contestIdField->setValue( $this->contest->getId() );

		$hiddenFieldsPage[] = $actionField;
		$hiddenFieldsPage[] = $contestIdField;

		$fields = $this->contest->getSettingsItem( 'contest.form.fields', [] );

		foreach ( $fields as $fieldSettings ):
			$factoryCallable = [ $this->formFactory, 'make' . ucfirst( $fieldSettings['type'] ) . 'Field' ];
			if ( is_callable( $factoryCallable ) ):
				$field = call_user_func( [ $this->formFactory, 'make' . ucfirst( $fieldSettings['type'] ) . 'Field' ] );
			else:
				$field = apply_filters( "totalcontest/filters/form/custom-field-type/{$fieldSettings['type']}", $fieldSettings, 'participate' );
			endif;

			$field = apply_filters( "totalcontest/filters/form/field/{$fieldSettings['type']}", $field, $fieldSettings, 'participate' );

			if ( ! $field instanceof Field ) :
				continue;
			endif;

			if ( ! empty( $fieldSettings['validations']['unique']['enabled'] ) ):
				$fieldSettings['validations']['unique']['contestId'] = $this->contest->getId();
				$fieldSettings['validations']['unique']['callback']  = TotalContest( 'validators.unique' );
			endif;

			$field->setName( $fieldSettings['name'] );
			$field->setOptions(
				[
					'id'          => "{$fieldSettings['name']}-field",
					'name'        => "totalcontest[{$fieldSettings['name']}]" . ( empty( $fieldSettings['attributes']['multiple'] ) ? '' : '[]' ),
					'default'     => empty( $fieldSettings['default'] ) ? null : $fieldSettings['default'],
					'placeholder' => isset( $fieldSettings['placeholder'] ) ? $fieldSettings['placeholder'] : '',
					'label'       => isset( $fieldSettings['label'] ) ? $fieldSettings['label'] : false,
					'validations' => isset( $fieldSettings['validations'] ) ? $fieldSettings['validations'] : [],
					'options'     => isset( $fieldSettings['options'] ) ? $fieldSettings['options'] : [],
					'attributes'  => isset( $fieldSettings['attributes'] ) ? $fieldSettings['attributes'] : [],
					'template'    => isset( $fieldSettings['template'] ) ? $fieldSettings['template'] : false,
				]
			);
			if ( $field instanceof FileField ):
				$field->setValue( $this->request->file( "totalcontest.{$fieldSettings['name']}", null ) );
			else:
				$field->setValue(
					$this->request->request(
						"totalcontest.{$fieldSettings['name']}",
						empty( $_POST ) ? null : ''
					)
				);
			endif;

			$customFieldsPage[] = $field;
		endforeach;

		// Captcha
		$captchaSettings = TotalContest()->option( 'services.recaptcha' );
		if ( ! empty( $captchaSettings['enabled'] ) && ! empty( $captchaSettings['key'] ) && ! empty( $captchaSettings['secret'] ) ):
			$captchaField = $this->formFactory->makeCaptchaField();
			$captchaField->setOptions(
				[
					'type'      => 'captcha',
					'name'      => 'captcha',
					'key'       => $captchaSettings['key'],
					'secret'    => $captchaSettings['secret'],
					'invisible' => ! empty( $captchaSettings['invisible'] ),
				]
			);
			$customFieldsPage[] = $captchaField;
		endif;

		$this->pages['hiddenFields'] = $hiddenFieldsPage;
		$this->pages['fields']       = $customFieldsPage;

		/**
		 * Filters the form pages.
		 *
		 * @param array                                              $pages   Form pages.
		 * @param \TotalContest\Contracts\Contest\Model              $contest Contest model object.
		 * @param string                                             $context Form context.
		 * @param \TotalContestVendors\TotalCore\Contracts\Form\Form $form    Form object.
		 *
		 * @since 2.0.0
		 * @return array
		 */
		$this->pages = apply_filters( 'totalcontest/filters/form/pages', $this->pages, $this->contest, 'participate', $this );
	}

	/**
	 * Open tag.
	 *
	 * @return string
	 */
	public function open() {
		return $this->getFormElement()
		            ->getOpenTag();
	}

	/**
	 * Close tag.
	 *
	 * @return string
	 */
	public function close() {
		return $this->getFormElement()->getCloseTag();
	}

	/**
	 * Hidden fields.
	 *
	 * @return mixed
	 */
	public function hiddenFields() {
		return $this->pages['hiddenFields']->render();
	}

	/**
	 * Fields.
	 *
	 * @return null
	 */
	public function fields() {
		return $this->pages['fields']->render();
	}

	/**
	 * Buttons.
	 *
	 * @return string
	 */
	public function buttons() {
		$buttons = [];

		$buttons[] = $this->getSubmitButtonElement();

		/**
		 * Filters the form buttons.
		 *
		 * @param array                                              $buttons Form buttons.
		 * @param \TotalContest\Contracts\Contest\Model              $contest Contest model object.
		 * @param \TotalContestVendors\TotalCore\Contracts\Form\Form $form    Form object.
		 *
		 * @since 4.0.0
		 * @return array
		 */
		$buttons = apply_filters( 'totalcontest/filters/form/buttons', $buttons, $this->contest, $this );

		return implode( '', $buttons );
	}

	/**
	 * @return \TotalContestVendors\TotalCore\Helpers\Html
	 */
	public function getFormElement() {
		$form = parent::getFormElement();
		$form->appendToAttribute( 'novalidate', 'novalidate' )
		     ->appendToAttribute( 'class', 'totalcontest-participate-form' );

		return $form;
	}

	/**
	 * @return \TotalContestVendors\TotalCore\Helpers\Html
	 */
	public function getSubmitButtonElement() {
		$submit = parent::getSubmitButtonElement();
		$submit->setAttribute( 'class', 'totalcontest-button totalcontest-button-primary totalcontest-button-submit' );
		$submit->setInner( __( 'Submit', 'totalcontest' ) );

		return $submit;
	}
}
