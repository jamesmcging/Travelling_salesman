<?php
class TravellingSalesMan {
  private $objStateGenerator = null;
  private $objAlgorithm = null;
  private $sMapName = '';

  public function __construct() {
    // Load a map for the salesman to travel, we default to the 15 city map
    // unless the user has already set the map in a previous session.
    $sMap = isset($_SESSION['map']) ? $_SESSION['map'] : '15_city_matrix.txt';
    $this->setMap($sMap);

    // Load a route generator, we default to the MoveOneToFront unless the user
    // has already set the map in a previous session.
    $sStateGenerator = isset($_SESSION['stategenerator']) ? $_SESSION['stategenerator'] : 'MoveOneToFront';
    $this->setStateGenerator($sStateGenerator);

    // Load an algorithm. we default to HillClimbing unless the user has already
    // set out an algorithm in a previous session.
    $sAlgorithm = isset($_SESSION['algorithm']) ? $_SESSION['algorithm'] : 'HillClimbing';
    $this->setAlgorithm($sAlgorithm);
  }

  public function __destruct() {
    $_SESSION['map'] = $this->sMapName;
    $_SESSION['algorithm'] = get_class($this->objAlgorithm);
    $_SESSION['stategenerator'] = get_class($this->objStateGenerator);
  }

  public function findShortestRoute($nRunCount) {
    $arrRunData = array('fixed_data'=>array(
        'map' => $this->sMapName,
        'stategenerator' => get_class($this->objStateGenerator),
        'algorithm' => get_class($this->objAlgorithm)
    ));
    for ($i = 0; $i < $nRunCount; $i++) {
      $arrRunData[] = $this->objAlgorithm->findShortestRoute();
    }
    return $arrRunData;
  }

  public function setMap($sMapName) {
    $this->sMapName = $sMapName;
    Map::loadMap($sMapName);
    return true;
  }

  public function setAlgorithm($sAlgorithmName) {
    // Ensure we have a default state generator (required to instantiate the
    // algorithm).
    if ($this->objStateGenerator === null) {
      $this->objStateGenerator = new $sAlgorithmName();
    }

    $this->objAlgorithm = new $sAlgorithmName($this->objStateGenerator);

    return true;
  }

  public function setStateGenerator($sStateGeneratorName) {
    // Set the state generator
    $this->objStateGenerator = new $sStateGeneratorName();

    // The algorithm needs re-instantiated with the current state generator
    if ($this->objAlgorithm != null) {
      $sCurrentAlgorithm = get_class($this->objAlgorithm);
      $this->objAlgorithm = new $sCurrentAlgorithm($this->objStateGenerator);
    }
    return true;
  }

  public function setStateGeneratorParameters($arrParameters) {
    return $this->objStateGenerator->setParameters($arrParameters);
  }

  public function getStats() {
    return array(
      'data' => Log::getStats(get_class($this->objAlgorithm), get_class($this->objStateGenerator), $this->sMapName),
      'bOutcome' => true
    );
  }
}