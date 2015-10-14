<?php
require_once './map.php';

Map::loadMap();

//echo Map::getDistance(1,4);

//echo Map::getDistanceForRoute(array(0,1,2,3,4,5,6,7,8,9,10,11,12,13,14,0));

$nShortestRouteLength = 9999999999;
$arrShortestRoute = array();

for ($i = 0; $i < 1000000; $i++) {
  $arrRoute = Map::generateRandomRoute();
  $nRouteLength = Map::getDistanceForRoute($arrRoute);

  if ($nRouteLength < $nShortestRouteLength) {
   $nShortestRouteLength = $nRouteLength;
   $arrShortestRoute = $arrRoute;
  }

//  echo "<p>Route " . implode(",", $arrRoute) . " is " . $nRouteLength . "units long.</p>";
}

echo "<p>Shortest route found is $nShortestRouteLength units long.</p>";