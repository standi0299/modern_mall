(function($) {
	$(document).ready(function() {
		$('#checkbox-essential2, #checkbox-essential3').on('change', function() {
			const agree3 = $('#checkbox-essential2').prop('checked');
			const agree4 = $('#checkbox-essential3').prop('checked');

			if (agree3 && agree4) {
				$('#checkbox-all').prop('checked', true);
			} else {
				$('#checkbox-all').prop('checked', false);
			}
		});
	});
})(jQuery1_11_0);
