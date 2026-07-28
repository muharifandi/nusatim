/*
 * Sitewide image skeleton loading, no dependency.
 * Watches every <img> (including ones injected later by Livewire/AJAX/
 * carousels) and marks it done/error as soon as it finishes loading so
 * assets/css/img-skeleton.css can swap off the shimmer placeholder.
 */
(function () {
	function markLoaded(img) {
		img.classList.add('skel-done');
	}

	function markError(img) {
		img.classList.add('skel-done', 'skel-error');
	}

	function observe(img) {
		if (img.dataset.skelBound || img.classList.contains('skel-off')) {
			return;
		}
		img.dataset.skelBound = '1';

		if (img.complete) {
			img.naturalWidth > 0 ? markLoaded(img) : markError(img);
			return;
		}

		img.addEventListener('load', function () {
			markLoaded(img);
		}, { once: true });
		img.addEventListener('error', function () {
			markError(img);
		}, { once: true });
	}

	function scan(root) {
		(root || document).querySelectorAll('img').forEach(observe);
	}

	if (document.readyState === 'loading') {
		document.addEventListener('DOMContentLoaded', function () {
			scan();
		});
	} else {
		scan();
	}

	var observer = new MutationObserver(function (mutations) {
		mutations.forEach(function (mutation) {
			mutation.addedNodes.forEach(function (node) {
				if (node.nodeType !== 1) {
					return;
				}
				if (node.tagName === 'IMG') {
					observe(node);
				} else if (node.querySelectorAll) {
					scan(node);
				}
			});
		});
	});
	observer.observe(document.documentElement, { childList: true, subtree: true });
})();
