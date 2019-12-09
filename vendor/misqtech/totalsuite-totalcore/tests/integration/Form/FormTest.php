<?php

namespace Form;

use TotalCore\Form\Fields\TextField;
use TotalCore\Form\Form;
use TotalCore\Form\Page;

class FormTest extends \Codeception\TestCase\WPTestCase {
	/**
	 * @var Form $form
	 */
	protected $form;
	/**
	 * @var Page $page
	 */
	protected $page;

	public function setUp() {
		parent::setUp();
		$this->page            = new Page();
		$this->form            = new Form();
		$this->form['page-id'] = $this->page;

		$field = new TextField();
		$field->setOptions( [
			'name'        => 'test',
			'validations' => [
				'filled' => [ 'enabled' => true ],
			],
		] );
		$this->page[] = $field;
	}

	public function testAddingPage() {
		static::assertCount( 1, $this->form );
		static::assertSame( $this->page, $this->form['page-id'] );
	}

	public function testValidateMethod() {
		static::assertFalse( $this->form->validate() );
	}

	public function testIsValidatedMethod() {
		$this->form->validate();
		static::assertTrue( $this->form->isValidated() );
	}

	public function testErrorsMethod() {
		$this->form->validate();
		static::assertNotEmpty( $this->form->errors() );
	}

	public function testToArrayMethod() {
		static::assertSame( [ 'test' => null ], $this->form->toArray() );
	}

	public function testRenderMethod() {
		static::assertSame(
			'<form action="" enctype="multipart/form-data" class="-form" method="POST"><div class="-form-page"><div class="-form-field -form-field-type-text -column-full"><label for="" class="-form-field-label"></label><input name="test" value="" type="text" class="-form-field-input"><div class="-form-field-errors"></div></div></div><button type="submit" class="-button --primary">Submit</button></form>',
			$this->form->render()
		);
	}

	public function testGetFormElementMethod() {
		static::assertInstanceOf( '\TotalCore\Helpers\Html', $this->form->getFormElement() );
		static::assertSame( 'form', $this->form->getFormElement()->getTag() );
	}

	public function testGetSubmitButtonElementMethod() {
		static::assertInstanceOf( '\TotalCore\Helpers\Html', $this->form->getSubmitButtonElement() );
		static::assertSame( 'button', $this->form->getSubmitButtonElement()->getTag() );
		static::assertSame( 'submit', $this->form->getSubmitButtonElement()->getAttribute( 'type' ) );
	}
}