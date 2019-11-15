$.ajax({
    url: 'api/adminApi/controller/user/checkLogin.php',
    type: 'post',
    dataType: 'json',
    statusCode: {
        200: function() {
            $("#menu").empty();
            $("#menu").load("templates/menuLogin.html", function() {
                console.log("Wzytano menu");
                $(function () {
                    $('[data-toggle="popover"]').popover()
                  })
            });
        },
        400: function() {
            console.log("NIE ZALOGOWANY");
        }
    }
})

// Załaduj strone playerów
function loadPlayerSite() {
    localStorage.setItem("activeURL", "players");
    $("#mainContent").empty();
    $("#mainContent").load("templates/playerTable.html", function() {
        // playertable powinno zawierac wszystko oprocz rekordow
        // 1. Wczytaj rekordy z bazy danych
        // 2. Wygeneruj wiersze tabeli
        // 3. Użyj metody DataTable
        $('#playersTable').DataTable({
            stateSave: true
        });
        $('#playersTable').addClass('background-opacity');
    });
}

// Załaduj strone highscores
function loadHighscoresSite(name) {
    localStorage.setItem("activeURL", "highscores");
    $("#mainContent").empty();
    $("#mainContent").text(`HIGHSCORES ${name}`);
}

// Załaduj stronę ETL
function loadETLSite() {
    localStorage.setItem("activeURL", "etl");
    $.ajax({
        url: 'api/adminApi/controller/user/checkLogin.php',
        type: 'post',
        dataType: 'json',
        statusCode: {
            200: function() {
                $("#mainContent").empty();
                $("#mainContent").load("templates/adminPanel/etl.html", function() {
                    console.log("Wczytano panel ETL");
                });
            },
            400: function() {
                $("#mainContent").empty();
                $("#mainContent").text(`Brak dostępu do strony`);
            }
        }
    })
}

// Załaduj stronę Scheduler
function loadSchedulerSite() {
    localStorage.setItem("activeURL", "scheduler");
    $("#mainContent").empty();
    $("#mainContent").text("Scheduleer");
}

// Załaduj stronę Logs
function loadShowLogsSite() {
    localStorage.setItem("activeURL", "showLogs");
    $("#mainContent").empty();
    $("#mainContent").text("Logs");
}

// co ma sie odpalic przy refreshu
$(document).ready( function () {
    //to poleci pod funkcje które są wywołane zależnie od activeUrl w localStorage
    var activeURL = localStorage.getItem("activeURL");
    if(activeURL == null || activeURL == "players") loadPlayerSite();
    else if(activeURL == "highscores") loadHighscoresSite(`axe`);
    
    // Admin
    else if(activeURL == "etl") loadETLSite();
    else if(activeURL == "scheduler") loadSchedulerSite();
    else if(activeURL == "showLogs") loadShowLogsSite();
});

function logout()
{
    var logout = $.post('api/adminApi/controller/user/logout.php');
    logout.done(function()
    {
        localStorage.setItem("activeURL", "players");
        location.reload();
    }, "json");
}

function login()
{
    var login = $("input[name*='login']").val();
    var password = $("input[name*='password']").val();

    var form_data = {
        "login"     : login,
        "password"  : password
    };

    $.ajax({
        url: 'api/adminApi/controller/user/login.php',
        type: 'POST',
        contentType: "application/json; charset=utf-8",
        data: JSON.stringify(form_data),
        dataType: 'json',
        statusCode: {
            200: function() {
                location.reload();
            },
            400: function(response) {
                alert(response.responseJSON.message);
            }
        }
    });
}





    //window.history.pushState("a", "Title", "/adminPanel"); // rozkminic jak by tu móc wysyłać linkiem aktualny content mimo że jest SPA