<?php
abstract class StateGenerator {
  public $arrParameters = array();

  /**
   *
   * @param State $objCurrentState
   * @param type $arrParameters
   * @return array of State objects
   */
  public function getChildStates(State $objCurrentState) {
    $arrChildStates = array();
    return $arrChildStates;
  }

  public function generateRandomState() {}

  public function setStateGeneratorParameters($arrParameters) {
    $this->arrParameters = $arrParameters;
  }
}

abstract class RouteGenerator extends StateGenerator {
  public function getChildRoutes(\State $objCurrentRoute) {
    parent::getChildStates($objCurrentRoute);
  }

  public function generateRandomRoute() {
    $arrCityIDs = range(0, (count(Map::$arrMap) - 1));
    shuffle($arrCityIDs);
    return $arrCityIDs;
  }

  public function setRouteGeneratorParameters($arrParameters) {
    parent::setStateGeneratorParameters($arrParameters);
  }

}

/**
 * Class that generates a child state by removing a random city from the route
 * map and prepending that city to the front of the route. Can request that this
 * be done X times in order to generate X child states.
 */
class MoveOneToFront extends RouteGenerator {

  const DEFAULT_CHILD_STATES = 10;

  public function __construct() {
    $this->arrParameters = array('nDesiredChildStates' => self::DEFAULT_CHILD_STATES);
  }

  public function setParameters($arrParameters) {
    parent::setRouteGeneratorParameters($arrParameters);
  }

  public function getChildRoutes(State $objCurrentState) {
    // Default return
    $arrChildStates = array();

    // The number of child states to generate
    for($i = 0; $i < $this->arrParameters['nDesiredChildStates']; $i++) {
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