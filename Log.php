<?php

class Log {
  static $arrLog = array();

  static function addIteration($nIteration, $nRouteLength) {
    self::$arrLog[$nIteration] = $nRouteLength;
  }

  static function getLog() {
    return self::$arrLog;
  }
}

