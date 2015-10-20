<?php
abstract class Algorithm {
  public $objMap = null;
  public $objStateGenerator = null;
  protected $objCurrentRoute = null;
  
  public function __construct($objStateGenerator) {
    $this->objStateGenerator = $objStateGenerator;

    // Generate a starting route
    $this->objCurrentRoute = new Route($objStateGenerator->generateRandomRoute());
  }
  
  public function findShortestPath() {}
}

class HillClimbing extends Algorithm {

  public function __construct($objStateGenerator) {
    parent::__construct($objStateGenerator);
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

class Simulated_annealing extends Algorithm {
  
  const TEMP_DROP = 0.05; // should give us 20 iterations
  
  public function __construct($objStateGenerator) {
    parent::__construct($objStateGenerator);
  }
  
  function findShortestRoute() {
    
//    var_dump($this);
    
    
    $objCurrentShortestRoute = $this->objCurrentRoute;
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
    $fTemperature = 0.99;

    while ($fTemperature > 0) {
      
      // Generate child conditions of the current route
      $arrChildRoutes = $this->objStateGenerator->getChildRoutes($objCurrentShortestRoute);

      $bShorterChildRouteFound = false;
      
      // Run through the child routes looking for a shorter route then the 
      // current route
      foreach ($arrChildRoutes as $objChildRoute) {
        // Calculate the length of this child route
        $nChildRouteScore = $objChildRoute->getRouteLength() - $objCurrentShortestRoute->getRouteLength();
        
        // If the child is shorter than our current route...
        if ($nChildRouteScore < 0) {
          // ... make the child route into the current route
          $objCurrentShortestRoute = $objChildRoute;
          
          $bShorterChildRouteFound = true;
        }
      }
       
      // If we haven't found a shorter child route then the initial route, run
      // through the child routes again. This time we might take a risk 
      // depending on the temperature.
      if (!$bShorterChildRouteFound) {
        foreach ($arrChildRoutes as $objChildRoute) {
          // Generate a random number (rand returns an int)
          $fRandom = rand(0, 100) / 100;
          // If the random number is less then the magic number then...
          if ($fRandom < exp(-$nChildRouteScore/$fTemperature)) {
            // ... make this child route into the current route
            $objCurrentShortestRoute = $objChildRoute;
          }
        }
      }
      
      // Save the chosen child route to our data
      $arrTemp = array(
        'route' => $objCurrentShortestRoute->getRouteAsString(),
        'length' => $objCurrentShortestRoute->getRouteLength()
      );
      $arrData['states'][] = $arrTemp;
   
      // Reduce the temperature
      $fTemperature = $fTemperature - self::TEMP_DROP;
    }

    // Save the run to the DB
    Log::addRun($arrData);

    // Return the outcome of the run
    return $arrData;
  }
}