<?php
abstract class Algorithm {
  public function findShortestPath() {}
}

class HillClimbing extends Algorithm {

  public $objMap = null;
  public $objStateGenerator = null;
  private $objCurrentRoute = null;


  public function __construct($objStateGenerator) {
    $this->objStateGenerator = $objStateGenerator;

    // Generate a starting route
    $this->objCurrentRoute = new Route($objStateGenerator->generateRandomRoute());
  }

  function findShortestRoute() {
    $bFurtherImprovementPossible = true;
    $objCurrentShortestRoute = $this->objCurrentRoute;
    $nIteration = 0;
    $arrData = array(
      'algorithm' => get_class($this),
      'stategenerator' => get_class($this->objStateGenerator),
      'map' => Map::$sMapName,
      'states' => array(
        0 => array(
          'route' => $this->objCurrentRoute->getRouteAsString(),
          'length' => $this->objCurrentRoute->getRouteLength()
        )
      )
    );

    while ($bFurtherImprovementPossible) {
      // Assume that we won't find a shorter route
      $bFurtherImprovementPossible = false;

      // Generate child conditions for the current condition
      $arrChildRoutes = $this->objStateGenerator->getChildRoutes($objCurrentShortestRoute);

      // Compare current route length to child route lengths
      foreach ($arrChildRoutes as $objRoute) {
        // If the child is shorter than our current route, make the child route
        // the current route
        if ($objRoute->getRouteLength() < $objCurrentShortestRoute->getRouteLength()) {
          $objCurrentShortestRoute = $objRoute;
          // Save the chosen child route to our data
          $arrTemp = array(
            'route' => $objCurrentShortestRoute->getRouteAsString(),
            'length' => $objCurrentShortestRoute->getRouteLength()
          );
          $arrData['states'][] = $arrTemp;

          // Run the loop again with the new current shortest route
          $bFurtherImprovementPossible = true;
        }
      }
      // Increment a count of iterations
      $nIteration++;
    }

    // Save the run to the DB
    Log::addRun($arrData);

    // Return the outcome of the run
    return $arrData;
  }
}