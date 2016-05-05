$(function() {
   $("#sch_TabSched").tabs({
       beforeLoad: function (event, ui) {
           ui.panel[0].innerHTML = "<center><img src='/img/loadingBig.gif' /></center>";
       },
       load: function (event, ui) {
           $(".sch_DatePicker").datepicker({
               showOn: 'button', buttonImageOnly: true, buttonImage: '/img/calendar2.gif', constrainInput: true,
               onClose: function (dateText, inst) {
                   var selectedPanel = $("#sch_TabSched div.ui-tabs-panel:visible");
                   var dp = $(selectedPanel).find(".sch_DatePicker")[0];
                   var locId = $(dp).data("locid");
                   var tabs = $("#sch_TabSched");
                   var currentTabIndex = tabs.tabs("option", "active");
                   var tab = $(tabs.data('uiTabs').tabs[currentTabIndex]);
                   tab.find('.ui-tabs-anchor').attr('href', "/modules/Schedule/ScheduleAjax.php?a=getSched&lid=" + locId + "&startDate=" + dateText);
                   // If cached initially. Remove cache then
                   tab.data( "loaded", false);
                   tabs.tabs("load", currentTabIndex);
               }
           });
       }
   });
   $( document ).tooltip({
      track: true,
      items: "[data-publisher],.sch_ImgNotePad",
      content: function() {
          var element = $(this);
          if (element.attr('class') !== 'sch_ImgNotePad') {
              return "<div id='sch_DetailViewContainer'><div id='sch_DetailViewHeader'>" + element.data('publisher') + "</div><span id='sch_detLabel'>Phone: </span><span id='sch_detInfo'><a class='telephone' href='tel:" + element.data("pubphone") + "'>" + element.data("pubphone") + "</a></span><br /><span id='sch_detLabel'>E-Mail: </span><span id='sch_detInfo'>" + element.data("pubemail") + "</span></div>";
          } else {
             return "<div id='sch_ShowNoteViewContainer'><div id='sch_ShowNoteViewTitle'>" + element.data('name') + " - Note</div><div id='sch_ShowNoteViewNote'>" + element.data('note') + "</div></div>";
         }
      }
    });
});

function scheduleMe(shiftId, dateTime) {
    // Make it show loading...
    $("#sch_TabSched div.ui-tabs-panel:visible")[0].innerHTML =  "<center><img src='/img/loadingBig.gif' /></center>";
    $.ajax({
       datatype:'text',
       url:'/modules/Schedule/ScheduleAjax.php?a=scheduleMe',
       type: "POST",
       data: {shiftId: shiftId, dateTime: dateTime} 
    }).done(function (result) {
        switch (result) {
            case '0':
                // Add Error Checking
                break;
            default:
                alert("There was an error scheduling at this time, Please Try again Later.\n" + result);
        }
        $("#sch_TabSched").tabs('load',$("#sch_TabSched").tabs("option","active")); // Reload Current Tab
    });
}

function unscheduleMe(shiftId, dateTime) {
    // Make it show loading...
    $("#sch_TabSched div.ui-tabs-panel:visible")[0].innerHTML =  "<center><img src='/img/loadingBig.gif' /></center>";
    $.ajax({
       datatype:'text',
       url:'/modules/Schedule/ScheduleAjax.php?a=unscheduleMe',
       type: "POST",
       data: {shiftId: shiftId, dateTime: dateTime} 
    }).done(function (result) {
        switch (result) {
            case '0':
                // Add Error Checking
                break;
            default:
                alert("There was an error unscheduling at this time, Please Try again Later.\n" + result);
        }
        $("#sch_TabSched").tabs('load',$("#sch_TabSched").tabs("option","active")); // Reload Current Tab
    });
}

function goToDate(locId, dateText) {
    $("#sch_TabSched div.ui-tabs-panel:visible")[0].innerHTML =  "<center><img src='/img/loadingBig.gif' /></center>";
    var tabs = $("#sch_TabSched");
    var currentTabIndex = tabs.tabs("option", "active");
    var tab = $(tabs.data('uiTabs').tabs[currentTabIndex]);
    tab.find('.ui-tabs-anchor').attr('href', "/modules/Schedule/ScheduleAjax.php?a=getSched&lid=" + locId + "&startDate=" + dateText);
    // If cached initially. Remove cache then
    tab.data( "loaded", false);
    tabs.tabs("load", currentTabIndex);
}

function editNote(ui, shiftId, dateText) {
    if ($("#sch_NotePad").css('display') != 'none') return;
    $("#sch_NotePadBtnSave").val("Save Note");
    $("#sch_NotePadBtnSave").prop('disabled',false);
    $("#sch_NotePadBtnCancel").prop('disabled',false);
    $("#sch_NotePadBtnSave").data({
        shiftid: shiftId,
        datetext: dateText,
        idsuffix: $(ui).data("idsuffix")
    });
    $("#sch_NoteText").val($(ui).data("note"));
    $("#sch_NotePad").css({
       top: $(ui).offset().top + 20,
       left: $(ui).offset().left + 20
    });
    $("#sch_NotePad").show('slow');
}

function saveNote(ui) {
    $("#sch_NotePadBtnSave").val("Saving...");
    $("#sch_NotePadBtnSave").prop('disabled', true);
    $("#sch_NotePadBtnCancel").prop('disabled', true);
    $.ajax({
       datatype:'text',
       url:'/modules/Schedule/ScheduleAjax.php?a=postNote',
       type: "POST",
       data: {
           shiftId: $(ui).data('shiftid'), 
           dateTime: $(ui).data('datetext'),
           note: $("#sch_NoteText").val()
       } 
    }).done(function (result) {
        switch (result) {
            case '0':
                // Query was good update the editbox with new text
                if ($.find("#pubNote-" + $(ui).data('idsuffix')).length === 0 || $("#sch_NoteText").val() === '') {
                    $("#sch_TabSched").tabs('load',$("#sch_TabSched").tabs("option","active")); // Reload Current Tab
                    $("#sch_NotePad").hide('slow');
                    return;
                }
                $("#pubNote-" + $(ui).data('idsuffix')).data({note: $("#sch_NoteText").val()});
                $("#editNote-" + $(ui).data('idsuffix')).data({note: $("#sch_NoteText").val()});
                $("#sch_NotePad").hide('slow');
                break;
            default:
                alert("There was an error posting a note at this time, Please Try again Later.\n" + result);
                $("#sch_NotePad").hide('slow');
        }
    });
}

function cancelNote() {
    $("#sch_NotePad").hide('slow');
}