$(document).on("pagebeforeshow", "#teams", function() {
    buildTeamsDisplay();
    buildTeamEdit();
    addTeamForm();
});

//get Teams table, place in teamsDisplay div
function buildTeamsDisplay() {
    $.ajax({
        type: "GET",
        url: "../includes/getTeams.php",
        success: function(html) {
            //if ajax successful, the response (html) is placed into the div with id teamsDisplay
            $("#teamsDisplay").html(html);
            //necessary for the div changes to appear
            $("#teamsDisplay").trigger("create");
        }
    });
}

//give Teams table edit buttons functionality
function buildTeamEdit() {
    $("#teamsDisplay").on("click", ".team", function() {
        var id = $(this).attr("id");
        $.ajax({
            type: "GET",
            url: "includes/getTeam.php?id=" + id,
            success: function(data) {
                var team = JSON.parse(data);
                var active = team[0].active;
                $("#editTeamId").val(id);
                $("#editTeamTitle").html("Edit " + team[0].team_name);
                $("#updateTeamName").val(team[0].team_name);
                $("#updateTeamDivision").val(team[0].division_id).selectmenu("refresh");
                $("#updateTeamGroup").val(team[0].group_id).selectmenu("refresh");
                $("#updateTeamManager").val(team[0].manager_name);
                $("#updateTeamEmail").val(team[0].manager_email);
                $("#updateTeamPhone").val(team[0].manager_phone);
                if (active === "1") {
                    $("#updateTeamActive").prop("checked", true).checkboxradio("refresh");
                } else {
                    $("#updateTeamActive").prop("checked", false).checkboxradio("refresh");
                }
                $("#editTeam").trigger("create");
            }
        });
        $("#editTeam").popup("open");
        //form submission
        $("#updateTeamForm").submit(function(event) {
            event.preventDefault();
            event.stopImmediatePropagation();
            var updateTeamData = $("#updateTeamForm").serialize();
            //$.post is shorthand for ajax with type: 'POST'
            $.post("../includes/updateTeam.php", updateTeamData, function(response) {
                        $("#editTeam").popup("close");
                        buildTeamsDisplay();
                        alert(response);
                });
        });
    });
}

//form functionality for add team
function addTeamForm() {
	$("#addTeamForm").submit(function(event) {
		event.preventDefault();
		event.stopImmediatePropagation();
		var addTeamData = $("#addTeamForm").serialize();
		$.post("../includes/insertTeam.php", addTeamData, function(response) {
			$("#addTeam").popup("close");
			buildTeamsDisplay();
			//$("#success").popup("open");
            alert(response);
		});
	});
}

$(document).on("pagebeforeshow", "#fixtures", function() {
	var selectedSeason = $("#fixtureSeason").val();
	buildFixturesDisplay(selectedSeason);
    	buildFixtureTeamSelect();
	buildFixtureEdit();
	$("#fixtureSeaosn").change(function () {
		var id = $(this).val();
		$("#fixturesDisplay").html("").trigger("create");
		if (id) {
			buildFixturesDisplay(id);
		}
	});
    	addFixtureForm(selectedSeason);
});

//pull fixtures into fixturesDisplay
function buildFixturesDisplay(season) {
	$.ajax({
		type: "GET",
		url: "../includes/getFixtures.php?season="+season,
		success: function(html) {
			$("#fixturesDisplay").html(html).trigger("create");
		}
	});
}

//populate team selects on fixtures page
function buildFixtureTeamSelect() {
	$.ajax({
		type: "GET",
		url: "../includes/getTeamList.php",
		success: function(html) {
            $("#updateFixtureHomeTeam").html(html);
            $("#updateFixtureAwayTeam").html(html);
            $("#addFixtureHomeTeam").html(html);
            $("#addFixtureAwayTeam").html(html);
		}
	});
}

//populate fixture edit form with existing info, form submission
function buildFixtureEdit() {
	$("#fixturesDisplay").on("click", ".fixture", function() {
		var fixture_id = $(this).attr("id");
		$.ajax({
			type: "GET",
			url: "../includes/getFixture.php?id="+fixture_id,
			success: function(data) {
				var fixture = JSON.parse(data);
				$("#updateFixtureId").val(fixture[0].id);
				$("#updateFixtureSeason").val(fixture[0].season).selectmenu("refresh");
				$("#updateFixtureType").val(fixture[0].type).selectmenu("refresh");
				$("#updateFixtureDate").val(fixture[0].date);
				$("#updateFixtureDivision").val(fixture[0].division).selectmenu("refresh");
				$("#updateFixtureHomeTeam").val(fixture[0].home).selectmenu("refresh");
				$("#updateFixtureAwayTeam").val(fixture[0].away).selectmenu("refresh");
				$("#updateFixtureHour").val(fixture[0].hour).selectmenu("refresh");
				$("#updateFixtureField").val(fixture[0].field).selectmenu("refresh");
			}
		});
		$("#editFixture").trigger("create").popup("open");
		//form submission
		$("#updateFixtureForm").submit(function(event) {
			event.preventDefault();
			event.stopImmediatePropagation();
			var updateFixtureData = $("#updateFixtureForm").serialize();
			$.post("../includes/updateFixture.php", updateFixtureData, function(response) {
					$("#editFixture").popup("close");
					buildFixturesDisplay($("#fixtureSeason").val());
					alert(response);
			});
		});
	});
}

//create fixture form submission
function addFixtureForm(selectedSeason) {
    $("#addFixtureForm").submit(function(event) {
        event.preventDefault();
        event.stopImmediatePropagation();
        var addFixtureData = $("#addFixtureForm").serialize();
        $.post("../includes/insertFixture.php", addFixtureData, function(response) {
            $("#addFixture").popup("close");
            buildFixturesDisplay(selectedSeason);
            alert(response);
        });
    });
}

$(document).on("pagebeforeshow", "#games", function() {
    //get dates for selected season
    buildFixtureDateSelect($("#gameSheetSeasonSelect").val());
    //clear date and game select, clear rosters and hide title
    $("#gameSheetDateSelect").val("").selectmenu("refresh");
    $("#gameSheetSelect").val("").selectmenu("refresh");
    $("#submitGameSheet").hide();
    $("#gameSheetTitle").hide();
    $("#gameSheetMode").hide();
    $("#homeTeamRoster").html("");
    $("#awayTeamRoster").html("");
    $("#gameSheetSeasonSelect").change(function() {
        var season = $(this).val();
        buildFixtureDateSelect(season);
        $("#gameSheetDateSelect").val("");
    });
    $("#gameSheetDateSelect").change(function() {
        var date = $(this).val();
        buildGameSelect(date);
        $("#gameSheetSelect").val("").selectmenu("refresh");
    });
    $("#gameSheetSelect").change(function() {
        $("#gameSheetTitle").hide();
        $("#homeTeamRoster").html("");
        $("#awayTeamRoster").html("");
        $("#gameSheetMode").hide();
        var gameId = $(this).val();
        $.ajax({
            type:"GET",
            url: "../includes/getFixture.php?id="+gameId
        }).done(function(data) {
            var fixture = JSON.parse(data);
            var homeId = parseInt(fixture[0].home);
            var awayId = parseInt(fixture[0].away);
            $("#gameSheetHomeTeam").attr("teamId", homeId);
            $("#gameSheetAwayTeam").attr("teamId", awayId);
            var homeScore = parseInt(fixture[0].homeScore);
            var awayScore = parseInt(fixture[0].awayScore);
            $("#gameSheetHomeTeam").html(fixture[0].homeName);
            $("#gameSheetAwayTeam").html(fixture[0].awayName);
            $("#gameSheetHomeScore").html(homeScore);
            $("#gameSheetAwayScore").html(awayScore);
            $("#gameSheetTitle").show();
            $("#submitGameSheet").show();
            //build game sheet
            buildGameSheetRoster(homeId, "#homeTeamRoster");
            buildGameSheetRoster(awayId, "#awayTeamRoster");
            $("#submitGameSheet").prop("disabled", false);
            $("#gameSheetConfirm").prop("disabled", false);
            if (fixture[0].complete === "1") {
                //CODE FOR HANDLING GAME SHEET EDIT
                gameSheetEdit(gameId);
            } else if (fixture[0].complete === "0"){
                //CODE FOR HANDLING FIRST GAME SHEET FILL
                $("#gameSheetMode").html("FILL MODE").show();
            }
        });
    });
    //click anywhere on player that is not an input
    $("#gameDisplay").on("click", ".gameSheetPlayer", function(event) {
        var target = $(event.target);
        //this code doesnt remove the functionality of the actual checkbox
        if (!target.is("input")) {
            var checkbox = $(this).find(".played");
            checkbox.prop("checked", !checkbox.prop("checked"));
        }
    });
    //update scores when adding goals
    updateScores();
    submitGameSheet();
    $("#gameSheetConfirm").click(function() {
       $("#confirmGame").popup("open"); 
    });
});

function gameSheetEdit(gameId) {
    //prefill game sheet
    $.ajax({
        type: "GET",
        url: "../includes/getGameSheet.php?gameId="+gameId,
        success: function(data) {
            var gameSheet = JSON.parse(data);
            $(gameSheet).each(function() {
                var id = this.player;
                var goals = parseInt(this.goals);
                var yellow = parseInt(this.yellow);
                var red = parseInt(this.red);
                $(".gameSheetPlayer").each(function() {
                    var player = $(this);
                    var playerId = player.attr("id");
                    if (id === playerId) {
                        player.find(".goals").val(goals);
                        player.find(".yellow").val(yellow);
                        player.find(".red").val(red);
                        player.find(".played").prop("checked", true).checkboxradio("refresh");
                        player.attr("update", true);
                    }
                });
            });
        }
    });
    $("#gameSheetMode").html("EDIT MODE").show();
}

function submitGameSheet() {
    $("#submitGameSheet").on("click", function() {
        $("#confirmGame").popup("close"); 
        var gameId = parseInt($("#gameSheetSelect").val()); 
        var homeId = parseInt($("#gameSheetHomeTeam").attr("teamId"));
        var awayId = parseInt($("#gameSheetAwayTeam").attr("teamId"));
        var homeScore = parseInt($("#gameSheetHomeScore").html());
        var awayScore = parseInt($("#gameSheetAwayScore").html());
        //if editing game sheet, sort players into insert/update or delete arrays and submit
        if ($("#gameSheetSelect option:selected").hasClass("complete")) {
            var gameSheetInsertUpdateArray = [];
            var gameSheetDeleteArray = [];
            $("#homeTeamRoster").find($(".gameSheetPlayer")).each(function() {
                var played = $(this).find(".played").is(":checked");
                if (played) {
                    var playerId = $(this).attr("id");
                    var goals = $(this).find(".goals").val();
                    var yellow = $(this).find(".yellow").val();
                    var red = $(this).find(".red").val();
                    gameSheetInsertUpdateArray.push([gameId, playerId, homeId, goals, yellow, red]);
                } else {
                    var playerId = $(this).attr("id");
                    gameSheetDeleteArray.push([gameId, playerId]);
                }
            }); 
            $("#awayTeamRoster").find($(".gameSheetPlayer")).each(function() {
                var played = $(this).find(".played").is(":checked");
                if (played) {
                    var playerId = $(this).attr("id");
                    var goals = $(this).find(".goals").val();
                    var yellow = $(this).find(".yellow").val();
                    var red = $(this).find(".red").val();
                    gameSheetInsertUpdateArray.push([gameId, playerId, awayId, goals, yellow, red]);
                } else {
                    var playerId = $(this).attr("id");
                    gameSheetDeleteArray.push([gameId, playerId]);
                }
            }); 
            updateGameFixture(gameId, homeScore, awayScore);
            updateGameSheet(JSON.stringify(gameSheetInsertUpdateArray), JSON.stringify(gameSheetDeleteArray));
        } else {
            //first time filling game sheet, insert players into game table
            var homeGameArray = [];
            var awayGameArray = [];
            $("#homeTeamRoster").find(".gameSheetPlayer").each(function() {
                var playerId = $(this).attr("id");
                var goals = $(this).find(".goals").val();
                var yellow = $(this).find(".yellow").val();
                var red = $(this).find(".red").val();
                var played = $(this).find(".played").is(":checked");
                if (played) {
                    homeGameArray.push([gameId, playerId, homeId, goals, yellow, red]);
                }
            });
            $("#awayTeamRoster").find(".gameSheetPlayer").each(function() {
                var playerId = $(this).attr("id");
                var goals = $(this).find(".goals").val();
                var yellow = $(this).find(".yellow").val();
                var red = $(this).find(".red").val();
                var played = $(this).find(".played").is(":checked");
                if (played) {
                    awayGameArray.push([gameId, playerId, awayId, goals, yellow, red]);
                }
            });
            //alert("Home: " + homeId + "\nScore: " + homeScore + "\nAway: " + awayId + "\nScore: " + awayScore);
            //update Fixture
            updateGameFixture(gameId, homeScore, awayScore);
            //insert stats into game
            insertIntoGame(JSON.stringify(homeGameArray), JSON.stringify(awayGameArray));
        }
        location.reload();
    });
}

function updateGameSheet(insertUpdateArray, deleteArray) {
    $.ajax({
        type: "POST",
        dataType: "json",
        url: "../includes/updateGameSheet.php",
        data: {insertUpdateArray: insertUpdateArray, deleteArray: deleteArray},
        success: function(response) {
            alert(response);
        }
    });
}

function updateScores() {
    $("#homeTeamRoster").on("change", ".goals", function() {
        var homeScore = 0;
        $("#homeTeamRoster").find(".goals").each(function() {
            homeScore += parseInt($(this).val());
        });
        $("#gameSheetHomeScore").html(homeScore);
    });
    $("#awayTeamRoster").on("change", ".goals", function() {
        var awayScore = 0;
        $("#awayTeamRoster").find(".goals").each(function() {
            awayScore += parseInt($(this).val());
        });
        $("#gameSheetAwayScore").html(awayScore);
    });
}

function updateGameFixture(gameId, homeScore, awayScore) {
    $.ajax({
        type: "POST",
        url: "../includes/updateGameFixture.php",
        data: {gameId: gameId, homeScore: homeScore, awayScore: awayScore},
        success: function(response) {
            alert(response);
        }
    });
}

function insertIntoGame(homeGameArray, awayGameArray) {
    $.ajax({
        type: "POST",
        datatype: "json",
        url: "../includes/insertGame.php",
        data: {homeGameArray: homeGameArray, awayGameArray: awayGameArray},
        success: function(response) {
            alert(response);
            location.reload();
        }
    });
}

function buildFixtureDateSelect(season) {
    $.ajax({
        type: "GET",
        url: "../includes/getFixtureDates.php?season="+season,
        success: function(html) {
            $("#gameSheetDateSelect").html(html).trigger("create");
        }
    });
}

function buildGameSheetRoster(teamId, selector) {
	$.ajax({
		type: "GET",
		url: "../includes/getGameRoster.php?teamId="+teamId,
		success: function(html) {
            $(selector).html(html);
            $(selector).trigger("create");
		}
	});
}

function buildGameSelect(date) {
    $.ajax({
        type: "GET",
        url: "../includes/getGames.php?date="+date,
        success: function(html) {
            $("#gameSheetSelect").html(html).trigger("create");
        }
    });
}

$(document).on("pagebeforeshow","#players",function() {
	$("#rosterDisplay").hide();
	$("#addPlayerFormContainer").hide();
	buildTeamSelect();
	$("#teamRosterSelect").val("").selectmenu("refresh");
	buildPlayerEdit();
	$("#teamRosterSelect").change(function () {
		var id = $(this).val();
		$("#rosterDisplay").html("").trigger("create");
		if (id) {
			buildRosterDisplay(id);
			$("#addPlayerFormContainer").show(); 
		}
	});
	addPlayerForm();
});

function buildTeamSelect() {
	$.ajax({
		type: "GET",
		url: "../includes/getTeamList.php",
		success: function(html) {
			$("#teamRosterSelect").html(html);
			$("#updatePlayerTeam").html(html);
		}
	});
}

function buildPlayerEdit() {
	$("#rosterDisplay").on("click", ".player", function() {
		var id = $(this).attr("id");
		var name = $(this).find(".playerName").html();
		var team = $("#teamRosterSelect").val();
		var jersey = $(this).find(".playerJersey").html();
		var active = $(this).find(".playerActive").attr("playerActive");
		$("#updatePlayerId").val(id);
		$("#updatePlayerName").val(name);
		$("#updatePlayerTeam").val(team).selectmenu("refresh");
		$("#updatePlayerJersey").val(jersey).selectmenu("refresh");
		if (active) {
			$("#updatePlayerActive").prop("checked", true).checkboxradio("refresh");
		} else {
			$("#updatePlayerActive").prop("checked", false).checkboxradio("refresh");
		}
		$("#editPlayer").trigger("create");
		$("#editPlayer").popup("open");
		//form submission
		$("#updatePlayerForm").submit(function(event) {
			event.preventDefault();
			event.stopImmediatePropagation();
			var updatePlayerData = $("#updatePlayerForm").serialize();
			$.post("../includes/updatePlayer.php", updatePlayerData, function(response) {
					$("#editPlayer").popup("close");
					buildRosterDisplay(team);
					alert(response);
			});
		});
	});
}

function buildRosterDisplay(id) {
	$.ajax({
		type: "GET",
		url: "../includes/getRoster.php?id=" + id,
		success: function(html) {
			$("#rosterDisplay").html(html).trigger("create").show();
		}
	});
}

function addPlayerForm() {
	$("#addPlayerForm").submit(function(event) {
        var team = $("#teamRosterSelect").val();
		event.preventDefault();
		event.stopImmediatePropagation();
		var addPlayerData = $("#addPlayerForm").serialize();
		$.post("../includes/insertPlayer.php", addPlayerData, function(response) {
				$("#addPlayerForm").find("#addPlayerName").val("");
				$("#addPlayerForm").find("#addPlayerJersey").val("").selectmenu("refresh");
				buildRosterDisplay(team);
				alert(response);
		});
	});
}
