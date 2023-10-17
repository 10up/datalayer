/**
 * Send the event to Google Tag Manager.
 *
 * @param {HTMLElement} element The element that was clicked.
 */
function sendGTMEvent(element) {
	const attributeList = ['data-event', 'data-ctaText', 'data-destinationLink'];

	const data = attributeList.reduce((acc, attribute) => {
		const value = element.getAttribute(attribute);
		if (value) {
			const key = attribute.replace('data-', '');
			acc[key] = value;
		}
		return acc;
	}, {});

	window.dataLayer = window.dataLayer || [];
	window.dataLayer.push({ data });
}

/**
 * Add event listeners to the document.
 */
document.addEventListener('DOMContentLoaded', () => {
	document.addEventListener('click', function (event) {
		const clickedElement = event.target;
		const dataEvent = clickedElement.getAttribute('data-event');

		if (dataEvent) {
			sendGTMEvent(clickedElement);
		}
	});
});
