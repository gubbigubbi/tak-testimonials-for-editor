//import { isUndefined, pickBy } from 'lodash';
const { pickBy, isUndefined } = lodash;

const { Component } = wp.element;
const { InspectorControls, BlockControls, BlockAlignmentToolbar } = wp.editor;
const { __ } = wp.i18n;
const { withSelect } = wp.data;
const { RangeControl, Spinner, QueryControls, ToggleControl } = wp.components;

import classnames from 'classnames';

class PostsSliderEdit extends Component {
	render() {
		const {
			attributes: {
				align,
				numberCols,
				postsToShow,
				startAt,
				order,
				orderBy,
				categories,
				autoPlay,
				slidesToScroll,
			},
			className,
			setAttributes,
			latestPosts,
			categoriesList,
		} = this.props;

		if ( ! latestPosts ) {
			return (
				<p className={ className }>
					<Spinner />
					{ __( 'Loading Posts' ) }
				</p>
			);
		}
		if ( 0 === latestPosts.length ) {
			return <p>{ __( 'No Posts' ) }</p>;
		}

		const classes = classnames(
			'sk-posts-slider-wrapper',
			columnsToClass( numberCols )
		);

		return (
			<div className={ className }>
				<BlockControls>
					<BlockAlignmentToolbar
						value={ align }
						onChange={ nextAlign => setAttributes( { align: nextAlign } ) }
					/>
				</BlockControls>
				<InspectorControls>
					<QueryControls
						{ ...{ order, orderBy } }
						numberOfItems={ postsToShow }
						categoriesList={ categoriesList }
						selectedCategoryId={ categories }
						onOrderChange={ value => setAttributes( { order: value } ) }
						onOrderByChange={ value => setAttributes( { orderBy: value } ) }
						onCategoryChange={ value =>
							setAttributes( { categories: '' !== value ? value : undefined } )
						}
						onNumberOfItemsChange={ value =>
							setAttributes( { postsToShow: value } )
						}
					/>
					<RangeControl
						value={ numberCols }
						onChange={ numberCols => setAttributes( { numberCols } ) }
						min={ 1 }
						max={ 6 }
						step={ 1 }
						label={ __( 'Columns' ) }
					/>
					<RangeControl
						value={ startAt }
						onChange={ startAt => setAttributes( { startAt } ) }
						min={ 1 }
						max={ 20 }
						step={ 1 }
						allowReset="true"
						label={ __( 'Blog # to start at' ) }
					/>
					<ToggleControl
						label="Autoplay Slideshow?"
						checked={ autoPlay }
						onChange={ () =>
							setAttributes( {
								autoPlay: ! autoPlay,
							} )
						}
					/>
					<RangeControl
						value={ slidesToScroll }
						onChange={ slidesToScroll => setAttributes( { slidesToScroll } ) }
						min={ 1 }
						max={ 6 }
						step={ 1 }
						label={ __( 'Slides to scroll' ) }
					/>
				</InspectorControls>

				<div className={ classes }>
					{ latestPosts.map( blog => {
						console.log( blog );

						return (
							<article
								className="sk-blog-item sk-posts-slider-item"
								key={ blog.id }
							>
								<div className="sk-blog-item-link">
									<img
										src={ blog.featured_image }
										className="attachment-thumbnail size-thumbnail"
										alt=""
									/>
									<span className="sk-blog-item-content">
										<p className="sk-blog-item-content__title">
											{ blog.title.raw }
										</p>
									</span>
								</div>
							</article>
						);
					} ) }
				</div>
			</div>
		);
	}
}

export default withSelect( ( select, props ) => {
	const { postsToShow, startAt, order, orderBy, categories } = props.attributes;

	// shorthand
	const { getEntityRecords } = select( 'core' );

	const latestPostsQuery = pickBy(
		{
			categories,
			order,
			orderby: orderBy,
			per_page: postsToShow,
			offset: startAt, // dont know why this is out
		},
		value => ! isUndefined( value )
	);

	const categoriesListQuery = {
		per_page: 100,
	};

	return {
		latestPosts: getEntityRecords( 'postType', 'post', latestPostsQuery ),
		categoriesList: getEntityRecords(
			'taxonomy',
			'category',
			categoriesListQuery
		),
	};
} )( PostsSliderEdit );

function columnsToClass( n ) {
	return `slick-has-${ n }-columns`;
}
