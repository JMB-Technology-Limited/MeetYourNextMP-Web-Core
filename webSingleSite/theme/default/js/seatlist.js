
var searchBox;



$(document).ready(function() {
	searchBox = $('form#SearchForm input[name="freeTextSearch"]');
	searchBox.keyup(searchSeatList);
	searchBox.change(searchSeatList);
});


function searchSeatList() {
	var searchTerm = searchBox.val().trim().toLowerCase();
	if (searchTerm == '') {
		$('ul#SearchAreas li').show();
	} else {
		$('ul#SearchAreas li').each(function() {
			var elem = $(this);
			var text = elem.text().toLowerCase();
			if (text.search(searchTerm) == -1) {
				elem.hide();
			} else {
				elem.show();
			}
		});
	}
	searchBox.focus();
}
