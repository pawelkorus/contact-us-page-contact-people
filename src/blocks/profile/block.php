<?php
/**
 * Blocks Initializer
 *
 * Enqueue CSS/JS of all the blocks.
 *
 * @since   1.0.0
 * @package CGB
 */

namespace A3Rev\ContactPeople\Blocks;

// Exit if accessed directly.
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

class Profile {

	public function __construct() {
		add_action( 'init', array( $this, 'register_block' ) );	
	}

	public function register_block() {

		if ( ! function_exists( 'register_block_type' ) ) {
			// Gutenberg is not active.
			return;
		}

		// Create Dynamic Block via PHP render callback
		$block_args = array(
			'attributes'      => array(
				'blockID' => array(
					'type' => 'string'
				),
				'contactID' => array(
					'type'    => 'string',
					'default' => '',
				),
				'align'	=> array(
					'type'    => 'string',
					'default' => 'none',
				),
				'alignWrap'	=> array(
					'type'    => 'boolean',
					'default' => false,
				),
				'width'	=> array(
					'type'    => 'number',
					'default' => 300,
				),
				'widthUnit'	=> array(
					'type'    => 'string',
					'default' => 'px',
				),
				'paddingLeft' => array(
					'type' => 'string'
				),
				'paddingTop' => array(
					'type' => 'string'
				),
				'paddingRight' => array(
					'type' => 'string'
				),
				'paddingBottom' => array(
					'type' => 'string'
				),
				'paddingUnit' => array(
					'type' => 'string',
					'default' => 'px'
				),
				'paddingSync' => array(
					'type'    => 'boolean',
					'default' => false,
				),
				'marginLeft' => array(
					'type' => 'string'
				),
				'marginTop' => array(
					'type' => 'string'
				),
				'marginRight' => array(
					'type' => 'string'
				),
				'marginBottom' => array(
					'type' => 'string'
				),
				'marginUnit' => array(
					'type' => 'string',
					'default' => 'px'
				),
				'marginSync' => array(
					'type'    => 'boolean',
					'default' => false,
				),
	
			),
			'render_callback' 	=> array( $this, 'render' )
		);

		register_block_type( 'contact-people/profile', $block_args );
	}

	public function render( $attributes ) {

		extract( $attributes );

		if ( ! isset( $contactID ) ) {
			return '';
		}

		global $people_contact;

		if ( ! isset( $align ) ) {
			$align = 'none';
		}

		if ( in_array( $align, array( 'left', 'right' ) ) ) {
			if ( ! isset( $alignWrap ) ) {
				$alignWrap = false;
			}
		}

		// Validate Max Width
        if ( ! isset( $widthUnit ) ) {
            $widthUnit = 'px';
        }
        if ( ! isset( $width ) ) {
            $width = 300;
        }
		if ( 'px' === $widthUnit && $width < 300 ) {
			$width = 300;
		} else if ( 'px' !== $widthUnit && $width > 100 ) {
			$width = 100;
		}

		// Validate Padding Unit
		if ( ! isset( $paddingUnit ) ) {
            $paddingUnit = 'px';
        }

		$style = '';

		if ( 'center' === $align ) $style .= 'float:none;margin:auto;display:block;';
		else $style .= 'float:'.$align.';';

		$style .= 'width:100%;max-width:'.$width.$widthUnit.';';

		if ( isset( $paddingLeft ) && ! empty( $paddingLeft ) ) {
			$style .= 'padding-left:'.$paddingLeft.$paddingUnit.';';
		}
		if ( isset( $paddingTop ) && ! empty( $paddingTop ) ) {
			$style .= 'padding-top:'.$paddingTop.$paddingUnit.';';
		}
		if ( isset( $paddingRight ) && ! empty( $paddingRight ) ) {
			$style .= 'padding-right:'.$paddingRight.$paddingUnit.';';
		}
		if ( isset( $paddingBottom ) && ! empty( $paddingBottom ) ) {
			$style .= 'padding-bottom:'.$paddingBottom.$paddingUnit.';';
		}

		ob_start();
		echo $people_contact->create_people_contact( $contactID, $style, $alignWrap );
		$output = ob_get_clean();

		return $output;
	}
}
