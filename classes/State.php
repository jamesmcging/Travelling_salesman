<?php
class State {
  public $arrRoute = array();

  public function __construct($arrRoute) {
    $this->arrRoute = $arrRoute;
  }

  public function getStateValue() {
    return Map::getDistanceForRoute($this->arrRoute);
  }
}

class Route extends State {

  public function __construct($arrRoute) {
    parent::__construct($arrRoute);
  }

  public function getRoute() {
    return $this->getRoute();
  }

  public function getRouteAsString() {
    return implode(', ', $this->arrRoute);
  }

  public function getRouteLength() {
    return parent::getStateValue();
  }
}