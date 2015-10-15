<?php
abstract class StateGenerator {
  /**
   *
   * @param State $objCurrentState
   * @param type $arrParameters
   * @return array of State objects
   */
  static function getChildStates(State $objCurrentState, $arrParameters) {
    $arrChildStates = array();
    return $arrChildStates;
  }

  static function generateRandomState() {}

}

abstract class RouteGenerator extends StateGenerator {
  static function getChildRoutes(\State $objCurrentRoute, $arrParameters) {
    parent::getChildStates($objCurrentRoute, $arrParameters);
  }

  static function generateRandomRoute() {
    $arrCityIDs = range(0, (count(Map::$arrMap) - 1));
    shuffle($arrCityIDs);
    return $arrCityIDs;
  }

}

/**
 * Class that generates a child state by removing a random city from the route
 * map and prepending that city to the front of the route. Can request that this
 * be done X times in order to generate X child states.
 */
class MoveOneToFront extends RouteGenerator {
  static function getChildRoutes(State $objCurrentState, $arrParameters) {
    // Default return
    $arrChildStates = array();

    // The number of child states to generate
    for($i = 0; $i < $arrParameters['nDesiredChildStates']; $i++) {
      // Reset to the original state
      $objDefaultState = clone $objCurrentState;
      // Get a random key in the route
      $nKey = array_rand($objDefaultState->arrRoute, 1);
      // Get the city ID at that key
      $nCityID = $objDefaultState->arrRoute[$nKey];
      // Remove the city from the route
      unset($objDefaultState->arrRoute[$nKey]);
      // Add the city to the front of the route
      array_unshift($objDefaultState->arrRoute, $nCityID);
      // Save this child state
      $arrChildStates[] = $objDefaultState;
    }

    // Return the child state
    return $arrChildStates;
  }
}