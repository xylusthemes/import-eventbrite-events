const { __ } = wp.i18n;
const { registerBlockType } = wp.blocks;
const { dateI18n, getSettings } = wp.date;
const {
	PanelBody,
	PanelRow,
	Button,
	Dropdown,
	RangeControl,
	SelectControl,
	ToggleControl,
	RadioControl,
	DateTimePicker,
} = wp.components;
var InspectorControls = wp.blockEditor.InspectorControls;

registerBlockType( 'iee-block/eventbrite-events', {
	title: __( 'Eventbrite Events' ),
	description: __( 'Block for Display Eventbrite Events' ),
	icon: {
		foreground: '#f5662e',
		src: <svg xmlns="http://www.w3.org/2000/svg" width="24px" height="24px" viewBox="0 0 1000 1000">
		<g transform="translate(0.000000,1000.000000) scale(0.100000,-0.100000)"
		fill="#000000" stroke="none">
		<path d="M4590 9989 c-942 -81 -1818 -411 -2565 -964 -378 -280 -770 -672 -1050 -1050 -558 -752 -884 -1626 -965 -2580 -13 -155 -13 -635 0 -790 81 -954 407 -1828 965 -2580 280 -378 672 -770 1050 -1050 752 -558 1626 -884 2580 -965 155 -13 635 -13 790 0 954 81 1828 407 2580 965 378 280 770 672 1050 1050 558 752 884 1626 965 2580 13 155 13 635 0 790 -81 954 -407 1828 -965 2580 -280 378 -672 770 -1050 1050 -752 558 -1626 884 -2580 965 -139 11 -666 11 -805 -1z m2094 -2271 c14 -20 16 -85 16 -506 l0 -483 -25 -24 -24 -25 -1132 0 c-1040 0 -1134 -1 -1151 -17 -17 -15 -18 -46 -18 -559 l0 -543 26 -20 c27 -21 30 -21 1033 -21 770 0 1010 -3 1019 -12 9 -9 12 -134 12 -499 l0 -488 -22 -15 c-20 -14 -134 -16 -1027 -16 -921 0 -1006 -1 -1023 -17 -17 -15 -18 -46 -18 -561 0 -494 2 -547 17 -564 15 -17 65 -18 1188 -18 1103 0 1174 -1 1193 -17 30 -26 42 -82 49 -218 21 -452 -230 -751 -687 -820 -79 -12 -326 -15 -1487 -15 -1252 0 -1393 2 -1407 16 -14 14 -16 239 -16 2327 1 1905 3 2325 14 2387 63 345 331 636 666 720 41 10 86 21 100 23 14 2 624 5 1357 6 l1332 1 15 -22z" fill="#f5662e"/>
		</g>
		</svg>, 
	},
	category: 'widgets',
	keywords: [
		__( 'Events' ),
		__( 'Eventbrite' ),
		__( 'eventbrite events' ),
	],
	description: 'Block for Display Eventbrite Events',
    attributes: {
        col: {
			type: 'number',
			default: 2,
		},
		posts_per_page: {
			type: 'number',
			default: 12,
		},
		past_events: {
			type: 'boolean',
     		default: false
		},
		start_date: {
			type: 'string',
			default: '',
		},
		end_date: {
			type: 'string',
			default: '',
		},
		order: {
			type: 'string',
			default: 'ASC',
		},
		orderby: {
			type: 'string',
			default: 'event_start_date',
		},
		layout: {
			type: 'string',
			default: '',
		},
    },
    edit: ( { attributes, setAttributes } ) => {
        const { col, posts_per_page, past_events, start_date, end_date, order, orderby, layout } = attributes;
		const settings = getSettings();
		const dateClassName = past_events === true ? 'iee_hidden' : '';
		const { serverSideRender: ServerSideRender } = wp;

		const is12HourTime = /a(?!\\)/i.test(
			settings.formats.time
				.toLowerCase() // Test only the lower case a
				.replace( /\\\\/g, '' ) // Replace "//" with empty strings
				.split( '' ).reverse().join( '' ) // Reverse the string and test for "a" not followed by a slash
		);
        return (
            <div>
                <InspectorControls>
					<PanelBody title={ __( 'Eventbrite Events Setting' ) }>
						<RangeControl
								label={ __( 'Columns' ) }
								value={ col || 2 }	
								onChange={ ( value ) => setAttributes( { col: value } ) }
								min={ 1 }
								max={ 4 }
							/>
						<RangeControl
							label={ __( 'Events per page' ) }
							value={ posts_per_page || 12 }
							onChange={ ( value ) => setAttributes( { posts_per_page: value } ) }
							min={ 1 }
							max={ 100 }
						/>
						<ToggleControl
							label={ __( 'Display past events' ) }
							checked={ past_events }
							onChange={ value => {
								return setAttributes( { 
									start_date: '',
									end_date: '',
									past_events: value
								} );
							}
							}
						/>
						<SelectControl
							label="Event Grid View Layout"
							value={ layout }
							options={ [
								{ label: 'Default', value: '' },
								{ label: 'Style 2', value: 'style2' },
							] }
							onChange={ ( value ) => setAttributes( { layout: value } ) }
						/>
						<SelectControl
							label="Order By"
							value={ orderby }
							options={ [
								{ label: 'Event Start Date', value: 'event_start_date' },
								{ label: 'Event End Date', value: 'event_end_date' },
								{ label: 'Event Title', value: 'title' },
							] }
							onChange={ ( value ) => setAttributes( { orderby: value } ) }
						/>
						<RadioControl
							label={ __( 'Order' ) }
							selected={ order }
							options={ [
								{ label: __( 'Ascending' ), value: 'ASC' },
								{ label: __( 'Descending' ), value: 'DESC' },
							] }
							onChange={ value => setAttributes( { order: value } ) }
						/>
						<PanelRow className={ `iee-start-date ${ dateClassName }` }>
							<span>{ __( 'Event Start Date' ) }</span>
							<Dropdown
								label={ __( 'Start Date' ) }
								position="bottom left"
								contentClassName="iee-start-date__dialog"
								popoverProps={ { placement: 'bottom-start' } }
								renderToggle={ ( { isOpen, onToggle } ) => (
									<Button
										type="button"
										className="iee-start-date__toggle"
										onClick={ onToggle }
										aria-expanded={ isOpen }
										isLink
									>
										{ eventDateLabel( start_date, true ) }
									</Button>
								) }
								renderContent={ () =>
									<DateTimePicker
										currentDate={ start_date !== '' ? start_date : new Date() }
										onChange={ ( value ) => setAttributes( { start_date: value } ) }
										locale={ settings.l10n.locale }
										is12Hour={ is12HourTime }
										__nextRemoveHelpButton
										__nextRemoveResetButton
									/>
								}
							/>
						</PanelRow>
						<PanelRow className={ `iee-end-date ${ dateClassName }` }>
							<span>{ __( 'Event End Date' ) }</span>
							<Dropdown
								label={ __( 'End Date' ) }
								position="bottom left"
								contentClassName="iee-end-date__dialog"
								popoverProps={ { placement: 'bottom-start' } }
								renderToggle={ ( { isOpen, onToggle } ) => (
									<Button
										type="button"
										className="iee-end-date__toggle"
										onClick={ onToggle }
										aria-expanded={ isOpen }
										isLink
									>
										{ eventDateLabel( end_date ) }
									</Button>
								) }
								renderContent={ () =>
									<DateTimePicker
										currentDate={ end_date !== '' ? end_date : new Date() }
										onChange={ ( value ) => setAttributes( { end_date: value } ) }
										locale={ settings.l10n.locale }
										is12Hour={ is12HourTime }
										__nextRemoveHelpButton
										__nextRemoveResetButton
									/>
								}
							/>
						</PanelRow>
					</PanelBody>
                </InspectorControls>
				<ServerSideRender
					block="iee-block/eventbrite-events"
					attributes={attributes}
					key={JSON.stringify(attributes)}
				/>
            </div>
        );
    },
	save: function() {
		// Rendering in PHP.
		return null;
	},
});
function eventDateLabel( date, start ) {
	const settings = getSettings();
	const defaultLabel = start ? __( 'Select Start Date' ) : __( 'Select End Date' );
	return date ?
		dateI18n( settings.formats.datetime, date ) :
		defaultLabel;
}
