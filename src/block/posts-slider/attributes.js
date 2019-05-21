export const attributes = {
	align: {
		type: 'string',
	},
	numberCols: {
		type: 'number',
		default: 2,
	},
	postsToShow: {
		type: 'number',
		default: 4,
	},
	startAt: {
		type: 'number',
		default: 0,
	},
	order: {
		type: 'string',
		default: 'desc',
	},
	orderBy: {
		type: 'string',
		default: 'date',
	},
	categories: {
		type: 'string',
	},
	autoPlay: {
		type: 'boolean',
		default: true,
	},
	slidesToScroll: {
		type: 'number',
		default: 1,
	},
};
