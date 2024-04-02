/**
 * Pro Mime Types plugin
 * Copyright (C) 2023 - 2024 Sybre Waaijer, CyberWire B.V. (https://cyberwire.nl/)
 *
 * This program is free software: you can redistribute it and/or modify
 * it under the terms of the GNU General Public License version 3 as published
 * by the Free Software Foundation.
 *
 * This program is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the
 * GNU General Public License for more details.
 *
 * You should have received a copy of the GNU General Public License
 * along with this program. If not, see <http://www.gnu.org/licenses/>.
 */

/**
 * Most of the scripts below are repurposed from The SEO Framework plugin.
 */

'use strict';

document.addEventListener( 'DOMContentLoaded', () => {

	let passiveSupported = false,
		captureSupported = false;
	/**
	 * Sets passive & capture support flag.
	 * @link https://developer.mozilla.org/en-US/docs/Web/API/EventTarget/addEventListener
	 */
	try {
		( () => {
			const options = {
				get passive() {
					passiveSupported = true;
					return false;
				},
				get capture() {
					captureSupported = true;
					return false;
				},
			};
			// These EventTarget methods will try to get 'passive' and/or 'capture' when it's supported.
			window.addEventListener( 'pmt-test-passive', null, options );
			window.removeEventListener( 'pmt-test-passive', null, options );
		} )();
	} catch ( e ) {
		passiveSupported = false;
		captureSupported = false;
	}
	const options = passiveSupported && captureSupported ? { capture: true, passive: true } : true;

	const ttWraps        = document.querySelectorAll( '[data-pmt-tooltip]' ),
		  ttActions      = 'mouseenter pointerdown touchstart focus'.split( ' ' ),
		  ttLeaveActions = 'mouseleave mouseout blur'.split( ' ' );

	ttWraps.forEach( wrap => {
		if ( ! wrap.dataset?.pmtTooltip ) return;

		wrap.tabIndex = 0;

		ttActions.forEach( e => {
			wrap.addEventListener( e, handleToolTip, options );
		} );

		wrap.addEventListener(
			'click',
			preventTooltipHandleClick,
			captureSupported ? { capture: false } : false
		);
	} );

	let instigatingTooltip = false;
	function handleToolTip( event ) {
		if (
			   instigatingTooltip
			|| event.target.dataset?.hasTooltip
		) return;

		instigatingTooltip = true;

		createTooltip( event );
		event.stopPropagation();

		instigatingTooltip = false;
	}
	async function createTooltip( event ) {
		ttLeaveActions.forEach( e => {
			event.target.addEventListener( e, handleTooltipClear );
		} );

		event.target.innerHTML +=
			`<div class=pmt-tooltip><span class=pmt-tooltip-text-wrap><span class=pmt-tooltip-text>${event.target.dataset.pmtTooltip}</span><div class=pmt-tooltip-arrow></div></div>`;

		event.target.dataset.hasTooltip = true;

		const tooltip = event.target.querySelector( '.pmt-tooltip' );
		const rect    = tooltip.querySelector( '.pmt-tooltip-text-wrap' ).getBoundingClientRect();

		tooltip.style.top = `${
			-rect.height
			-9
		}px`;
		tooltip.style.left = `${
			-rect.width / 2
			+ parseInt( getComputedStyle( tooltip ).fontSize ) * .5
		}px`;
		tooltip.querySelector( '.pmt-tooltip-arrow' ).style.left = `${
			rect.width / 2 - 4.5 // arrow is 9px wide, 4.5 is middle.
		}px`;
	}
	function handleTooltipClear( event ) {

		removeTooltip( event.target );

		ttActions.forEach( e => {
			event.target.removeEventListener( e, handleTooltipClear );
		} );
	}
	function removeTooltip( element ) {

		if ( element instanceof HTMLElement ) {
			delete element.dataset.hasTooltip;
			_clickLocker( element ).release();
		}

		const tooltip = element.classList.contains( 'pmt-tooltip' )
			? element
			: element?.querySelector( '.pmt-tooltip' )

		tooltip?.parentNode.removeChild( tooltip );
	}

	function preventTooltipHandleClick ( event ) {
		if ( _clickLocker( event.target ).isLocked() ) return;
		event.preventDefault();
		// iOS 12 bug causes two clicks at once. Let's set this asynchronously.
		setTimeout( () => _clickLocker( event.target ).lock() );
	}
	const _clickLocker = element => {
		return {
			lock: () => {
				element.dataset.preventedClick = 1;

				// If the element is a label with a "for"-attribute, then we must forward this
				if ( element instanceof HTMLLabelElement && element.htmlFor ) {
					let input = document.getElementById( element.htmlFor );
					if ( input ) input.dataset.preventedClick = 1;
				}
				if ( element instanceof HTMLInputElement && element.id ) {
					document.querySelectorAll( `label[for="${element.id}"]` ).forEach(
						label => { label.dataset.preventedClick = 1; }
					);
				}
			},
			release: () => {
				if ( ! ( element instanceof Element ) ) return;

				delete element.dataset.preventedClick;

				if ( element instanceof HTMLLabelElement && element.htmlFor ) {
					let input = document.getElementById( element.htmlFor );
					if ( input ) delete input.dataset.preventedClick;
				}
				if ( element instanceof HTMLInputElement && element.id ) {
					document.querySelectorAll( `label[for="${element.id}"]` ).forEach(
						la => { delete la.dataset.preventedClick; }
					);
				}
			},
			isLocked: () => element instanceof Element && !!+element.dataset.preventedClick,
		}
	}

	document.querySelectorAll( '.pmt-settings-accordion' ).forEach( accordion => {
		accordion.addEventListener( 'click', event => {
			if ( ! event.target.classList.contains( 'pmt-settings-accordion-trigger' ) ) return;

			if ( 'true' === event.target.getAttribute( 'aria-expanded' ) ) {
				event.target.setAttribute( 'aria-expanded', 'false' );
				document.querySelector( `#${event.target.getAttribute('aria-controls')}` ).setAttribute( 'hidden', true );
			} else {
				event.target.setAttribute( 'aria-expanded', 'true' );
				document.querySelector( `#${event.target.getAttribute('aria-controls')}` ).removeAttribute( 'hidden' );
			}
		} );
	} );
} );
