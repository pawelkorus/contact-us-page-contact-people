/**
 * BLOCK: Profile
 *
 * Registering a basic block with Gutenberg.
 * Simple block, renders and saves the same content without any interactivity.
 */

/**
 * Internal dependencies
 */
import shorthash from 'shorthash';

import BlockEdit, { cardSpacingAttributes } from './edit';

// icons
import IconContact from './../../assets/icons/icon.svg';

import ProfileAttributes from './attributes';

const { __ } = wp.i18n; // Import __() from wp.i18n
const { registerBlockType } = wp.blocks;

const { Fragment } = wp.element;

/**
 * List uniqueID of this block is using on a post to see if the block needs to generate a new ID.
 */
const listUniqueIDs = [];

/**
 * Register: a3 Gutenberg Block.
 *
 * Registers a new block provided a unique name and an object defining its
 * behavior. Once registered, the block is made editor as an option to any
 * editor interface where blocks are implemented.
 *
 * @param  {string}   name     Block name.
 * @param  {Object}   settings Block settings.
 * @return {?WPBlock}          The block, if it has been successfully
 *                             registered; otherwise `undefined`.
 */
registerBlockType( 'contact-people/profile', {
	// Block name. Block names must be string that contains a namespace prefix. Example: my-plugin/my-custom-block.
	title: __( 'Contact Profile' ), // Block title.
	icon: {
		src: IconContact,
		foreground: '#24b6f1',
	}, // Block icon from Dashicons → https://developer.wordpress.org/resource/dashicons/.
	category: 'a3rev-blocks', // Block category — Group blocks together based on common traits E.g. common, formatting, layout widgets, embed.
	keywords: [ __( 'Contact People' ), __( 'Contact Profile' ), __( 'Single Profile' ), __( 'a3rev' ) ],

	attributes: {
		...ProfileAttributes,
		...cardSpacingAttributes,
	},

	supports: {
		customClassName: false,
		className: false,
	},

	// The "edit" property must be a valid function.
	edit( props ) {
		const { attributes, setAttributes } = props;

		const { blockID = shorthash.unique( props.clientId ) } = attributes;

		if ( ! attributes || typeof attributes.blockID === 'undefined' ) {
			setAttributes( { blockID: blockID } );
			listUniqueIDs.push( blockID );
		} else if ( listUniqueIDs.includes( blockID ) ) {
			const newBlockID = shorthash.unique( props.clientId );
			setAttributes( { blockID: newBlockID } );
			listUniqueIDs.push( newBlockID );
		} else {
			listUniqueIDs.push( blockID );
		}

		return (
			<Fragment>
				<BlockEdit { ...props } />
			</Fragment>
		);
	},

	// The "save" property must be specified and must be a valid function.
	save() {
		// Rendering in PHP
		return null;
	},
} );
