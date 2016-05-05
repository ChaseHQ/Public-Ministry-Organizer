var pubSelShown = false;
$(document).ready(function () {
    $("#main_page").fancyfields({ customScrollBar: true,
    onSelectChange: function (input,text,val) {
        switch(input.attr('id')) {
            case 'cong':
                if (val !== 0) {
                   $(input).fancyfields("disable");
                   $("#congLoader").css('visibility', 'visible');
                   $.getJSON("ajaxInsecure.php?a=getPub&pid=" + val, function (data) {
                       var listOptions = new Array();
                       $.each(data, function (key, value) {
                           listOptions[key] = [value.lastName + ", " + value.firstName, value.id];
                       });
                       $("#publisher").setOptions(listOptions);
                       $(input).fancyfields("enable");
                       $("#congLoader").css('visibility', 'hidden');
                   });
                   if (pubSelShown === false){
                       pubSelShown = true;
                       $("#login_box").animate({height: '265px'},{duration: "slow",
                       done: function(){
                           $("#hidden").css('visibility', 'visible');
                           $(document).keypress(function(e){
                                if (e.which === 13){
                                    $("#loginBtn").click();
                                }
                            });
                       }
                   });
                   }
                }
                break;
        }
    }});
});

function loginPress() {
    $("#loginError").css('visibility','hidden');
    $("#loginBtn").fancyfields("disable");
    $("#congLoader").css('visibility','visible');
    $.ajax({
       datatype:'text',
       url:'ajaxInsecure.php?a=login&pid=' + $("#publisher").val(),
       type: "POST",
       data: {pin: $("#pubPassword").val()}
    }).done(function (result) {
        if (result === "TRUE") {
            window.location.href = "/";
        } else {
            $("#loginError").css('visibility','visible');
            $("#loginBtn").fancyfields("enable");
            $("#congLoader").css('visibility','hidden');
        }
    });
}


