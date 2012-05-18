$(document).ready(function() {

	function changeTheme(theme){
		
		window.location.search = "?theme=" + theme;
	}

	$("#themeswitcher").change(function() {
		changeTheme($(this).val());
	});
});
