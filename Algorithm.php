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

  function findShortestPath() {
    $bFurtherImprovementPossible = true;
    $objCurrentShortestRoute = $this->objCurrentRoute;
    $nIteration = 0;

    while ($bFurtherImprovementPossible) {
      // Assume that we won't find a shorter route
      $bFurtherImprovementPossible = false;

      // Generate child conditions for the current condition
      $arrChildRoutes = $this->objStateGenerator->getChildRoutes($objCurrentShortestRoute, array('nDesiredChildStates'=>1000));

      // Compare current route length to child route lengths
      foreach ($arrChildRoutes as $objRoute) {
        // If the child is shorter than our current route, make the child route
        // the current route
        if ($objRoute->getRouteLength() < $objCurrentShortestRoute->getRouteLength()) {
          $objCurrentShortestRoute = $objRoute;
          // Run the loop again with the new current shortest route
          $bFurtherImprovementPossible = true;
        }
      }
      // Increment a count of iterations
      $nIteration++;
      // Log the iteration
      Log::addIteration($nIteration, $objCurrentShortestRoute->getRouteLength());
    }
  }
}