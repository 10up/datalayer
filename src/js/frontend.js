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

	/**
	 * If the ctaText is not set, use the innerText of the element.
	 * WP_HTML_Tag_Processor is limited in what it can do, so we can't
	 * assign the data-ctaText attribute.
	 */
	if (!data.ctaText) {
		data.ctaText = element.innerText.trim();
	}

	window.dataLayer = window.dataLayer || [];
	window.dataLayer.push({ data });
}

/**
 * Add event listeners to the document.
 */
document.addEventListener('DOMContentLoaded', () => {
	// Add event listeners to all elements with a data-event attribute.
	document.addEventListener('click', function (event) {
		const clickedElement = event.target;
		const dataEvent = clickedElement.getAttribute('data-event');

		if (dataEvent) {
			sendGTMEvent(clickedElement);
		}
	});

	// Add event listeners to all internal links without a data-event attribute.
	const hyperLinks = document.querySelectorAll('a:not([data-event])'); // Select only links without data-event attribute.
	hyperLinks.forEach((link) => {
		// Check if it's an internal link.
		if (link.host === window.location.host) {
			link.addEventListener('click', () => {
				const ctaText = link.innerText;
				const destinationLink = link.href;

				window.dataLayer = window.dataLayer || [];
				window.dataLayer.push({
					event: 'recirculation',
					module: 'hyperlink',
					ctaText,
					destinationLink,
				});
			});
		}
	});
});
