{{--
    Loaded via a panel render hook (not the page's own view) so the init
    function is guaranteed to exist before Alpine's x-init runs, mirroring
    the jsvectormap-loader pattern used for the admin dashboard's country
    map. Event listeners are delegated on the board container (not attached
    per-card) so they keep working after Livewire re-renders the columns
    following a moveLead() call.
--}}
<script>
	window.pipelineInitBoard = function (el, wire) {
		if (! el || el.dataset.initialized === '1') {
			return;
		}
		el.dataset.initialized = '1';

		el.addEventListener('dragstart', function (event) {
			var card = event.target.closest('.pipeline-card');
			if (! card) {
				return;
			}
			event.dataTransfer.setData('text/plain', card.dataset.leadId);
			event.dataTransfer.effectAllowed = 'move';
		});

		el.addEventListener('dragover', function (event) {
			if (event.target.closest('.pipeline-column')) {
				event.preventDefault();
			}
		});

		el.addEventListener('drop', function (event) {
			var column = event.target.closest('.pipeline-column');
			if (! column) {
				return;
			}
			event.preventDefault();

			var leadId = event.dataTransfer.getData('text/plain');
			var newStatus = column.dataset.status;

			if (leadId && newStatus) {
				wire.call('moveLead', parseInt(leadId, 10), newStatus);
			}
		});
	};
</script>
