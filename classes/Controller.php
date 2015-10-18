<?php
require 'classes/State.php';
require 'classes/DB.php';
require 'classes/Log.php';


class Controller {
  static $arrAvailableMaps = array();
  static $arrAvailableStateGenerators = array();
  static $arrAvailableAlgorithms = array();

  private $objProblem = null;

  public function __construct() {

    session_start();

    // Figure out the maps available
    self::$arrAvailableMaps = array_diff(scandir('maps'), array('..', '.'));
    require 'classes/Map.php';

    // Figure out which state generators are available in StateGenerator.php
    $arrExistingClasses = get_declared_classes();
    require 'classes/StateGenerator.php';
    array_push($arrExistingClasses, 'StateGenerator'); // We don't want the abstract parent StateGenerator class
    array_push($arrExistingClasses, 'RouteGenerator'); // We don't want the abstract parent RouteGenerator class
    self::$arrAvailableStateGenerators = array_diff(get_declared_classes(), $arrExistingClasses);

    // Figure out which algorithms are available in Algorithm.php
    $arrExistingClasses = get_declared_classes();
    require 'classes/Algorithm.php';
    array_push($arrExistingClasses, 'Algorithm'); // We don't want the abstract parent class Algorithm
    self::$arrAvailableAlgorithms = array_diff(get_declared_classes(), $arrExistingClasses);

    // Instantiate the problem
    require 'classes/TravellingSalesman.php';
    $this->objProblem = new TravellingSalesMan();

    // Set a default action
    $sAction = isset($_REQUEST['action']) ? $_REQUEST['action'] : 'renderInterface';

    // Set a default response
    $arrResponse = array("bOutcome" => false);

    // Carry out requested action
    switch ($sAction) {
      case 'renderInterface':
        $this->renderInterface();
        break;

      case 'setMap':
        $arrResponse['bOutcome'] = $this->setMap($_REQUEST['sMapName']);
        break;

      case 'setAlgorithm':
        $arrResponse['bOutcome'] = $this->setAlgorithm($_REQUEST['sAlgorithmName']);
        break;

      case 'setStateGenerator':
        $arrResponse['bOutcome'] = $this->setStateGenerator($_REQUEST['sStateGeneratorName']);
        break;

      case 'getSolution':
        $arrResponse = $this->getSolution();
        break;

      case 'getStats':
        $arrResponse = $this->objProblem->getStats();
        break;

      default:
        $arrResponse['sMessage'] = "Action parameter not recognized, use 'renderInterface', 'setMap', 'setAlgorithm', 'setStateGenerator' or 'getSolution'.";
        break;
    }

    echo json_encode($arrResponse);
  }

  private function setMap() {
    $bOutcome = false;
    if (isset($_REQUEST['sMapName']) && in_array($_REQUEST['sMapName'], self::$arrAvailableMaps)) {
      $bOutcome = $this->objProblem->setMap($_REQUEST['sMapName']);
    }
    return $bOutcome;
  }

  private function setAlgorithm() {
    $bOutcome = false;
    if (isset($_REQUEST['sAlgorithmName']) && in_array($_REQUEST['sAlgorithmName'], self::$arrAvailableAlgorithms)) {
      $bOutcome = $this->setAlgorithm($_REQUEST['sAlgorithmName']);
    }
    return $bOutcome;
  }

  private function setStateGenerator() {
    $bOutcome = false;
    if (isset($_REQUEST['sStateGeneratorName']) && in_array($_REQUEST['sStateGeneratorName'], self::$arrAvailableStateGenerators)) {
      $bOutcome = $this->setStateGenerator($_REQUEST['sStateGeneratorName']);
    }
    return $bOutcome;
  }

  private function getSolution() {
    $nRunCount = (int)$_REQUEST['runCount'];
    $arrResponse['arrRunData'] = $this->objProblem->findShortestRoute($nRunCount);
    return $arrResponse;
  }

  private function renderInterface() {
    $sInterfaceConfiguration = json_encode(array(
      "maps" => array_values(self::$arrAvailableMaps),
      "stateGenerators" => array_values(self::$arrAvailableStateGenerators),
      "algorithms" => array_values(self::$arrAvailableAlgorithms)
    ));
    $sHTML = <<<HTML
<!DOCTYPE html>
<html lang="en">
  <head>
    <title>Heuristic Search</title>

    <!-- jQuery -->
    <script src="./assets/js/jquery-2.1.4.js"></script>

    <!-- Bootstrap CSS and JS -->
    <link rel="stylesheet" href="./assets/css/bootstrap.css">
    <script src="./assets/js/bootstrap.js"></script>

    <!-- js that generates and handles the interface -->
    <script language="javascript" src="/Travelling_salesman/assets/js/view.js"></script>

    <!-- Pass some data to the front end js -->
    <script language="javascript">
      var jsonInterfaceConfiguration = $sInterfaceConfiguration;
    </script>

    <meta name="viewport" content="width=device-width, initial-scale=1">

    <!-- Load the google AJAX API -->
    <script type="text/javascript" src="https://www.google.com/jsapi?autoload={'modules':[{'name':'visualization','version':'1','packages':['corechart']}]}"></script>
  </head>

  <body></body>

</html>
HTML;
    echo $sHTML;

//    echo "<hr>";
//    echo "<pre>";
//    echo print_r($_SESSION);
//    echo "</pre>";

    exit;
  }
}