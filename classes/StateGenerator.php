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

class OptimiseGroupsOfThree extends RouteGenerator {
  
  const CHILD_STATE_COUNT = 10;
  
  public function getChildRoutes(State $objCurrentState) {
    // Default return
    $arrChildStates = array();

    // The number of child states to generate
    for($i = 0; $i < self::CHILD_STATE_COUNT; $i++) {
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
      
      // Split the cities into chunks of three
      $arrChunks = array_chunk($objDefaultState->arrRoute, 3);
      $arrTemp = array();
      foreach ($arrChunks as $arrGroupOfThreeCities) {
        // Optimise the order of the three cities to reduce the distance travelled
        $arrTemp = array_merge($arrTemp, $this->optimise($arrGroupOfThreeCities));
      }
      
      // ... and stuff back into arrRoute
      $objDefaultState->arrRoute = $arrTemp;
      
      // Save this child state
      $arrChildStates[] = $objDefaultState;
    }

    // Return the child state
    return $arrChildStates;
  }
  
  /**
   * Function charged with optimising (reducing as low as possible) the distance
   * between three cities.
   * 
   * @param type $arrCities
   */
  function optimise($arrCities) {
    $arrOutcome = $arrCities;
    
    // Only try to re-organise if we have three cities (array_chunk can give us
    // back fewer then three if the original array is a multiple of three)
    if (count($arrCities) === 3) {
      $arrPossible = array();
      // $arrPossible = array();Generate all possible combinations of the three cities. There are three
      // possible ways of organizing three cities: A-B-C, A-C-B and C-A-B
      $arrPossible[0] = array($arrCities[0],$arrCities[1],$arrCities[2]); //A-B-C
      $arrPossible[1] = array($arrCities[0],$arrCities[2],$arrCities[1]); //A-C-B
      $arrPossible[2] = array($arrCities[1],$arrCities[0],$arrCities[2]); //B-A-C

      // Get the length of each tuple
      $nShortestDistance = 999999999;
      $nShortestCombination = 0;
      foreach ($arrPossible as $nKey => $arrOfThreeCities) {
        // If this is the shortest combinatin found, save it
        $nLengthOfThisCombination = Map::getDistanceForRoute($arrOfThreeCities, false);
        if ($nLengthOfThisCombination < $nShortestDistance) {
          $nShortestDistance = $nLengthOfThisCombination;
          $nShortestCombination = $nKey;
        }
      }

      // return the shortest combination
      $arrOutcome = $arrPossible[$nShortestCombination];
    }
    
    return $arrOutcome;
  }
}