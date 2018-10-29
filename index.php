<!DOCTYPE html> 
<html> 
<head> 
    <title>ICRSSL Stat Manager</title> 
    <meta name="viewport" content="width=device-width, initial-scale=1"> 
    <script type="text/javascript" src="https://code.jquery.com/jquery-1.12.4.min.js" integrity="sha256-ZosEbRLbNQzLpnKIkEdrPv7lOy9C27hHQ+Xp8a4MxAQ=" crossorigin="anonymous"></script>
    <script type="text/javascript" src="https://code.jquery.com/mobile/1.4.5/jquery.mobile-1.4.5.min.js"></script>
    <link rel="stylesheet" href="https://code.jquery.com/mobile/1.4.5/jquery.mobile-1.4.5.min.css" />

    <script type="text/javascript" src="_js/myscript.js"></script>
    <link rel="stylesheet" href="_css/main.css" />
</head> 
<body> 
    <?php 
    include "includes/dbfunctions.php";
    $divisions = getDivisions();
    $seasons = getSeasons();
    $currentSeason = date("Y");
    $gameTypes = getGameTypes();
    $fields = getFields();
    ?>
    <div data-role="page" id="home">
        <div data-role="header">
            <!--<a class="ui-btn-left" data-icon="bars" href="#panelMenu">Menu</a>-->
            <h1>ICRSSL Stat Manager</h1>
            <div data-role="navbar">
                <ul>
                    <li><a href="#teams">Teams</a></li>
                    <li><a href="#players">Players</a></li>
                    <li><a href="#fixtures">Fixtures</a></li>
                    <li><a href="#games">Games</a></li>
                </ul>
            </div>
        </div>
        
        <div data-role="content">
            <h3>Notes</h3>
            <div>
                Known bugs/issues:
                <ul>
                    <li>Loading data is sometimes delayed while menus and buttons remain active. TODO: disable forms/buttons and add animation during loading</li>
                    <li>Fixture times are limited to hourly intervals</li>
                    <li>Possibly an issue: fixtures are grouped by date instead of week. Ex. If you have some games on a monday and some games on a wednesday of the same week, those dates will be interpreted as separate weeks.</li>
                </ul>
                Planned features/functionality (ordered by priority):
                <ul>
                    <li><del>Edit gamesheets</del></li>
                    <li><del>Create fixtures</del></li>
                    <li>Manage divisions</li>
                    <li>Manage fields</li>
                    <li>Manage discipine?</li>
                    <li>Postpone fixtures</li>
                    <li>Easier way to input date for fixtures</li>
                    <li>Possible eventual UI overhaul (very low priority)</li>
                </ul>
                Gamesheets that need updating:
                <ul>
                    <li><del>Week 1: Meteor vs C4N</del></li>
                    <li>Week 1: Eagles vs Rams</li>
                </ul>
            </div>
        </div>
    </div>

    <!--Teams page-->
    <div data-role="page" id="teams">
        <div data-role="header">
            <h1>ICRSSL Stat Manager</h1>
            <div data-role="navbar">
                <ul>
                    <li><a href="#teams" class="ui-btn-active ui-state-persist">Teams</a></li>
                    <li><a href="#players">Players</a></li>
                    <li><a href="#fixtures">Fixtures</a></li>
                    <li><a href="#games">Games</a></li>
                </ul>
            </div>
        </div>

        <!--Teams Content-->
        <div data-role="content">
            <h2>Manage Teams</h2>
            <a href="#addTeam" data-rel="popup" data-position-to="window" class="ui-btn ui-btn-inline">New Team</a>
            <a href="#divisions" class="ui-btn ui-btn-inline" disabled>Manage Divisions</a>
            <!--Teams table goes here (populated with js)-->
            <div id="teamsDisplay"></div>
        </div>

        <!--Edit Team popup-->
        <div data-role="popup" id="editTeam" data-theme="a" data-overlay-theme="b" data-dismissible="false" data-transition='pop'>
            <div data-role="content">
                <h2 id="editTeamTitle"></h2>
                <form id="updateTeamForm" method="post">
                    <input type="hidden" name="id" id="editTeamId" value=""></input>
                    <label for="updateTeamName">Team Name: </label>
                    <input type="text" name="updateTeamName" id="updateTeamName" value=""></input>
                    <label for="updateTeamDivision">Division:</label>
                    <select name="updateTeamDivision" id="updateTeamDivision">
                    <?php
                    for ($d = 0; $d < count($divisions); $d++) {
                        echo '<option value="' . $divisions[$d]['division_id'] . '">' . $divisions[$d]['Division'] . '</option>';
                    }
                    ?>
                    </select>
                    <label for="updateTeamGroup">Group:</label>
                    <select name="updateTeamGroup" id="updateTeamGroup">
                    	<option value="1">A</option>
                    	<option value="2">B</option>
                    </select>
                    <label for="updateTeamManager">Manager Name:</label>
                    <input type="text" name="updateTeamManager" id="updateTeamManager" value=""></input>
                    <label for="updateTeamEmail">Manager Email: <span class="error"></span></label>
                    <input type="email" name="updateTeamEmail" id="updateTeamEmail" value=""></input>
                    <label for="updateTeamPhone">Manager Phone:</label>
                    <input type="text" name="updateTeamPhone" id="updateTeamPhone" value=""></input>
                    <label for="updateTeamActive">Active</label>
                    <input type="checkbox" name="updateTeamActive" id="updateTeamActive"></input>
                    <input type="submit" data-inline="true" value="Update" id="updateTeamSubmit">
                    <a href="#" data-rel='back' class="ui-btn ui-corner-all ui-shadow ui-btn-inline ui-btn-b">Go Back</a>
                </form>
            </div>
        </div>

        <!--Success popup-->
        <div data-role="popup" id="success" data-theme="a" data-overlay-theme="b" data-transmission="pop">
            <div data-role="header">
                <h1>Edit Succesful</h1>
            </div>
            <div data-role="main" class="ui-content">
                Success!
            </div>
        </div>

        <!--Failure popup-->
        <div data-role="popup" id="failure" data-theme="a" data-overlay-theme="b" data-transmission="pop">
            <div data-role="header">
                <h1>Edit Failed</h1>
            </div>
            <div data-role="main" class="ui-content">
                Something went wrong, probably David's fault
            </div>
        </div>

        <!--Create Team popup-->
        <div data-role="popup" id="addTeam" data-theme="a" data-overlay-theme="b" data-dismissible="false" data-transition='pop'>
            <div data-role="content" >
                <h2>Add Team</h2>
                <p class="error">* Required</p>
                <form id="addTeamForm" method="post">
                    <label for="addTeamName">Team name: <span class="error">*</span></label>
                    <input type="text" name="addTeamName" id="addTeamName">
                    <label for="addTeamDivision">Division: <span class="error">*</span></label>
                    <select name="addTeamDivision" id="addTeamDivision">
                        <?php
                        for ($d = 0; $d < count($divisions); $d++) {
                                echo "<option value='" . $divisions[$d]['division_id'] . "'>" . $divisions[$d]['Division'] . "</option>";
                        }
                        ?>
                    </select>
                    <label for="addTeamManager">Manager Name:</label>
                    <input type="text" name="addTeamManager" id="addTeamManager">
                    <label for="addTeamEmail">Manager Email:</label>
                    <input type="email" name="addTeamEmail" id="addTeamEmail">
                    <label for="addTeamPhone">Manager Phone:</label>
                    <input type="text" maxlength="10" name="addTeamPhone" id="addTeamPhone">
                    <label for="addTeamActive">Active</label>
                    <input type="checkbox" name="addTeamActive" id="addTeamActive" checked></input>
                    <input type="submit" id="addTeamSubmit" data-inline="true" value="Submit">
                    <a href="#" class="ui-btn ui-corner-all ui-shadow ui-btn-inline ui-btn-b" data-rel="back">Go Back</a>
                </form>
            </div>
        </div>
    </div>

    <!--Players Page-->
    <div data-role="page" id="players">
        <div data-role="header">
            <h1>ICRSSL Stat Manager</h1>
            <div data-role="navbar">
                <ul>
                    <li><a href="#teams">Teams</a></li>
                    <li><a href="#players" class="ui-btn-active ui-state-persist">Players</a></li>
                    <li><a href="#fixtures">Fixtures</a></li>
                    <li><a href="#games">Games</a></li>
                </ul>
            </div>
        </div>

        <div data-role="content">
            <label for="teamRosterSelect">Select Team:</label>
            <form id="addPlayerForm" method="post">
                <select name="teamRosterSelect" id="teamRosterSelect" data-inline="true" ></select>
                <div id="rosterDisplay"></div>
                <ul data-role="listview" data-inset="true" id="addPlayerFormContainer">
                    <li>
                        <fieldset class="ui-grid-b">
                            <div class="ui-block-a">
                                <input type="text" name="addPlayerName" id="addPlayerName"></input>
                            </div>
                            <div class="ui-block-b">
                                <select name="addPlayerJersey" id="addPlayerJersey" data-inline="true">';
                                    <?php 
                                    for ($i = 1; $i < 100; $i++) {
                                            echo '<option value="' . $i . '">' . $i . '</option>';
                                    }
                                    ?>
                                </select>
                            </div>
                            <div class="ui-block-c">
                                <input type="submit" value="Add" id="addPlayerSubmit" data-inline="true"></input>
                            </div>
                        </fieldset>
                    </li>
                </ul>
            </form>
        </div>

        <!--Edit Player popup-->
        <div data-role="popup" id="editPlayer" data-theme="a" data-overlay-theme="b" data-dismissible="false" data-transition='pop'>
            <div data-role="content">
                <h2>Edit Player</h2>
                <form id="updatePlayerForm" method="post">
                    <input type="hidden" name="id" id="updatePlayerId" value=""></input>
                    <label for="updatePlayerName">Name: </label>
                    <input type="text" name="updatePlayerName" id="updatePlayerName" value=""></input>
                    <label for="updatePlayerTeam">Team: </label>
                    <select name="updatePlayerTeam" id="updatePlayerTeam"></select>
                    <label for="updatePlayerJersey">Jersey: </label>
                    <select name="updatePlayerJersey" id="updatePlayerJersey" data-inline="true">';
                        <?php 
                        for ($i = 1; $i < 100; $i++) {
                                echo '<option value="' . $i . '">' . $i . '</option>';
                        }
                        ?>
                    </select>
                    <label for="updatePlayerActive">Active</label>
                    <input type="checkbox" name="updatePlayerActive" id="updatePlayerActive"></input>
                    <input type="submit" data-inline="true" value="Update" id="updatePlayerSubmit">
                    <a href="#" data-rel='back' class="ui-btn ui-corner-all ui-shadow ui-btn-inline ui-btn-b">Cancel</a>
                </form>
            </div>
        </div>
    </div>

    <!--Fixtures Page-->
    <div data-role="page" id="fixtures">
        <div data-role="header">
            <h1>ICRSSL Stat Manager</h1>
            <div data-role="navbar">
                <ul>
                    <li><a href="#teams">Teams</a></li>
                    <li><a href="#players">Players</a></li>
                    <li><a href="#fixtures" class="ui-btn-active ui-state-persist">Fixtures</a></li>
                    <li><a href="#games">Games</a></li>
                </ul>
            </div>
        </div>

        <div data-role="content">
            <h2>Edit Fixtures</h2>
            <label for="fixtureSeason" class="select">Select Season:</label>
            <select name="fixtureSeason" id="fixtureSeason" data-inline="true">
            	<option value='2017'>2017</option>
            	<option value='2018'>2018</option>
            </select>
            <div id="fixturesDisplay"></div>
            <a href="#addFixture" data-rel="popup" data-position-to="window" class="ui-btn ui-btn-inline">New Fixture</a>
        </div>

        <!--Edit Fixture popup-->
        <div data-role="popup" id="editFixture" data-theme="a" data-overlay-theme="b" data-dismissible="false" data-transition='pop'>
            <div data-role="content">
                <h2>Edit Fixture</h2>
                <form id="updateFixtureForm" method="post">
                    <input type="hidden" name="updateFixtureId" id="updateFixtureId" value=""></input>
                    <label for="updateFixtureSeason">Season: </label>
                    <select name="updateFixtureSeason" id="updateFixtureSeason" data-inline="true">
                    	<option value='2017'>2017</option>
            		<option value='2018'>2018</option>
                    <label for="updateFixtureDate">Date: </label>
                    <input type="text" name="updateFixtureDate" id="updateFixtureDate" placeholder="yyyy-mm-dd"></input>
                    <label for="updateFixtureType">Game Type: </label>
                    <select name="updateFixtureType" id="updateFixtureType">
                    <?php 
                        for ($g = 0; $g < count($gameTypes); $g++) {
                            echo '<option value="'.$gameTypes[$g]['id'].'">'.$gameTypes[$g]['name'].'</option>';
                        }
                    ?>
                    </select>
                    <label for="updateFixtureDivision">Division: </label>
                    <select name="updateFixtureDivision" id="updateFixtureDivision">
                    <?php 
                        for ($d = 0; $d < count($divisions); $d++) {
                            echo '<option value="'.$divisions[$d]['division_id'].'">'.$divisions[$d]['Division'].'</option>';
                        }
                    ?>
                    </select>
                    <label for="updateFixtureHomeTeam">Home Team: </label>
                    <select name="updateFixtureHomeTeam" id="updateFixtureHomeTeam"></select>
                    <label for="updateFixtureAwayTeam">Away Team: </label>
                    <select name="updateFixtureAwayTeam" id="updateFixtureAwayTeam"></select>
                    <label for="updateFixtureHour">Kick-off: </label>
                    <select name="updateFixtureHour" id="updateFixtureHour">
                    <?php 
                        for ($t = 0; $t < 24; $t++) {
                            echo '<option value="'.$t.'">';
                            if ($t < 10) {
                                echo '0';
                            } 
                            echo $t . ':00</option>';
                        }
                    ?>
                    </select>
                    <label for="updateFixtureField">Field: </label>
                    <select name="updateFixtureField" id="updateFixtureField">
                    <?php 
                        for ($f = 0; $f < count($fields); $f++) {
                            echo '<option value="'.$fields[$f]['id'].'">'.$fields[$f]['name'].'</option>';
                        }
                    ?>
                    </select>
                    <input type="submit" data-inline="true" value="Update" id="updateFixtureSubmit">
                    <a href="#" data-rel='back' class="ui-btn ui-corner-all ui-shadow ui-btn-inline ui-btn-b">Cancel</a>
                </form>
            </div>
        </div>

        <!--Create Fixture popup-->
        <div data-role="popup" id="addFixture" data-theme="a" data-overlay-theme="b" data-dismissible="false" data-transition='pop'>
            <div data-role="content" >
                <h2>Create Fixture</h2>
                <form id="addFixtureForm" method="post">
                    <label for="addFixtureSeason">Season: </label>
                    <select name="addFixtureSeason" id="addFixtureSeason" data-inline="true">
                        <option value='2017'>2017</option>
            		<option value='2018'>2018</option>
                    </select>
                    <label for="addFixtureDate">Date: </label>
                    <input type="text" data-role="date" name="addFixtureDate" id="addFixtureDate" placeholder="yyyy-mm-dd"></input>
                    <label for="addFixtureType">Game Type: </label>
                    <select name="addFixtureType" id="addFixtureType">
                    <?php 
                        for ($g = 0; $g < count($gameTypes); $g++) {
                            echo '<option value="'.$gameTypes[$g]['id'].'">'.$gameTypes[$g]['name'].'</option>';
                        }
                    ?>
                    </select>
                    <label for="addFixtureDivision">Division: </label>
                    <select name="addFixtureDivision" id="addFixtureDivision">
                    <?php 
                        for ($d = 0; $d < count($divisions); $d++) {
                            echo '<option value="'.$divisions[$d]['division_id'].'">'.$divisions[$d]['Division'].'</option>';
                        }
                    ?>
                    </select>
                    <label for="addFixtureHomeTeam">Home Team: </label>
                    <select name="addFixtureHomeTeam" id="addFixtureHomeTeam"></select>
                    <label for="addFixtureAwayTeam">Away Team: </label>
                    <select name="addFixtureAwayTeam" id="addFixtureAwayTeam"></select>
                    <label for="addFixtureHour">Kick-off: </label>
                    <select name="addFixtureHour" id="addFixtureHour">
                    <?php 
                        for ($t = 0; $t < 24; $t++) {
                            echo '<option value="'.$t.'">';
                            if ($t < 10) {
                                echo '0';
                            } 
                            echo $t . ':00</option>';
                        }
                    ?>
                    </select>
                    <label for="addFixtureField">Field: </label>
                    <select name="addFixtureField" id="addFixtureField">
                    <?php 
                        for ($f = 0; $f < count($fields); $f++) {
                            echo '<option value="'.$fields[$f]['id'].'">'.$fields[$f]['name'].'</option>';
                        }
                    ?>
                    </select>
                    <input type="submit" id="addFixtureSubmit" data-inline="true" value="Submit">
                    <a href="#" class="ui-btn ui-corner-all ui-shadow ui-btn-inline ui-btn-b" data-rel="back">Go Back</a>
                </form>
            </div>
        </div>
    </div>

    <!--Game sheets Page-->
    <div data-role="page" id="games">
        <div data-role="header">
            <h1>ICRSSL Stat Manager</h1>
            <div data-role="navbar">
                <ul>
                    <li><a href="#teams">Teams</a></li>
                    <li><a href="#players">Players</a></li>
                    <li><a href="#fixtures">Fixtures</a></li>
                    <li><a href="#games" class="ui-btn-active ui-state-persist">Games</a></li>
                </ul>
            </div>
        </div>

        <div data-role="content">	
            <div id="gameSheetMode"></div>
            <!--Drop down menu to select season-->
            <div class="ui-grid-a">
                <div class="ui-block-a">
                    <label for="gameSheetSeasonSelect">Select Season:</label>
                    <select name="gameSheetSeasonSelect" id="gameSheetSeasonSelect">
                        <option disabled="disabled">Select season...</option>
                        <?php 
                            for ($s = 0; $s < count($seasons); $s++) {
                                echo '<option value="'.$seasons[$s]['season'].'"';
                                if ($seasons[$s]['season'] === $currentSeason) {
                                    echo ' selected';
                                }
                                echo '>' . $seasons[$s]['season'] . '</option>';
                            }
                        ?>
                    </select>
                </div>
                <div class="ui-block-b">
                    <label for="gameSheetDateSelect">Select Week:</label>
                    <select name="gameSheetDateSelect" id="gameSheetDateSelect"></select>
                </div>
            </div>
            <label for="gameSheetSelect">Select Game:</label>
            <select name="gameSheetSelect" id="gameSheetSelect" data-iconpos="none"></select>
            <!--Gamesheet-->
            <div id="gameSheetTitle" class="ui-grid-a">
                <div class="ui-block-a" style="text-align: right">
                    <span id="gameSheetHomeTeam" teamId=""></span>
                    <span id="gameSheetHomeScore" class="score"></span>
                </div>
                <div class="ui-block-b" style="text-align: left">
                    <span id="gameSheetAwayScore" class="score"></span>
                    <span id="gameSheetAwayTeam" teamId=""></span>
                </div>
            </div>
            <div id="gameDisplay" class="ui-grid-a ui-responsive">
                <input type="hidden" id="gameId" value=""/>
                <input type="hidden" id="homeId" value=""/>
                <input type="hidden" id="awayId" value=""/>
                <div id="homeTeamRoster" class="ui-block-a"></div>
                <div id="awayTeamRoster" class="ui-block-b"></div>
                <button id="gameSheetConfirm" disabled>Submit</button>
            </div>
        </div>
        
        <div data-role="popup" id="confirmGame" data-theme="a" data-overlay-theme="b" data-dismissible="false" data-transition='pop'>
            <div data-role="content">
                Are you sure you want to submit this game Sheet?<br/><br/>
                <button id="submitGameSheet" data-inline="true">Submit</button>
                <a href="#" class="ui-btn ui-corner-all ui-shadow ui-btn-inline ui-btn-b" data-rel="back">Go Back</a>
            </div>
        </div>
    </div>

</body>
</html>