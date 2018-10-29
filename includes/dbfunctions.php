<?php

/*
 * File that contains all database queries
 */
require 'dbconfig.php';

function simpleSelect($sql) {
	try {
        $ds = "mysql:host=" . DBSERVER . ";dbname=" . DBNAME;
        //Step 2 - establish database connection
        $pdo = new PDO($ds, DBUSERNAME, DBPASSWORD);
        //Step 3 - execute an SQL statement
        $stmt = $pdo->prepare($sql);
        $stmt->execute();
        //Step 4 - process the query results
        //return the array of results
        return $stmt->fetchAll();
    } catch (Exception $ex) {
        echo "DB Exception: " . $ex->getMessage();
        return null;
    } finally {
        //Step 5 - close your database connection
        $pdo = null;
    }
}

function preparedSelect($sql, $params) {
	try {
        $ds = "mysql:host=" . DBSERVER . ";dbname=" . DBNAME;
        //Step 2 - establish database connection
        $pdo = new PDO($ds, DBUSERNAME, DBPASSWORD);
        //Step 3 - execute an SQL statement
        $stmt = $pdo->prepare($sql);
        $stmt->execute($params);
        //Step 4 - process the query results
        //return the array of results
        return $stmt->fetchAll();
    } catch (Exception $ex) {
        echo "DB Exception: " . $ex->getMessage();
        return null;
    } finally {
        //Step 5 - close your database connection
        $pdo = null;
    }
}

function preparedMultipleInsertUpdateDelete($sql, $data) {
    try {
		$ds = "mysql:host=" . DBSERVER . ";dbname=" . DBNAME;
		$pdo = new PDO($ds, DBUSERNAME, DBPASSWORD);
		$pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
        $pdo->beginTransaction();
		$stmt = $pdo->prepare($sql);
        foreach ($data as $row) { 
            $stmt->execute($row);
            $stmt->closeCursor();
        }
        $pdo->commit();
		$count = $stmt->rowCount();
		if ($count > 0) {
			return true;
		}
	} catch(PDOException $e) {
		echo $e->getMessage();
	} finally {
		$pdo = null;
	}
}

function getSeasons() {
	$sql = "SELECT DISTINCT season FROM Fixture";
	return simpleSelect($sql);
}

function getCurrentSeason() {
	$sql = "SELECT MAX(season) current_season FROM Fixture";
	return simpleSelect($sql);
}

function getDivisions() {
	$sql = "SELECT d.division_id, d.division_name 'Division', COUNT(*) 'numTeams'
			FROM Team t
			JOIN Division d
			ON t.division_id = d.division_id
			GROUP BY Division
			ORDER BY d.division_id ASC";
	return simpleSelect($sql);
}

function getTeam($id) {
	$sql = "SELECT team_name, division_id, group_id, manager_name, manager_email, manager_phone, active FROM Team  WHERE team_id=?";
    $params = array($id);
	return preparedSelect($sql, $params);
}

function getTeams_All($division) {
	$sql = "SELECT team_id, team_name 'Team', manager_name 'Manager', manager_email 'Email', manager_phone 'Phone', active, group_id 'Group'
			FROM Team t
			WHERE division_id = ?
			ORDER BY active DESC, Team";
    $params = array($division);
	return preparedSelect($sql, $params);
}

function getTeams($division) {
	$sql = "SELECT team_id, team_name 'Team', manager_name 'Manager', manager_email 'Email', manager_phone 'Phone', active
			FROM Team
			WHERE division_id = ? AND active=true
			ORDER BY active, Team";
    $params = array($division);
	return preparedSelect($sql, $params);
}

function insertTeam($name, $division, $manager, $email, $phone, $active) {
	try {
		$ds = "mysql:host=" . DBSERVER . ";dbname=" . DBNAME;
		$pdo = new PDO($ds, DBUSERNAME, DBPASSWORD);
		$pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
		$sql = "INSERT INTO Team (team_name, division_id, manager_name, manager_email, manager_phone, active) VALUES (?,?,?,?,?,?)";
		$stmt = $pdo->prepare($sql);
		$stmt->bindParam(1, $name, PDO::PARAM_STR);
		$stmt->bindParam(2, $division, PDO::PARAM_INT);
		$stmt->bindParam(3, $manager, PDO::PARAM_STR);
		$stmt->bindParam(4, $email, PDO::PARAM_STR);
		$stmt->bindParam(5, $phone, PDO::PARAM_STR);
		$stmt->bindParam(6, $active, PDO::PARAM_BOOL);
		$stmt->execute();
		$count = $stmt->rowCount();
		if ($count > 0) {
			return true;
		}
	} catch(PDOException $e) {
		echo $e->getMessage();
	} finally {
		$pdo = null;
	}
}

function updateTeam($name, $division, $group, $manager, $email, $phone, $active, $id) {
	try {
		$ds = "mysql:host=" . DBSERVER . ";dbname=" . DBNAME;
		$pdo = new PDO($ds, DBUSERNAME, DBPASSWORD);
		$pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
		$sql = "UPDATE Team SET team_name=?, division_id=?, group_id=?, manager_name=?, manager_email=?, manager_phone=?, active=? WHERE team_id=?";
		$stmt = $pdo->prepare($sql);
		$stmt->bindParam(1, $name, PDO::PARAM_STR);
		$stmt->bindParam(2, $division, PDO::PARAM_INT);
		$stmt->bindParam(3, $group, PDO::PARAM_INT);
		$stmt->bindParam(4, $manager, PDO::PARAM_STR);
		$stmt->bindParam(5, $email, PDO::PARAM_STR);
		$stmt->bindParam(6, $phone, PDO::PARAM_STR);
		$stmt->bindParam(7, $active, PDO::PARAM_BOOL);
		$stmt->bindParam(8, $id, PDO::PARAM_INT);
		$stmt->execute();
		$count = $stmt->rowCount();
		if ($count > 0) {
			return true;
		}
	} catch(PDOException $e) {
		echo $e->getMessage();
	} finally {
		$pdo = null;
	}
}

//get all fixtures of a given week
function getDateFixtures($date) {
    $sql = "SELECT f.fixture_id 'id', f.division_id 'division', f.date, t1.team_name 'Home', f.home_score 'Home Score', f.away_score 'Away Score', t2.team_name 'Away', Field.field_name 'Field', f.hour 'Time', complete
			FROM Fixture f
			LEFT JOIN Team t1
			ON f.home_team_id=t1.team_id
			LEFT JOIN Team t2
			ON f.away_team_id=t2.team_id
			JOIN Field
			ON f.field_id = Field.field_id
			WHERE f.date = ?";
    $params = array($date);
    return preparedSelect($sql, $params);
}

function getFixtureDates($season) {
	$sql = "SELECT DISTINCT date FROM Fixture WHERE season = ?";
    $params = array($season);
	return preparedSelect($sql, $params);
}

function getFixture($id) {
	$sql = "SELECT f.fixture_id 'id', f.season, f.division_id 'division', f.game_type 'type', f.home_team_id 'home', t1.team_name 'homeName', f.home_score 'homeScore', f.away_score 'awayScore', f.away_team_id 'away', t2.team_name 'awayName', f.date, f.hour, f.field_id 'field', f.complete
			FROM Fixture f
            LEFT JOIN Team t1
            ON f.home_team_id=t1.team_id
            LEFT JOIN Team t2
            ON f.away_team_id=t2.team_id
			WHERE fixture_id = ?";
    $params = array($id);
	return preparedSelect($sql, $params);
}

function getFixtures($season) {
	$sql = "SELECT f.fixture_id 'id', f.division_id, f.date, t1.team_name 'Home', f.home_score 'Home Score', f.away_score 'Away Score', t2.team_name 'Away', Field.field_name 'Field', f.hour 'Time', complete
			FROM Fixture f
			LEFT JOIN Team t1
			ON f.home_team_id=t1.team_id
			LEFT JOIN Team t2
			ON f.away_team_id=t2.team_id
			JOIN Field
			ON f.field_id = Field.field_id
			WHERE f.season = ?";
    $params = array($season);
	return preparedSelect($sql, $params);
}

function getGameSheetRoster($teamId) {
    $sql = "SELECT player_id 'id', player_name 'name', jersey_num 'jersey' FROM Player WHERE team_id = ? AND active = true ORDER BY jersey_num";
    $params = array($teamId);
    return preparedSelect($sql, $params);
}

function insertFixture($season, $date, $type, $division, $home, $away, $hour, $field) {
	try {
		$ds = "mysql:host=" . DBSERVER . ";dbname=" . DBNAME;
		$pdo = new PDO($ds, DBUSERNAME, DBPASSWORD);
		$pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
		$sql = "INSERT INTO Fixture (season, date, game_type, division_id, home_team_id, away_team_id, hour, field_id) VALUES (?,?,?,?,?,?,?,?)";
		$stmt = $pdo->prepare($sql);
		$stmt->execute(array($season, $date, $type, $division, $home, $away, $hour, $field));
		$count = $stmt->rowCount();
		if ($count > 0) {
			return true;
		}
	} catch(PDOException $e) {
		echo $e->getMessage();
	} finally {
		$pdo = null;
	}
}

function updateFixture($id, $season, $date, $type, $division, $home, $away, $hour, $field) {
	try {
		$ds = "mysql:host=" . DBSERVER . ";dbname=" . DBNAME;
		$pdo = new PDO($ds, DBUSERNAME, DBPASSWORD);
		$pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
		$sql = "UPDATE Fixture SET season=?, date=?, game_type=?, division_id=?, home_team_id=?, away_team_id=?, hour=?, field_id=? WHERE fixture_id=?";
		$stmt = $pdo->prepare($sql);
		$stmt->bindParam(1, $season, PDO::PARAM_INT);
		$stmt->bindParam(2, $date, PDO::PARAM_STR);
		$stmt->bindParam(3, $type, PDO::PARAM_INT);
		$stmt->bindParam(4, $division, PDO::PARAM_INT);
		$stmt->bindParam(5, $home, PDO::PARAM_INT);
		$stmt->bindParam(6, $away, PDO::PARAM_INT);
		$stmt->bindParam(7, $hour, PDO::PARAM_INT);
		$stmt->bindParam(8, $field, PDO::PARAM_INT);
		$stmt->bindParam(9, $id, PDO::PARAM_INT);
		$stmt->execute();
		$count = $stmt->rowCount();
		if ($count > 0) {
			return true;
		}
	} catch(PDOException $e) {
		echo $e->getMessage();
	} finally {
		$pdo = null;
	}
}

function updateGameFixture($gameId, $homeScore, $awayScore) {
    try {
		$ds = "mysql:host=" . DBSERVER . ";dbname=" . DBNAME;
		$pdo = new PDO($ds, DBUSERNAME, DBPASSWORD);
		$pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
		$sql = "UPDATE Fixture SET home_score=?, away_score=?, complete=true WHERE fixture_id=?";
		$stmt = $pdo->prepare($sql);
		$stmt->bindParam(1, $homeScore, PDO::PARAM_INT);
		$stmt->bindParam(2, $awayScore, PDO::PARAM_INT);
		$stmt->bindParam(3, $gameId, PDO::PARAM_BOOL);
		$stmt->execute();
		$count = $stmt->rowCount();
		if ($count > 0) {
			return true;
		}
	} catch(PDOException $e) {
		echo $e->getMessage();
	} finally {
		$pdo = null;
	}
}

function getFields() {
	$sql = "SELECT field_id 'id', field_name 'name' FROM Field ORDER BY field_name";
	return simpleSelect($sql);
}

function getGameTypes() {
	$sql = "SELECT game_type_id 'id', game_type_name 'name' FROM GameType ORDER BY id ASC";
	return simpleSelect($sql);
}

function getGame($id) {
	$sql = "SELECT player_id 'player', team_id 'team', goals_scored 'goals', yellow_cards 'yellow', red_cards 'red'
            FROM Game
            WHERE game_id = ?";
    $params = array($id);
	return preparedSelect($sql, $params);
}

function insertGame($insertData) {
	try {
		$ds = "mysql:host=" . DBSERVER . ";dbname=" . DBNAME;
		$pdo = new PDO($ds, DBUSERNAME, DBPASSWORD);
		$pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
        $pdo->beginTransaction();
		$sql = "INSERT INTO Game (game_id, player_id, team_id, goals_scored, yellow_cards, red_cards) VALUES (?, ?, ?, ?, ?, ?)";
		$stmt = $pdo->prepare($sql);
        foreach ($insertData as $row) { 
            $stmt->execute($row);
            $stmt->closeCursor();
        }
        $pdo->commit();
		$count = $stmt->rowCount();
		if ($count > 0) {
			return true;
		}
	} catch(PDOException $e) {
		echo $e->getMessage();
	} finally {
		$pdo = null;
	}
}

function insertUpdateGame($insertUpdateData) {
	$sql = "INSERT INTO Game
                (game_id, player_id, team_id, goals_scored, yellow_cards, red_cards)
            VALUES (?, ?, ?, ?, ?, ?)
            ON DUPLICATE KEY UPDATE goals_scored=VALUES(goals_scored), yellow_cards=VALUES(yellow_cards), red_cards=VALUES(red_cards)";
    return preparedMultipleInsertUpdateDelete($sql, $insertUpdateData);
}

function deleteGame($deleteData) {
    $sql = "DELETE FROM Game WHERE game_id = ? AND player_id = ?";
    return preparedMultipleInsertUpdateDelete($sql, $deleteData);
}

function getStandings($season, $division) {
	$sql = "SELECT
				Team, 
				COUNT(CASE WHEN complete THEN 1 END) 'Played', 
				COUNT(CASE WHEN home_score > away_score THEN 1 END) 'Wins', 
				COUNT(CASE WHEN away_score > home_score THEN 1 END) 'Losses', 
				COUNT(CASE WHEN home_score = away_score AND complete THEN 1 END) 'Draws',
				SUM(home_score) 'Goals', 
				SUM(away_score) 'Allowed', 
				SUM(home_score) - SUM(away_score) 'Differential',
				SUM(
					(CASE WHEN home_score > away_score THEN 3 ELSE 0 END) + 
                    (CASE WHEN home_score = away_score AND complete THEN 1 ELSE 0 END)) 'Points',
                SUM(
					(CASE WHEN home_score < away_score AND (void_team_id IS NOT NULL AND void_team_id != id) THEN 3 ELSE 0 END) + 
                    (CASE WHEN home_score > away_score AND (void_team_id IS NOT NULL AND void_team_id = id) THEN -3 ELSE 0 END) + 
                    (CASE WHEN home_score = away_score AND complete AND (void_team_id IS NOT NULL AND void_team_id != id) THEN 2 ELSE 0 END) + 
                    (CASE WHEN home_score = away_score AND complete AND (void_team_id IS NOT NULL AND void_team_id = id) THEN -1 ELSE 0 END)) 'Void'
			FROM (
				SELECT t.team_name Team, f.home_team_id id, home_score, away_score, void_team_id, complete FROM Fixture f JOIN Team t ON f.home_team_id = t.team_id WHERE f.season = ? AND f.division_id = ?
				UNION ALL
				SELECT t.team_name Team, f.away_team_id id, away_score, home_score, void_team_id, complete FROM Fixture f JOIN Team t ON f.away_team_id = t.team_id WHERE f.season = ? AND f.division_id = ?
			) a
			GROUP BY Team
			ORDER BY Points DESC, Wins DESC, Differential DESC, Goals DESC";
    $params = array($season, $division, $season, $division);
	return preparedSelect($sql, $params);
}

function getPlayer($id) {
	$sql = "SELECT player_name 'name', team_id 'team', jersey_num 'jersey', active FROM Player WHERE player_id = ?";
    $params = array($id);
	return preparedSelect($sql, $params);
}

function getPlayers($teamId){
	$sql = "SELECT player_id 'id', player_name 'Name', jersey_num 'Jersey', active FROM Player WHERE team_id = ? ORDER BY active DESC, Jersey";
	$params = array($teamId);
    return preparedSelect($sql, $params);
}

function insertPlayer($name, $team, $jersey) {
	try {
		$ds = "mysql:host=" . DBSERVER . ";dbname=" . DBNAME;
		$pdo = new PDO($ds, DBUSERNAME, DBPASSWORD);
		$pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
		$sql = "INSERT INTO Player (player_name, team_id, jersey_num) VALUES (?,?,?)";
		$stmt = $pdo->prepare($sql);
		$stmt->bindParam(1, $name, PDO::PARAM_STR);
		$stmt->bindParam(2, $team, PDO::PARAM_INT);
		$stmt->bindParam(3, $jersey, PDO::PARAM_INT);
		$stmt->execute();
		$count = $stmt->rowCount();
		if ($count > 0) {
			return true;
		}
	} catch(PDOException $e) {
		echo $e->getMessage();
	} finally {
		$pdo = null;
	}
}

function updatePlayer($id, $name, $team, $jersey, $active) {
	try {
		$ds = "mysql:host=" . DBSERVER . ";dbname=" . DBNAME;
		$pdo = new PDO($ds, DBUSERNAME, DBPASSWORD);
		$pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
		$sql = "UPDATE Player SET player_name=?, team_id=?, jersey_num=?, active=? WHERE player_id=?";
		$stmt = $pdo->prepare($sql);
		$stmt->bindParam(1, $name, PDO::PARAM_STR);
		$stmt->bindParam(2, $team, PDO::PARAM_INT);
		$stmt->bindParam(3, $jersey, PDO::PARAM_INT);
		$stmt->bindParam(4, $active, PDO::PARAM_BOOL);
		$stmt->bindParam(5, $id, PDO::PARAM_INT);
		$stmt->execute();
		$count = $stmt->rowCount();
		if ($count > 0) {
			return true;
		}
	} catch(PDOException $e) {
		echo $e->getMessage();
	} finally {
		$pdo = null;
	}
}