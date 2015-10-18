<?php
// Get the sensitive info from an ignored config file
require('./assets/config/config.php');

// Fetch and run the controller
require './classes/Controller.php';
new Controller();