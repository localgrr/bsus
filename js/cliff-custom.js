

(function ($) {

/*$('#mailchimp-form').ajaxChimp({
    url: 'https://berlinstandupschool.us7.list-manage.com/subscribe/post-json?u=ff2e27ffb240b1033b471eefc&id=98248e4f6e',
    callback: function(resp,x,y) {
        console.log(resp,x,y);
        return false;
    },
    error: function(x,y,z) {
        console.log("err",x,y,z)
    }
});*/





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


$(document).ready(function() {

    $(".navbar li.menu-subscribe, .navbar li.menu-enquire").find("a").attr(
    {
        "data-toggle": "modal",
        "data-target": $(this).attr("href")
    }
);

$( "a[data-toggle=modal]" ).one( "click", function() {

    $("#enquiry-modal input[name=subject]").val($(this).attr("data-subject"));

});

modalButtons = $("[href='#mailchimp-modal']");

modalButtons.click(function() {

    modalButtons.removeClass('interested');
    $(this).addClass('interested');
    interest = $(this).attr("data-interest");
    $("input[name=interest-array]").removeAttr("checked");
    $("input[value='" + interest + "']").attr("checked", true)

});

$(".student-register-table input[type=checkbox]").prop("checked", false);
orders_table_check_changes() 

$(".student-register-table .all").click(function() {

    $(this).parents(".student-register-table").find(".check-row").prop('checked', $(this).prop('checked'));
    orders_table_check_changes();

});

$('.student-register-table .check-row').on("change", function(){
    
    orders_table_check_changes();

});

$(".orders-table-toolbar .copy-emails").click(function() {

    copy_text("orders_emails");

}); 

});

function orders_table_check_changes() {

    $(".orders-table-toolbar input[type=number]").val(0);

    var total = 0;
    var vatTotal = 0;
    var emails = [];
    var emailStr = "";
    
    $(".student-register-table tbody tr").each(function() {

        if($(this).find(".check-row").prop("checked")) {

            total += parseInt($(this).find(".total").text());

            emails.push($(this).find(".email").text()); 

        }

        $(".orders-table-toolbar .grand-total").val(total);

        if(total > 0) vatTotal = (total - (total * 0.19)).toFixed(2);

        $(".orders-table-toolbar .grand-total-minus-vat").val(vatTotal);

        emailStr = emails.join("; ");

        $(".orders-table-toolbar .emails").text(emailStr);

    }); 

}


 
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

