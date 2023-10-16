/**
 * TagManager
 *
 * Fuctionality for Google Analytics and Tag Manager
 *
 * @returns {void}
 */

const TagManager = () => {
	/**
	 * dataLayer push on a CTA click
	 *
	 * @returns {void}
	 */
	const addCTAListeners = () => {
		const buttons = document.querySelectorAll('.wp-block-button__link');
		buttons.forEach((button) => {
			button.addEventListener('click', () => {
				const ctaText = button.innerText;
				const destinationLink = button.href;

				window.dataLayer = window.dataLayer || [];
				window.dataLayer.push({
					event: 'button',
					ctaText,
					destinationLink,
				});
			});
		});
	};

	/**
	 * dataLayer push on a click on a homepage section
	 *
	 * @returns {void}
	 */
	const addHomePageListeners = () => {
		const bodyHome = document.querySelector('body.home');

		if (bodyHome) {
			const links = bodyHome.querySelectorAll('main a');
			links.forEach((link) => {
				link.addEventListener('click', () => {
					// Find the closest ancestor <section> element
					const closestSection = link.closest('section');

					if (closestSection) {
						// Find the closest previous sibling <h2> element with class 'wp-block-heading'
						const closestH2 = closestSection.previousElementSibling;
						if (
							closestH2 &&
							closestH2.tagName === 'H2' &&
							closestH2.classList.contains('wp-block-heading')
						) {
							const ctaText = link.innerText;
							const destinationLink = link.href;

							window.dataLayer = window.dataLayer || [];
							window.dataLayer.push({
								event: 'homepage',
								module: closestH2.innerText,
								ctaText,
								destinationLink,
							});
						}
					}
				});
			});
		}
	};

	/**
	 * dataLayer push on an image click
	 *
	 * @returns {void}
	 */
	const addImageListeners = () => {
		// Find clickable images in articles
		const articles = document.querySelectorAll('article');
		articles.forEach((article) => {
			const image = article.querySelector('img');
			const link = article.querySelector('a');

			if (image && link) {
				image.addEventListener('click', () => {
					const destinationLink = link.getAttribute('href');

					window.dataLayer = window.dataLayer || [];
					window.dataLayer.push({
						event: 'clickable_image',
						destinationLink,
					});
				});
			}
		});

		// Find clickable images in standalone figures
		const figures = document.querySelectorAll('figure a');
		figures.forEach((figure) => {
			const image = figure.querySelector('img');
			if (image) {
				figure.addEventListener('click', () => {
					const destinationLink = figure.getAttribute('href');

					window.dataLayer = window.dataLayer || [];
					window.dataLayer.push({
						event: 'clickable_image',
						destinationLink,
					});
				});
			}
		});
	};

	/**
	 * dataLayer push on a click on one of the following:
	 * Editorial Links
	 * End of Content
	 * Hyperlink
	 * Post Tags at Bottom of Page
	 * Nativo
	 *
	 * @returns {void}
	 */
	const addRecirculationListeners = () => {
		// Editorial Links
		const editorialLinks = document.querySelectorAll('div.wp-block-query-pagination a');
		editorialLinks.forEach((link) => {
			link.addEventListener('click', () => {
				const ctaText = link.innerText;
				const destinationLink = link.href;

				window.dataLayer = window.dataLayer || [];
				window.dataLayer.push({
					event: 'recirculation',
					module: 'Editorial Links',
					ctaText,
					destinationLink,
				});
			});
		});

		// End of Content
		const endoOfContentLinks = document.querySelectorAll(
			'fotter.wp-block-gearpatrol-article-footer a',
		);
		endoOfContentLinks.forEach((link) => {
			link.addEventListener('click', () => {
				const ctaText = link.innerText;
				const destinationLink = link.href;

				window.dataLayer = window.dataLayer || [];
				window.dataLayer.push({
					event: 'recirculation',
					module: 'End of Content',
					ctaText,
					destinationLink,
				});
			});
		});

		// Hyperlink
		const hyperLinks = document.querySelectorAll('a');
		hyperLinks.forEach((link) => {
			link.addEventListener('click', () => {
				const ctaText = link.innerText;
				const destinationLink = link.href;

				window.dataLayer = window.dataLayer || [];
				window.dataLayer.push({
					event: 'recirculation',
					module: 'Hyperlink',
					ctaText,
					destinationLink,
				});
			});
		});

		// Post Tags at Bottom of Page
		const postTagLinks = document.querySelectorAll('div.wp-block-post-terms a');
		postTagLinks.forEach((link) => {
			link.addEventListener('click', () => {
				const ctaText = link.innerText;
				const destinationLink = link.href;

				window.dataLayer = window.dataLayer || [];
				window.dataLayer.push({
					event: 'recirculation',
					module: 'Post Tags at Bottom of Page',
					ctaText,
					destinationLink,
				});
			});
		});

		// TODO: Nativo module should be added here
	};

	/**
	 * dataLayer push on a navigation link click (top or flyout navigation)
	 *
	 * @returns {void}
	 */
	const addNavigationListeners = () => {
		const links = document.querySelectorAll('nav.site-header__navigation a');
		links.forEach((link) => {
			link.addEventListener('click', () => {
				const ctaText = link.innerText;
				const destinationLink = link.href;

				window.dataLayer = window.dataLayer || [];
				window.dataLayer.push({
					event: 'navigation',
					ctaText,
					destinationLink,
				});
			});
		});
	};

	/**
	 * dataLayer push on a footer link click
	 *
	 * @returns {void}
	 */
	const addFooterListeners = () => {
		const links = document.querySelectorAll('footer.site-footer a');
		links.forEach((link) => {
			link.addEventListener('click', () => {
				const ctaText = link.innerText;
				const destinationLink = link.href;

				window.dataLayer = window.dataLayer || [];
				window.dataLayer.push({
					event: 'footer',
					ctaText,
					destinationLink,
				});
			});
		});
	};

	/**
	 * dataLayer push on a product link click (button or affiliate hyperlink)
	 *
	 * @returns {void}
	 */
	const addProductListeners = () => {
		const links = document.querySelectorAll('article.wp-block-gearpatrol-product a');
		links.forEach((link) => {
			link.addEventListener('click', () => {
				const article = link.closest('article.wp-block-gearpatrol-card');
				if (article) {
					const productTitleElement = article.querySelector('.wp-block-post-title a');
					if (productTitleElement) {
						const productName = productTitleElement.innerText;
						const destinationLink = productTitleElement.href;

						window.dataLayer = window.dataLayer || [];
						window.dataLayer.push({
							event: 'product',
							ctaText: productName,
							prodName: productName,
							destinationLink,
						});
					}
				}
			});
		});
	};

	/**
	 * init
	 *
	 * Init Tag Manager.
	 *
	 * @returns {void}
	 */
	const init = () => {
		addCTAListeners();
		addHomePageListeners();
		addImageListeners();
		addRecirculationListeners();
		addNavigationListeners();
		addFooterListeners();
		addProductListeners();
	};

	init();
};

export { TagManager };
