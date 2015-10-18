<?php

class Log {
  /**
   * arrData => (
   *   algorithm => STRING,
   *   stategenerator => STRING,
   *   states => (
   *      0 => (ROUTE),
   *      1 => (ROUTE),
   *      etc.
   *    )
   * )
   *
   * where ROUTE => array(
   *   route => STRING (comma separated list of city IDs,
   *   length => length of route
   * )
   *
   * @param type $arrData
   */
  static function addRun($arrData) {
    $bOutcome = true;
    $nEpoch = time();
    $sQuery = "INSERT INTO log SET
                log_unixtimestamp = $nEpoch,
                log_state = :state_count,
                log_algorithm = '{$arrData['algorithm']}',
                log_stategenerator = '{$arrData['stategenerator']}',
                log_map = '{$arrData['map']}',
                log_route = :route,
                log_routelength = :routelength
              ";

    // Query the DB
    $statement = DB::getInstance()->prepare($sQuery);
    foreach ($arrData['states'] as $nStateCount => $arrStateData) {
      $statement->bindParam(':state_count', $nStateCount);
      $statement->bindParam(':route', $arrStateData['route']);
      $statement->bindParam(':routelength', $arrStateData['length']);
      if (!$statement->execute()) {
        $bOutcome = false;
        break;
      }
    }
    return $bOutcome;
  }

  static function getOneRun($nRunID) {

  }

  static function getStats($sAlgorithmName, $sStateGenerator, $sMap) {
    $arrResponse = array(
      'algorithm' => $sAlgorithmName,
      'stategenerator' => $sStateGenerator,
      'map' => $sMap
    );

    // Determine the amount of states that there are (state 0 is the initial
    // route, each number after that is a child of the previous state.
    $sQuery = "SELECT
                  count(distinct log_state) as state_count,
                  min(log_routelength) as minRouteLength
                FROM log
                WHERE log_algorithm = '$sAlgorithmName'
                  AND log_stategenerator = '$sStateGenerator'
                  AND log_map = '$sMap'
                ";
    $statement = DB::getInstance()->prepare($sQuery);
    $statement->execute();
    $row = $statement->fetch(PDO::FETCH_ASSOC);
    $nStateCount = $row['state_count'];
    $arrResponse['shortest_route_length'] = $row['minRouteLength'];

    // Ask the DB for the route of the shortest distance found
    $sQuery = "SELECT
                  *
                FROM log
                WHERE log_routelength = {$arrResponse['shortest_route_length']}
              ";
    $statement = DB::getInstance()->prepare($sQuery);
    $statement->execute();
    $row = $statement->fetch(PDO::FETCH_ASSOC);
    $arrResponse['shortest_route'] = $row['log_route'];

    // Ask the Db for the average of each state
    $sQuery = "SELECT
                AVG(log_routelength) AS averagelength,
                count(log_routelength) as iterationcount,
                log_state
                FROM log
                WHERE log_algorithm = '$sAlgorithmName'
                  AND log_stategenerator = '$sStateGenerator'
                  AND log_map = '$sMap'
                  AND log_state = :nStateCount
              ";
    // Get the average of each state
    $statement = DB::getInstance()->prepare($sQuery);
    for ($i = 0; $i < $nStateCount; $i++) {
      $statement->bindParam(':nStateCount', $i);
      $statement->execute();
      $row = $statement->fetch(PDO::FETCH_ASSOC);
      $arrResponse['average_length_of_iteration'][$i] = $row['averagelength'];
      $arrResponse['count_of_iteration'][$i] = $row['iterationcount'];
    }

    return $arrResponse;
  }
}


/*
 * CREATE TABLE log (
	log_id INT NOT NULL AUTO_INCREMENT COMMENT 'an ID for each state ever covered',
  log_unixtimestamp INT(11) NOT NULL COMMENT 'ID for a single run of an algorithm',
	log_mysqltimestamp TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
	log_state INT UNSIGNED COMMENT 'the iteration of the run through 0 is the intial state',
	log_algorithm varchar(30),
	log_stategenerator varchar(30),
  log_map varchar(30),
	log_route varchar(1000),
	log_routelength INT UNSIGNED,
	PRIMARY KEY (log_id)
);
 */