// date picker
$(function() {
  $("#date").datepicker( {
      changeMonth: true,
      showButtonPanel: true,
      dateFormat: "MM yy",
      minDate: "0m",
      maxDate: "6m",
      onClose: function(dateText, inst) {
            $(this).datepicker('setDate', new Date(inst.selectedYear, inst.selectedMonth, 1));
      }
  });
  $("#depart_date").datepicker( {
      dateFormat: "yy-mm-dd",
      minDate: 0
  });
  $("#return_date").datepicker( {
      dateFormat: "yy-mm-dd",
      minDate: 0
  });
});

// Loading
$(function() {
  $("#loading").css("visibility", "hidden")
  $("#search").closest("form").submit(function() {
    $(".flights_container").fadeOut()
    $("#error").fadeOut()
    $("#loading").css("visibility", "visible").hide().fadeIn()
  })
});

$(document).ready(function() {
  $(".flights_container").hide().fadeIn()
  $("#error").hide().fadeIn()
});


// Amadeus autocomplete
$(function() {
    $("#origin").autocomplete( {
      source: function(request, response) {
        $.ajax({
          url: "https://api.sandbox.amadeus.com/v1.2/airports/autocomplete",
          dataType: "json",
          data: {
            apikey: "AMADEUS_API_KEY",
            term: request.term
          },
          success: function(data) {
            response(data);
          }
        });
      }
    });

    $("#destination").autocomplete ({
      source: function(request, response) {
        $.ajax({
          url: "https://api.sandbox.amadeus.com/v1.2/airports/autocomplete",
          dataType: "json",
          data: {
            apikey: "AMADEUS_API_KEY",
            term: request.term
          },
          success: function(data) {
            response(data);
          }
        });
      }
    });
});
