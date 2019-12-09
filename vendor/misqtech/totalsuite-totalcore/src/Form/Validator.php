<?php

namespace TotalContestVendors\TotalCore\Form;


use TotalContestVendors\TotalCore\Contracts\Http\File as FileContract;

/**
 * Class Validator
 * @package TotalContestVendors\TotalCore\Form
 */
class Validator {

	/**
	 * @param Field $field
	 * @param array $args
	 *
	 * @return bool|string
	 */
	public static function isFilled( Field $field, $args = [] ) {
		$value        = $field->getValue();
		$errorMessage = __( '{{label}} must be filled.', \TotalContestVendors\TotalCore\Application::getInstance()->env( 'slug' ) );

		if ( $value === null ):
			return $errorMessage;
		elseif ( is_string( $value ) && trim( $value ) === '' ):
			return $errorMessage;
		elseif ( ( is_array( $value ) || $value instanceof \Countable ) && count( $value ) < 1 ):
			return $errorMessage;
		endif;

		return true;
	}

	/**
	 * @param Field $field
	 * @param array $args
	 *
	 * @return bool|string
	 */
	public static function isEmail( Field $field, $args = [] ) {
		$email = trim( $field->getValue() );
		if ( ! empty( $email ) ):
			return filter_var( $email, FILTER_VALIDATE_EMAIL ) ? true : __( '{{label}} must be a valid email address.', \TotalContestVendors\TotalCore\Application::getInstance()->env( 'slug' ) );
		endif;

		return true;
	}

	/**
	 * @param Field $field
	 * @param array $args
	 *
	 * @return bool|string
	 */
	public static function isUrl( Field $field, $args = [] ) {
		$url = trim( $field->getValue() );
		if ( ! empty( $url ) ):
			return filter_var( $url, FILTER_VALIDATE_URL ) ? true : __( '{{label}} must be a valid URL.', \TotalContestVendors\TotalCore\Application::getInstance()->env( 'slug' ) );
		endif;

		return true;
	}

	/**
	 * @param Field $field
	 * @param array $args
	 *
	 * @return bool|string
	 */
	public static function isNumber( Field $field, $args = [] ) {
		$number = trim( $field->getValue() );
		if ( $number !== '' ):
			filter_var( $number, FILTER_VALIDATE_INT ) ? true : __( '{{label}} is not a number.', \TotalContestVendors\TotalCore\Application::getInstance()->env( 'slug' ) );
		endif;

		return true;
	}

	/**
	 * @param Field $field
	 * @param array $args
	 *
	 * @return string
	 */
	public static function isIn( Field $field, $args = [] ) {
		$values = (array) $args['values'] ?: [];

		return in_array( $field->getValue(), $values ) ?: __( '{{label}} is not within the supported range.', \TotalContestVendors\TotalCore\Application::getInstance()->env( 'slug' ) );
	}

	/**
	 * @param Field $field
	 * @param array $args
	 *
	 * @return bool|string
	 */
	public static function isRegex( Field $field, $args = [] ) {
		$modifiers    = empty( $args['modifiers'] ) ? [] : array_filter( (array) $args['modifiers'] );
		$mustMatch    = empty( $args['type'] ) || $args['type'] === 'match';
		$match        = @preg_match( '/' . $args['pattern'] . '/' . implode( '', array_keys( $modifiers ) ), $field->getValue() );
		$result       = ( $match && $mustMatch ) || ( ! $match && ! $mustMatch );
		$errorMessage = ! empty( $args['errorMessage'] ) ? $args['errorMessage'] : __( '{{label}} does not allow this value.', \TotalContestVendors\TotalCore\Application::getInstance()->env( 'slug' ) );

		return $result ? true : $errorMessage;
	}

	/**
	 * @param Field $field
	 * @param array $args
	 *
	 * @return bool|string
	 */
	public static function isFilter( Field $field, $args = [] ) {
		$value = $field->getValue();
		$rules = isset( $args['rules'] ) ? (array) $args['rules'] : [];
		if ( ! empty( $value ) ):
			foreach ( $rules as $rule ):
				$ruleValue = str_replace( ' ', '', $rule['value'] );
				$regexp    = str_replace( '\*', '.+', preg_quote( $ruleValue, '/' ) );
				$result    = (bool) preg_match( "/{$regexp}/i", $value );

				if ( ( $result && $rule['type'] === 'deny' ) || ( ! $result && $rule['type'] === 'allow' ) ):
					return __( '{{label}} is not accepted.', \TotalContestVendors\TotalCore\Application::getInstance()->env( 'slug' ) );
				elseif ( $result && $rule['type'] === 'allow' ):
					return true;
				endif;
			endforeach;
		endif;

		return true;
	}

	/**
	 * @param Field $field
	 * @param array $args
	 *
	 * @return bool|string
	 */
	public static function isArray( Field $field, $args = [] ) {
		return is_array( $field->getValue() ) ? true : __( '{{label}} is not in an array format.', \TotalContestVendors\TotalCore\Application::getInstance()->env( 'slug' ) );
	}

	/**
	 * @param Field $field
	 * @param array $args
	 *
	 * @return bool|string
	 */
	public static function isString( Field $field, $args = [] ) {
		return is_string( $field->getValue() ) ? true : __( '{{label}} is not a string.', \TotalContestVendors\TotalCore\Application::getInstance()->env( 'slug' ) );
	}

	/**
	 * @param Field $field
	 * @param array $args
	 *
	 * @return bool|string
	 */
	public static function isSize( Field $field, $args = [] ) {
		$value = $field->getValue();
		$min   = empty( $args['min'] ) ? 0 : (int) $args['min'];
		$max   = empty( $args['max'] ) ? 0 : (int) $args['max'];

		if ( $value instanceof FileContract ):
			$min *= 1024; // Bytes
			$max *= 1024; // Bytes

			if ( $min && $value->getSize() < $min ) :
				$minSizeFormatted = size_format( $min );

				return sprintf( __( '{{label}} file size must be at least %s.', \TotalContestVendors\TotalCore\Application::getInstance()->env( 'slug' ) ), $minSizeFormatted );
			elseif ( $max && $value->getSize() > $max ):
				$maxSizeFormatted = size_format( $max );

				return sprintf( __( '{{label}} file size must be less than %s.', \TotalContestVendors\TotalCore\Application::getInstance()->env( 'slug' ) ), $maxSizeFormatted );
			endif;
		elseif ( is_string( $value ) ):
			if ( $min && mb_strlen( $value ) < $min ) :
				return sprintf( __( '{{label}} must be at least %d characters.', \TotalContestVendors\TotalCore\Application::getInstance()->env( 'slug' ) ), $min );
			elseif ( $max && mb_strlen( $value ) > $max ):
				return sprintf( __( '{{label}} must be less than %d characters.', \TotalContestVendors\TotalCore\Application::getInstance()->env( 'slug' ) ), $max );
			endif;
		endif;

		return true;
	}

	/**
	 * @param Field $field
	 * @param array $args
	 *
	 * @return bool|string
	 */
	public static function isFormats( Field $field, $args = [] ) {
		$file = $field->getValue();

		if ( ! empty( $args['extensions'] ) && $file instanceof FileContract ):
			if ( ! is_array( $args['extensions'] ) ):
				$args['extensions'] = array_flip( preg_split( '/(\s*(\||\,|\-)\s*)/m', trim( $args['extensions'] ) ) );
			endif;

			$allowedExtensions = array_keys( array_filter( $args['extensions'] ) );
			$extension         = $file->getExtension();

			if ( ! in_array( $extension, $allowedExtensions ) ):
				$allowedExtensions = implode( ', ', $allowedExtensions );

				return sprintf( __( 'Only files with these extensions are allowed: %s.', \TotalContestVendors\TotalCore\Application::getInstance()->env( 'slug' ) ), $allowedExtensions );
			endif;

		endif;

		return true;
	}

	/**
	 * @param Field $field
	 * @param array $args
	 *
	 * @return bool|string
	 */
	public static function isFileType( Field $field, $args = [] ) {
		$file = $field->getValue();
		if ( $file instanceof FileContract ):
			$allowedType = trim( $args['type'] );
			$fileType    = explode( '/', $file->getMimeType(), 2 )[0];

			if ( $fileType !== $allowedType ):
				return sprintf( __( 'Only %s files are accepted.', \TotalContestVendors\TotalCore\Application::getInstance()->env( 'slug' ) ), $allowedType );
			endif;

		endif;

		return true;
	}

	/**
	 * @param Field $field
	 * @param array $args
	 *
	 * @return bool|string
	 */
	public static function isFile( Field $field, $args = [] ) {
		$file = $field->getValue();
		if ( ! empty( $file ) && ! ( $file instanceof FileContract ) ):
			return sprintf( __( 'You must upload a file.', \TotalContestVendors\TotalCore\Application::getInstance()->env( 'slug' ) ) );
		endif;

		return true;
	}

	/**
	 * @param Field $field
	 * @param array $args
	 *
	 * @return bool|string
	 */
	public static function isLength( Field $field, $args = [] ) {
		$file = $field->getValue();

		if ( $file instanceof FileContract ):
			$id3File    = \TotalContestVendors\TotalCore\Application::get( 'id3' )->analyze( $file->getPathName() );
			$fileLength = isset( $id3File['playtime_seconds'] ) ? (int) round( $id3File['playtime_seconds'] ) : false;

			// Accept if type is not supported by ID3
			if ( $fileLength === false ):
				return true;
			endif;

			// min length check
			if ( isset( $args['min'] ) && $fileLength < $args['min'] ):
				return sprintf( __( 'Minimum length for files is: %s seconds.', \TotalContestVendors\TotalCore\Application::getInstance()->env( 'slug' ) ), $args['min'] );
			endif;
			// min length check
			if ( isset( $args['max'] ) && $fileLength > $args['max'] ):
				return sprintf( __( 'Maximum length for files is: %s seconds.', \TotalContestVendors\TotalCore\Application::getInstance()->env( 'slug' ) ), $args['max'] );
			endif;
		endif;

		return true;
	}

	/**
	 * @param Field $field
	 * @param array $args
	 *
	 * @return bool|string
	 */
	public static function isDimensions( Field $field, $args = [] ) {
		$file = $field->getValue();
		if ( $file instanceof FileContract ):
			$dimensions = getimagesize( $file->getPathName() );
			$width      = $dimensions[0];
			$height     = $dimensions[1];

			// minWidth check
			if ( isset( $args['minWidth'] ) && $width < $args['minWidth'] ):
				return sprintf( __( 'Minimum width for images is: %s.', \TotalContestVendors\TotalCore\Application::getInstance()->env( 'slug' ) ), $args['minWidth'] );
			endif;
			// minHeight check
			if ( isset( $args['minHeight'] ) && $height < $args['minHeight'] ):
				return sprintf( __( 'Minimum height for images is: %spx.', \TotalContestVendors\TotalCore\Application::getInstance()->env( 'slug' ) ), $args['minHeight'] );
			endif;
			// maxWidth check
			if ( isset( $args['maxWidth'] ) && $width > $args['maxWidth'] ):
				return sprintf( __( 'Maximum width for images is: %spx.', \TotalContestVendors\TotalCore\Application::getInstance()->env( 'slug' ) ), $args['maxWidth'] );
			endif;
			// maxHeight check
			if ( isset( $args['maxHeight'] ) && $height > $args['maxHeight'] ):
				return sprintf( __( 'Maximum height for images is: %spx.', \TotalContestVendors\TotalCore\Application::getInstance()->env( 'slug' ) ), $args['maxHeight'] );
			endif;
		endif;

		return true;
	}

	/**
	 * @param Field $field
	 * @param array $args
	 *
	 * @return bool|string
	 */
	public static function isUploadedVia( Field $field, $args = [] ) {
		$url = trim( $field->getValue() );
		if ( ! empty( $url ) ):
			$acceptedServices = array_keys( $args['services'] );
			$serviceName      = \TotalContestVendors\TotalCore\Application::get( 'embed' )->getProviderName( $url );

			if ( ! in_array( $serviceName, $acceptedServices ) ):
				return sprintf( __( 'Only links from these services are accepted: %s.', \TotalContestVendors\TotalCore\Application::getInstance()->env( 'slug' ) ), implode( ', ', array_map( 'ucfirst', $acceptedServices ) ) );
			endif;
		endif;

		return true;
	}

}