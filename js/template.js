function getWorldList() {
    $.ajax({
        url: 'api/clientApi/controller/getWorldList.php',
        type: 'post',
        dataType: 'json',
        statusCode: {
            200: function(response) {
                console.log(response);
                $("#world-list").empty();
                response.forEach(world => {
                    let $world = $(`<option id=${world.id}">${world.name}</option>`);
                    $("#world-list").append($world);
                });
            },
            400: function(error) {
                console.log(error);
            }
        }
    })
}
function loadMenu(){
    $.ajax({
        url: 'api/adminApi/controller/user/checkLogin.php',
        type: 'post',
        dataType: 'json',
        statusCode: {
            200: function() {
                $("#menu").empty();
                $("#menu").load("templates/menuLogin.html", function() {
                    getWorldList();
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
}

function ConvertToCSV(objArray) {
    var array = typeof objArray != 'object' ? JSON.parse(objArray) : objArray;
    var str = '';

    for (var i = 0; i < array.length; i++) {
        var line = '';
        for (var index in array[i]) {
            if (line != '') line += ','

            line += array[i][index];
        }

        str += line + '\r\n';
    }

    return str;
}

function download(filename, text) {
    var element = document.createElement('a');
    element.setAttribute('href', 'data:text/plain;charset=utf-8,' + encodeURIComponent(text));
    element.setAttribute('download', filename);
  
    element.style.display = 'none';
    document.body.appendChild(element);
  
    element.click();
  
    document.body.removeChild(element);
  }

function drawPlayerTable(data) {
    var table = $("<table id='playersTable' class='table table-bordered table-hover'>");
    var theader = $("<thead class='thead-dark'><tr><th>Nazwa</th><th>Profesja</th><th>Lvl</th><th>Miasto</th><th>PACC</th></tr></thead>");
    var tbody = $("<tbody>");
    $("#mainContent").append(table);
    table.append(theader);
    table.append(tbody);

    if(!("message" in data)) {
        data.forEach(record => {
            let row = $(`<tr>
                            <td data-id-player='${record.idPlayer}'
                            data-container="body"
                            data-toggle="popover"
                            data-placement="left"
                            data-title="Last update:"
                            data-content="${record.dayOfMonth}.${record.month}.${record.year}"
                            data-trigger="hover">
                                <a href='#'>${record.name}</a>
                            </td>
                            <td>${record.vocation}</td>
                            <td>${record.level}</td>
                            <td>${record.residence}</td>
                            <td>${record.status ? "Premium" : "Free"}</td>
                        </tr>`);
            tbody.append(row);
        });

        // to CSV
        let button = $("<button class='btn btn-primary' id='convertButton'>Export</button>");
        $("#mainContent").append(button);
        button.click(function () {
            let csvString = ConvertToCSV(data);
            download("data.csv", csvString);
        })

        $('#playersTable').DataTable({
            stateSave: true
        });
        $('#playersTable').addClass('background-opacity');
        $(function () {
            $('[data-toggle="popover"]').popover()
          })
    } else alert("Przeprowadz ETL dla wybranego swiata");    
}


// Załaduj strone playerów
function loadPlayerSite() {
    localStorage.setItem("activeURL", "players");
    $("#mainContent").empty();
    $("#mainContent").load("templates/playerTable.html", function() {
        // dane do wyslania
        var world = $( "#world-list" ).val();
        var form_data = {
            "idWorld"     : world
        };
        console.log(world)
        // 1. Wczytaj rekordy z bazy danych
        $.ajax({
            url: 'api/clientApi/controller/playerTransaction/getAllPlayerTransaction.php',
            type: 'POST',
            contentType: "application/json; charset=utf-8",
            data: JSON.stringify(form_data),
            dataType: 'json',
            statusCode: {
                200: function(response) {
                    console.log(response);
                    $("#mainContent").empty();
                    drawPlayerTable(response);
                },
                400: function(response) {
                    $("#mainContent").empty();
                    $("#mainContent").text(response.responseJSON.message);
                    alert(response.responseJSON.message);
                },
                500: function(response) {
                    console.log(response);
                    $("#mainContent").empty();
                    $("#mainContent").text(response);
                }
            }
        });
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

$.when(loadMenu()).then(function () {
    $.when(getWorldList()).then(function() {
        $(document).ready( function () {
            //to poleci pod funkcje które są wywołane zależnie od activeUrl w localStorage
            var activeURL = localStorage.getItem("activeURL");
            if(activeURL == null || activeURL == "players") loadPlayerSite();
            else if(activeURL == "highscores") loadHighscoresSite(`axe`);
            
            // Admin
            else if(activeURL == "etl") loadETLSite();
            else if(activeURL == "scheduler") loadSchedulerSite();
            else if(activeURL == "showLogs") loadShowLogsSite();

            $( "#world-list" ).change(function() {
                var activeURL = localStorage.getItem("activeURL");
                if(activeURL == null || activeURL == "players") loadPlayerSite();
                else if(activeURL == "highscores") loadHighscoresSite(`axe`);
            });
        });
    })
})

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