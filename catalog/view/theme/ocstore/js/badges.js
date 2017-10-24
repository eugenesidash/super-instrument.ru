$(document).ready(function() {
	$( window ).resize(function() {
		if (typeof resizeTimer != "undefined")
			clearTimeout(resizeTimer);
		showBadges();
		resizeTimer = setTimeout(function() { showBadges(); }, 1000);
	});
	$( window ).trigger("resize");
	
	$(".product-thumb.thumbnail").mouseout(function() {
		if ($(".ocstore-badge-item[data-product-hash="+$(this).data("product-hash")+"]").length > 0) {
			$( window ).trigger("resize");
		}
	});
	
	
	/* hack Magazin template */
	$("ul.nav li").click(function() {
		checkBadget();
	});

});

function showBadges() {
	$(".ocstore-badge-item").remove();
    $.each($(".ocstore-badge[data-product-id]"), function(idx, element) {
        if (typeof badges_product[$(element).data("product-id")] != "undefined") {
            $.each(badges_product[$(element).data("product-id")], function(idx, badgeId) {
				if (!$(element).is(":visible"))
					return;
				
				/* hack XDS Coloring Theme */
				if ($(element).parent().hasClass("owl-item") || $(element).parent().hasClass("option-hover")) { 
					if ($(element)[0].getBoundingClientRect().left < $(element).parents(".owl-wrapper-outer")[0].getBoundingClientRect().left ||
						Number($(element)[0].getBoundingClientRect().left) > Number($(element).parents(".owl-wrapper-outer")[0].getBoundingClientRect().left) + Number($(element).parents(".owl-wrapper-outer").width()))
						return;
				}
				/* end hack XDS Coloring Theme */
				
                if ($(".ocstore-badge[data-product-id="+$(element).data("product-id")+"]").find("a").attr("href") != "")
                    badgeUnionObject = $( "<div class=\"ocstore-badge-item\" data-product-hash=\""+$(element).data("product-hash")+"\" onClick='window.location.href= $(\".ocstore-badge[data-product-id="+$(element).data("product-id")+"]\").find(\"a\").attr(\"href\")'></div>" );
                else
                    badgeUnionObject = $( "<div class=\"ocstore-badge-item\" data-product-hash=\""+$(element).data("product-hash")+"\" onClick='$(\".ocstore-badge[data-product-id="+$(element).data("product-id")+"]\").find(\"a\").trigger(\"click\")'></div>" );
                badgeUnionObject.css("position", "absolute");
                badgeUnionObject.css("cursor", "pointer");
                badgeUnionObject.css("z-index", "10");

                if (badges[badgeId].data.badge.top != "")
                    badgeUnionObject.css("top", (Number(badges[badgeId].data.badge.top) + Number($(element).offset().top) - 1) + "px");
                if (badges[badgeId].data.badge.left != "")
                    badgeUnionObject.css("left", (Number($(element).offset().left) + Number(badges[badgeId].data.badge.left)) + "px");
                if (badges[badgeId].data.badge.right != "")
                    badgeUnionObject.css("right", (Number($(window).width()) -  Number($(element).offset().left) - Number($(element)[0].getBoundingClientRect().width) + Number(badges[badgeId].data.badge.right) - 1) + "px");
                if (badges[badgeId].data.badge.bottom != "")
                    badgeUnionObject.css("top", (Number($(element).offset().top) + Number($(element)[0].getBoundingClientRect().height) - Number(badges[badgeId].data.badge.bottom) - Number(badges[badgeId].data.image_height)) + "px");
				
                badgeObject = $( "<img />" );
                badgeObject.attr("src", "/image/"+badges[badgeId]['image']);
				if (typeof badges[badgeId].data.ballontext != "undefined" && badges[badgeId].data.ballontext != "")
					badgeObject.attr("data-title", badges[badgeId].data.ballontext).attr("data-position", badges[badgeId].data.ballonposition);
				
                $(badgeUnionObject).prepend(badgeObject);

                if (badges[badgeId].data.badgetext != "") {
                    badgeObject = $( "<span>"+badges[badgeId].data.badgetext+"</span>" );
                    badgeObject.css("position", "absolute");
                    badgeObject.css("z-index", "11");
                    badgeObject.css("text-align", "left");
                    badgeObject.css("line-height", "1.42857");
                    if (badges[badgeId].data.text.top != "")
                        badgeObject.css("top", badges[badgeId].data.text.top + "px");
                    if (badges[badgeId].data.text.left != "")
                        badgeObject.css("left", badges[badgeId].data.text.left + "px");
                    if (badges[badgeId].data.text.right != "")
                        badgeObject.css("right", badges[badgeId].data.text.right + "px");
                    if (badges[badgeId].data.text.bottom != "")
                        badgeObject.css("bottom", badges[badgeId].data.text.bottom + "px");
                    if (badges[badgeId].data.color != "")
                        badgeObject.css("color", badges[badgeId].data.color);
                    if (badges[badgeId].data.size != "")
                        badgeObject.css("font-size", badges[badgeId].data.size + "pt");
                    if (badges[badgeId].data.font != "")
                        badgeObject.css("font-family", badges[badgeId].data.font);
                    if (badges[badgeId].data.angle > 0) {
                        badgeObject.css("transform", "rotate(" + badges[badgeId].data.angle + "deg)");
                        badgeObject.css("-webkit-transform", "rotate(" + badges[badgeId].data.angle + "deg)");
                        badgeObject.css("-moz-transform", "rotate(" + badges[badgeId].data.angle + "deg)");
                        badgeObject.css("-o-transform", "rotate(" + badges[badgeId].data.angle + "deg)");
                        badgeObject.css("-ms-transform", "rotate(" + badges[badgeId].data.angle + "deg)");
                    }
                    badgeObject.css("width", badges[badgeId].data.image_width + "px");
                    badgeObject.css("height", badges[badgeId].data.image_height + "px");
                    $(badgeUnionObject).prepend(badgeObject);
                }

                $("body").append(badgeUnionObject);
            });
        }
    });
	
	$.each($('.ocstore-badge-item > img[data-title]'), function() {
		$('.ocstore-badge-item').balloon({ contents: $(this).data("title"), position: $(this).data("position") });
	});
}

/* hack XDS Coloring Theme */
function checkBadget(el) {
	var base = this;
	$(".ocstore-badge-item[data-product-hash]").hide();
	setTimeout(function() { showBadges(); }, 1000);
}


/* end hack XDS Coloring Theme */