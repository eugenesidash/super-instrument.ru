$(document).ready(function() {
	$( window ).scroll(function() {
		showBadges();
		$( window ).trigger("resize");
		
		hackMagazin();
		setTimeout(function() { 
			hackMagazin();
		}, 1000);
	});
});

function hackMagazin() {
	$(".ocstore-badge").unbind("click");
	$(".ocstore-badge-item").unbind("click");
	$(".ocstore-badge-item").attr("onclick", "");
	$(".ocstore-badge-item").css("z-index", 2); 
	$(".ocstore-badge-item span").css("z-index", 3);
}