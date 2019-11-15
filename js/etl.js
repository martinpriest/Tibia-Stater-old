$.ajax({
    url: 'api/adminApi/controller/getWorldList.php',
    type: 'POST',
    contentType: "application/json; charset=utf-8",
    dataType: 'json',
    statusCode: {
        200: function(response) {
            console.log(response);
            localStorage.setItem("worldsInfo", JSON.stringify(response));
            response.forEach(world => {
                $('#etlWorldList').append(`<option value="${world.id}" data-last-operation="${world.lastOperation}">${world.name}</option>`);
            });
            // zmien przyciski zaleznie od pierwszego wyniku w select
            var firstWorldLastOperation = $( "#etlWorldList option:selected" ).attr('data-last-operation');
            if(firstWorldLastOperation == 0 || firstWorldLastOperation == 3) {
                $("#transformButton").attr("disabled", true).removeClass("btn-success").addClass("btn-secondary");
                $("#loadButton").attr("disabled", true).removeClass("btn-success").addClass("btn-secondary");
            } else if(firstWorldLastOperation == 1) {
                $("#transformButton").attr("disabled", false).removeClass("btn-secondary").addClass("btn-success");
                $("#loadButton").attr("disabled", true).removeClass("btn-success").addClass("btn-secondary");
            }
        },
        400: function(response) {
            alert(response.responseJSON.message);
        }
    }
});
// zmien przyciski zaleznie od wyboru w select
$("#etlWorldList").change(function() {
    var selectedWorldLastOperation = $( "#etlWorldList option:selected" ).attr('data-last-operation');
    // ostatnia operacja do nic lub load
    if(selectedWorldLastOperation == 0 || selectedWorldLastOperation == 3) {
        $("#transformButton").attr("disabled", true).removeClass("btn-success").addClass("btn-secondary");
        $("#loadButton").attr("disabled", true).removeClass("btn-success").addClass("btn-secondary");
    // ostatnia operacja to extract
    } else if(selectedWorldLastOperation == 1) {
        $("#transformButton").attr("disabled", false).removeClass("btn-secondary").addClass("btn-success");
        $("#loadButton").attr("disabled", true).removeClass("btn-success").addClass("btn-secondary");
    // ostatnia operacja to transform
    } else if(selectedWorldLastOperation == 2) {
        $("#transformButton").attr("disabled", false).removeClass("btn-secondary").addClass("btn-success");
        $("#loadButton").attr("disabled", true).removeClass("btn-success").addClass("btn-secondary");
    }
})

function extract() {
    var world = $( "#etlWorldList" ).val();
    var onlineListTopic = $('#playersTopic')[0].checked ? 1 : 0;
    var highscoreTopic = $('#highscoresTopic')[0].checked ? 1 : 0;

    var form_data = {
        "idWorld"     : world,
        "onlineListTopic"  : onlineListTopic,
        "highscoreTopic"  : highscoreTopic,
        "guildTopic" : 0
    };

    $(".console").append(`<p>Rozpoczęto ekstrakcje dla ${$( "#etlWorldList option:selected" ).text()}.</p>`);
    $("#etlWorldList").attr("disabled", true);
    $("#extractButton").attr("disabled", true).removeClass("btn-success").addClass("btn-secondary");
    $("#transformButton").attr("disabled", true).removeClass("btn-success").addClass("btn-secondary");
    $("#loadButton").attr("disabled", true).removeClass("btn-success").addClass("btn-secondary");
    $("#etlButton").attr("disabled", true).removeClass("btn-success").addClass("btn-secondary");
    $("#clearButton").attr("disabled", true).removeClass("btn-success").addClass("btn-secondary");

    $.ajax({
        url: 'api/adminApi/controller/etl/extractProcess.php',
        type: 'POST',
        contentType: "application/json; charset=utf-8",
        data: JSON.stringify(form_data),
        dataType: 'json',
        statusCode: {
            200: function(response) {
                $(".console").append(`<p>Zakończono proces ekstracji dla ${$( "#etlWorldList option:selected" ).text()}. Pobrano ${response.fileCreated} plików w czasie ${response.executionTime}s.`);
                $("#etlWorldList").attr("disabled", false);
                $("#extractButton").attr("disabled", false).removeClass("btn-secondary").addClass("btn-success");
                $("#transformButton").attr("disabled", false).removeClass("btn-secondary").addClass("btn-success");
                $("#etlButton").attr("disabled", false).removeClass("btn-secondary").addClass("btn-success");
                $("#clearButton").attr("disabled", false).removeClass("btn-secondary").addClass("btn-success");
                $("#etlWorldList option:selected").attr('data-last-operation', 1);
            },
            400: function(response) {
                alert(response.responseJSON.message);
            },
            500: function(response) {
                console.log(response);
            }
        }
    });
  }

  function transform() {
    var world = $( "#etlWorldList" ).val();

    var form_data = {
        "idWorld"     : world
    };

    $.ajax({
        url: 'api/adminApi/controller/etl/transformProcess.php',
        type: 'POST',
        contentType: "application/json; charset=utf-8",
        data: JSON.stringify(form_data),
        dataType: 'json',
        statusCode: {
            200: function(response) {
                $(".console").append(`<p>Zakończono proces transformacji dla ${$( "#etlWorldList option:selected" ).text()}. Czas wykonania: ${response.executionTime}s.`);
            },
            400: function(response) {
                alert(response.responseJSON.message);
            },
            500: function(response) {
                console.log(response);
            }
        }
    });
  }