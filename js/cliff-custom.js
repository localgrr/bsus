

(function ($) {

$('#mailchimp-form').ajaxChimp({
    url: 'https://berlinstandupschool.us7.list-manage.com/subscribe/post-json?u=ff2e27ffb240b1033b471eefc&id=98248e4f6e',
    callback: function(resp,x,y) {
        console.log(resp,x,y);
        return false;
    },
    error: function(x,y,z) {
        console.log("err",x,y,z)
    }
});

var modalButtons = $("[data-target='#mailchimp-modal']");

modalButtons.click(function() {

    modalButtons.removeClass('interested');
    $(this).addClass('interested');
    interest = $(this).attr("data-interest");
    $("input[name=interest-array]").removeAttr("checked");
    $("input[value='" + interest + "']").attr("checked", true)

});

$("#any-course").change(function() {
    if($(this).attr("checked")) {
        $("input[name=interest-array]").removeAttr("checked");
        $("input[name=INTEREST]").val("");
    }
});

$("input[name=interest-array]").change(function() {
    if($(this).attr("checked")) {
        $("#any-course").removeAttr("checked");
    }

    var val = "";
    $("input[name=interest-array]").each(function() {
        if($(this).attr("checked")=="checked") val += $(this).val() + ",";
    });
    $("input[name=INTEREST]").val(val);

});

$('#mailchimp').submit(function(){
    var mailchimpform = $(this);

    $.ajax({
        url:mailchimpform.attr('action'),
        type:'POST',
        data:mailchimpform.serialize(),
        success:function(data){
            $("#mailchimp-modal-messages").html('<span class="text-success">' + data + '</span>');
            console.log(data);
        },
        error: function(data) {
            $("#mailchimp-modal-messages").html('<span class="text-error">' + data + '</span>');
            console.log(data);
        }
    });
    return false;
});

$(".navbar li.menu-subscribe, .navbar li.menu-enquire").find("a").attr(
    {
        "data-toggle": "modal",
        "data-target": $(this).attr("href")
    }
);

$(document).ready(function() {
if(document.getElementById("events_posts_array")) var macy = Macy({
    container: '.active-only #events_posts_array',
    trueOrder: false,
    waitForImages: false,
    margin: 24,
    columns: 2,
    breakAt: {
        400: 1
    },
    margin: { 
      x: 20,
      y: 20  
    }
});

$( "a[data-toggle=modal]" ).one( "click", function() {

    $("#enquiry-modal input[name=subject]").val($(this).attr("data-subject"));

});

});

 
})( jQuery );

function copy_text(element) {
    //Before we copy, we are going to select the text.
    var text = document.getElementById(element);
    var selection = window.getSelection();
    var range = document.createRange();
    range.selectNodeContents(text);
    selection.removeAllRanges();
    selection.addRange(range);
    //add to clipboard.
    document.execCommand('copy');
}
