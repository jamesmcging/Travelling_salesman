<?php
class Map {

  static $arrMap = array();

  static $sMapName = '';

  public function __construct() {
    self::loadMap();
  }

  /**
   * Method charged with returning the distance between two cities
   * @param int $nCity_A
   * @param int $nCity_B
   * @return type
   */
  static function getDistance($nCity_A, $nCity_B) {
    return self::$arrMap[$nCity_A][$nCity_B];
  }

  /**
   * Method charged with returning the length of a route. THe route is
   * identified by passing in an array of city IDs
   *
   * @param array $arrRoute array of nCityIDs
   * @return int
   */
  static function getDistanceForRoute(array $arrRoute) {
    $nDistance = 0;

    for ($i = 0; $i < (count($arrRoute) - 1); $i++) {
      $nCityFrom = $arrRoute[$i];
      $nCityTo = $arrRoute[$i + 1];
      $nDistance += self::$arrMap[$nCityFrom][$nCityTo];
    }

    // Remember to add the journey from the last city back to the first city
    $nDistance += self::$arrMap[count($arrRoute) - 1][0];

    return $nDistance;
  }

  /**
   * Method charged with extracting the distances between cities from the txt
   * file and loading them into a 2 dimensional array arrMap
   */
  static function loadMap($sMapName = '15_city_matrix.txt') {
    self::$sMapName = $sMapName;

    // Load the txt file as a string
    $sString = file_get_contents('./maps/'.$sMapName);

    // Add each line of distances between cities to array arrCities
    $arrCities = explode("\n", $sString);

//    echo "<pre>";
//    echo print_r($arrCities, 1);
//    echo "</pre>";
//    echo "<hr>";

    // Foreach line of distances from this city
    foreach ($arrCities as $nCityID => $sDistances) {

      // Extract the distances into array arrDistances
      preg_match_all('~([0-9])+~', $sDistances, $arrDistances);

      // Save the distances from this city to other cities in our static array
      self::$arrMap[$nCityID] = $arrDistances[0];
    }
  }
}