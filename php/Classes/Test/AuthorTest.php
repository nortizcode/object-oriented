<?php
namespace Nortizcode\DataDesign\Test;

use Nortizcode\ObjectOriented\{Author};

//hack!! added so this class could see datadesigntest
require_once(dirname(__DIR__) . "/Test/DataDesignTest.php");

// grab the class under scrutiny
require_once(dirname(__DIR__) . "/autoload.php");

// grab the uuid generator
require_once(dirname(__DIR__, 2) . "/lib/uuid.php");


class AuthorTest extends DataDesignTest {



}