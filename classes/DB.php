<?php
/** class db
 *
 * Class that provides a database handler in a static method
 * @author Jamie
 *
 */
class db{
	private static $instance = NULL;
	private static $aryCategories = array();

	/**
	 * Return DB instance or create intitial connection
	 * @return object (PDO)
	 * @access public
	 */
	public static function getInstance() {
		if (!self::$instance)
		{
			self::$instance = new PDO('mysql:host=localhost;dbname='.DB_NAME, DB_USER_NAME, DB_USER_PASS);
			self::$instance->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
			self::$instance->setAttribute(PDO::MYSQL_ATTR_USE_BUFFERED_QUERY, true);
		}
		return self::$instance;
	}

  private function __construct() {}
  private function __clone(){

  }
} /*** end of class ***/