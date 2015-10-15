<?php
require_once './Map.php';
require_once './State.php';
require_once './StateGenerator.php';
require_once './Algorithm.php';
require_once './Log.php';

new TravellingSalesMan();

class TravellingSalesMan {
  private $objStateGenerator = null;
  private $objAlgorithm = null;

  public function __construct() {
    // Load the map the salesman is going to travel
    Map::loadMap('./map_matrix/15_city_matrix.txt');

    // Instantiate a route generator
    $this->objStateGenerator = new MoveOneToFront();

    // Generate an algorithm class
    $this->objAlgorithm = new HillClimbing($this->objStateGenerator);

    // Ask the algorithm to search for the shortest path
    $this->objAlgorithm->findShortestPath();

    // Output the log
    echo "<pre>";
    echo print_r(Log::getLog(), 1);
    echo "</pre>";
  }
}