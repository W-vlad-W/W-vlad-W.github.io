/**
 * Retrieves the translation of text.
 *
 * @see https://developer.wordpress.org/block-editor/reference-guides/packages/packages-i18n/
 */
import { __ } from '@wordpress/i18n';
/**
 * React hook that is used to mark the block wrapper element.
 * It provides all the necessary props like the class name.
 *
 * @see https://developer.wordpress.org/block-editor/reference-guides/packages/packages-block-editor/#useblockprops
 */
import { useBlockProps } from '@wordpress/block-editor';


import { SelectControl, Disabled } from '@wordpress/components';
import ServerSideRender from '@wordpress/server-side-render';
import { useSelect } from '@wordpress/data';

/**
 * Lets webpack process CSS, SASS or SCSS files referenced in JavaScript files.
 * Those files can contain any CSS code that gets applied to the editor.
 *
 * @see https://www.npmjs.com/package/@wordpress/scripts#using-css
 */
import './editor.scss';

/**
 * The edit function describes the structure of your block in the context of the
 * editor. This represents what the editor will render when the block is used.
 *
 * @see https://developer.wordpress.org/block-editor/reference-guides/block-api/block-edit-save/#edit
 *
 * @return {Element} Element to render.
 */
const Edit = ( props ) => {
    const blockProps = useBlockProps();

    const onChangeModel = ( newModel ) => {
		props.setAttributes( { id: Number(newModel) } );			
	}


	const { pages } = useSelect( ( select ) => {
		const { getEntityRecords } = select( 'core' );

		// Query args
		const query = {
			status: 'publish',
			per_page: -1
		}

		return {
			pages: getEntityRecords( 'postType', 'product', query ),
		}
	} )
	//console.log(pages);
	// populate options for <SelectControl>
	let options = []
	if( pages ) {
		options.push( { value: 0, label: 'Select a model' } )
		pages.forEach( ( page ) => {
			options.push( { value : page.id, label : page.title.rendered } )
		})
	} else {
		options.push( { value: 0, label: 'Loading...' } )
	}
	
	console.log(props.attributes)

	return (
        <div {...blockProps}>
            <SelectControl
                label={__('WooCommerce AR Model', 'ar-for-woocommerce')}
                value={props.attributes.id}
                options={options}
                onChange={onChangeModel}
            />
            <Disabled>
                <ServerSideRender
                    block="ar-for-woocommerce/gutenberg-block"
                    attributes={props.attributes}
                />
            </Disabled>
        </div>
    );
    
};

export default Edit;
