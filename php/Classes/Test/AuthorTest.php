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

private $VALID_AUTHOR_ACTIVATION_TOKEN; //done in set up
private $VALID_AUTHOR_EMAIL = "nkortiz92@gmail.com";
private $VALID_AUTHOR_HASH; = //done in set up
private $VALID_AUTHOR_AVATAR = "https://avatar.com";
private $VALID_AUTHOR_USERNAME= "Nortiz41";

public function setUp(): void {
	parent::setUp();

	$password = "my_secret_password";
	$this->VALID_AUTHOR_HASH = password_hash($password, PASSWORD_ARGON2ID, ["time_cost" => 45]);
	$this->VALID_AUTHOR_ACTIVATION_TOKEN = bin2hex(random_bytes(16));
}

	public function testInsertValidAuthor() : void {

}

public function testDeleteValidAuthor() : void {

}

public function testGetValidAuthorByAuthorId() : void {

}

public function testGetValidAuthors() : void {

}

}