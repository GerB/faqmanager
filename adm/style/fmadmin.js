setMoveBtns();
/**
 * The following callbacks are for reordering and deleting FAQ entries.
 * They mimic their row_* counterparts, yet we don't have tables but fieldsets
 */
phpbb.addAjaxCallback('faq_down', function(res) {
	if (typeof res.success === 'undefined' || !res.success) {
		return;
	}

	var $firstTr = $(this).parents('#currentfaq fieldset'),
		$secondTr = $firstTr.next();

	$firstTr.insertAfter($secondTr);
    setMoveBtns();
});

phpbb.addAjaxCallback('faq_up', function(res) {
	if (typeof res.success === 'undefined' || !res.success) {
		return;
	}

	var $secondTr = $(this).parents('#currentfaq fieldset'),
		$firstTr = $secondTr.prev();

	$secondTr.insertBefore($firstTr);
    setMoveBtns();
});

phpbb.addAjaxCallback('faq_delete', function(res) {
	if (res.SUCCESS !== false) {
		$(this).parents('fieldset').remove();
        setMoveBtns();
	}
});
function setMoveBtns() {
    $('#currentfaq .up, #currentfaq .down').show();
    $('#currentfaq .up-disabled, #currentfaq .down-disabled').hide();
    $('#currentfaq .up').first().hide();
    $('#currentfaq .up-disabled').first().show();
    $('#currentfaq .down').last().hide();
    $('#currentfaq .down-disabled').last().show();
}
