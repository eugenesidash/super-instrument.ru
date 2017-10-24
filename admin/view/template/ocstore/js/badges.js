$(function() {
    $("[name='data[position]']").change(function() {
        if ($(this).val() == "random")
            $(".random").removeClass("hidden");
        else
            $(".random").addClass("hidden");
    });

    $("input").on("input", function() {
        livepreview();
    });

    $("#thumb-image img").on("load", function() {
        livepreview();
    });



    //$("[name='data[color]']").on("change", function() {
    //    livepreview();
    //});
    //
    //$("[name='data[size]']").on("change", function() {
    //    livepreview();
    //});
    //
    //$("[name='data[font]']").on("input", function() {
    //    livepreview();
    //});


    $("select").on("change", function() {
        $(".livepreview")
            .css("width", $(".livepreview .imgbadge").width())
            .css("height", $(".livepreview .imgbadge").height());

        if ($("[name='data[position]']").val() == "topright") {
            $('[name="data[badge][left]"]').val("");
            $('[name="data[badge][right]"]').val("0");
            $('[name="data[badge][top]"]').val("0");
            $('[name="data[badge][bottom]"]').val("");
        }

        if ($("[name='data[position]']").val() == "topleft") {
            $('[name="data[badge][left]"]').val("0");
            $('[name="data[badge][right]"]').val("");
            $('[name="data[badge][top]"]').val("0");
            $('[name="data[badge][bottom]"]').val("");
        }

        if ($("[name='data[position]']").val() == "bottomleft") {
            $('[name="data[badge][left]"]').val("0");
            $('[name="data[badge][right]"]').val("");
            $('[name="data[badge][top]"]').val("");
            $('[name="data[badge][bottom]"]').val("0");
        }

        if ($("[name='data[position]']").val() == "bottomright") {
            $('[name="data[badge][left]"]').val("");
            $('[name="data[badge][right]"]').val("0");
            $('[name="data[badge][top]"]').val("");
            $('[name="data[badge][bottom]"]').val("0");
        }

        livepreview();
    });

    $(".badgetop").click(function() {
        if ($("[name='data[position]']").val() == "topright" || $("[name='data[position]']").val() == "topleft")
            $('[name="data[badge][top]"]').val(Number($('[name="data[badge][top]"]').val())-1);
        else
            $('[name="data[badge][bottom]"]').val(Number($('[name="data[badge][bottom]"]').val())+1);

        livepreview();
        return false;
    });

    $(".badgeleft").click(function() {
        if ($("[name='data[position]']").val() == "topright" || $("[name='data[position]']").val() == "bottomright")
            $('[name="data[badge][right]"]').val(Number($('[name="data[badge][right]"]').val())+1);
        else
            $('[name="data[badge][left]"]').val(Number($('[name="data[badge][left]"]').val())-1);

        livepreview();
        return false;
    });

    $(".badgeright").click(function() {
        if ($("[name='data[position]']").val() == "topright" || $("[name='data[position]']").val() == "bottomright")
            $('[name="data[badge][right]"]').val(Number($('[name="data[badge][right]"]').val())-1);
        else
            $('[name="data[badge][left]"]').val(Number($('[name="data[badge][left]"]').val())+1);

        livepreview();
        return false;
    });

    $(".badgebottom").click(function() {
        if ($("[name='data[position]']").val() == "topright" || $("[name='data[position]']").val() == "topleft")
            $('[name="data[badge][top]"]').val(Number($('[name="data[badge][top]"]').val())+1);
        else
            $('[name="data[badge][bottom]"]').val(Number($('[name="data[badge][bottom]"]').val())-1);

        livepreview();
        return false;
    });

    $(".texttop").click(function() {
        if ($("[name='data[position]']").val() == "topright" || $("[name='data[position]']").val() == "topleft")
            $('[name="data[text][top]"]').val(Number($('[name="data[text][top]"]').val())-1);
        else
            $('[name="data[text][bottom]"]').val(Number($('[name="data[text][bottom]"]').val())+1);

        livepreview();
        return false;
    });

    $(".textleft").click(function() {
        if ($("[name='data[position]']").val() == "topright" || $("[name='data[position]']").val() == "bottomright")
            $('[name="data[text][right]"]').val(Number($('[name="data[text][right]"]').val())+1);
        else
            $('[name="data[text][left]"]').val(Number($('[name="data[text][left]"]').val())-1);

        livepreview();
        return false;
    });

    $(".textright").click(function() {
        if ($("[name='data[position]']").val() == "topright" || $("[name='data[position]']").val() == "bottomright")
            $('[name="data[text][right]"]').val(Number($('[name="data[text][right]"]').val())-1);
        else
            $('[name="data[text][left]"]').val(Number($('[name="data[text][left]"]').val())+1);

        livepreview();
        return false;
    });

    $(".textbottom").click(function() {
        if ($("[name='data[position]']").val() == "topright" || $("[name='data[position]']").val() == "topleft")
            $('[name="data[text][top]"]').val(Number($('[name="data[text][top]"]').val())+1);
        else
            $('[name="data[text][bottom]"]').val(Number($('[name="data[text][bottom]"]').val())-1);

        livepreview();
        return false;
    });
});

$( window ).load(function() {
    livepreview();
});

function livepreview() {
    console.log(123);
    $(".livepreview .imgbadge").attr("src", "/image/"+$("#input-image").val());

    $(".livepreview")
        .css("right", ($("[name='data[badge][right]']").val().trim().length > 0? $("[name='data[badge][right]']").val()+"px": ""))
        .css("top", ($("[name='data[badge][top]']").val().trim().length > 0? $("[name='data[badge][top]']").val()+"px": ""))
        .css("left", ($("[name='data[badge][left]']").val().trim().length > 0? $("[name='data[badge][left]']").val()+"px": ""))
        .css("bottom", ($("[name='data[badge][bottom]']").val().trim().length > 0? $("[name='data[badge][bottom]']").val()+"px": ""))
        .css("width", $(".livepreview .imgbadge").width())
        .css("height", $(".livepreview .imgbadge").height());

    $(".livepreview .text")
        .css("right", ($("[name='data[text][right]']").val().trim().length > 0? $("[name='data[text][right]']").val()+"px": ""))
        .css("top", ($("[name='data[text][top]']").val().trim().length > 0? $("[name='data[text][top]']").val()+"px": ""))
        .css("left", ($("[name='data[text][left]']").val().trim().length > 0? $("[name='data[text][left]']").val()+"px": ""))
        .css("bottom", ($("[name='data[text][bottom]']").val().trim().length > 0? $("[name='data[text][bottom]']").val()+"px": ""))
        .css("width", $(".livepreview .imgbadge").width())
        .css("height", $(".livepreview .imgbadge").height())
        .css("color", $("[name='data[color]']").val())
        .css("font-size", $("[name='data[size]']").val()+"pt")
        .css("font-family", $("[name='data[font]']").val());


    if ($("[name='data[badgetext]']").val().trim().length > 0)
        $(".livepreview .text")
            .html($("[name='data[badgetext]']").val().trim())
            .css("-webkit-transform", "rotate("+$("[name='data[angle]']").val().trim()+"deg)")
            .css("-moz-transform", "rotate("+$("[name='data[angle]']").val().trim()+"deg)")
            .css("-o-transform", "rotate("+$("[name='data[angle]']").val().trim()+"deg)")
            .css("-ms-transform", "rotate("+$("[name='data[angle]']").val().trim()+"deg)");

}
