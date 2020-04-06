<?php
namespace Nortizcode\ObjectOriented;

require_once("autoload.php");
require_once(dirname(__DIR__) . "/vendor/autoload.php");

use NortizCode\ObjectOriented\ValidateDate;
use NortizCode\ObjectOriented\ValidateUuid;
use Ramsey\Uuid\Uuid;

class Author {
	use ValidateUuid;
	use ValidateDate;

	private $authorId;

	private $authorActivationToken;

	private $authorAvatarUrl;

	private $authorEmail;

	private $authorHash;

	private $authorUsername;

	//constructor method

	public function __construct($newAuthorId, $newAuthorActivationToken, $newAuthorEmail, $newAuthorHash, $newAuthorAvatarUrl, $newAuthorUsername) {
		try {
			$this->setAuthorId($newAuthorId);
			$this->setAuthorActivationToken($newAuthorActivationToken);
			$this->setAuthorEmail($newAuthorEmail);
			$this->setAuthorHash($newAuthorHash);
			$this->setAuthorAvatarUrl($newAuthorAvatarUrl);
			$this->setAuthorUsername($newAuthorUsername);
		} //determine what exception type was thrown
		catch(\InvalidArgumentException | \RangeException | \Exception | \TypeError $exception) {
			$exceptionType = get_class($exception);
			throw(new $exceptionType($exception->getMessage(), 0, $exception));
		}
	}

	//accessor method for author id

	public function getAuthorId() : Uuid{
		return($this->authorId);
	}

	//mutator method for author id

	public function setAuthorId(string $newAuthorId) : void {
		try {
			$uuid = self::validateUuid($newAuthorId);
		} catch(\InvalidArgumentException | \RangeException | \Exception | \TypeError $exception) {
			$exceptionType = get_class($exception);
			throw(new $exceptionType($exception->getMessage(), 0, $exception));
		}

		$this->authorId = $uuid;
	}

	//accessor method

	public function getAuthorActivationToken($newAuthorActivationToken){
		return ($this->authorActivationToken);
	}

	//mutator method

	public function setAuthorActivationToken($newAuthorActivationToken) :void {
		if($newAuthorActivationToken === null) {
			$this->authorActivationToken = null;
			return;
		}

		$newAuthorActivationToken = strtolower(trim($newAuthorActivationToken));
		if(ctype_xdigit($newAuthorActivationToken) === false) {
			throw(new\RangeException("user activation is not valid"));
		}

		//make sure user activation token is only 32 characters
		if(strlen($newAuthorActivationToken) !== 32) {
			throw(new\RangeException("user activation token has to be 32"));
		}
		$this->authorActivationToken = $newAuthorActivationToken;
	}

	//accessor method

	public function getAuthorEmail(): string {
		return $this->authorEmail;
	}

	/**
	 * mutator method for email
	 **/
	public function setAuthorEmail(string $newAuthorEmail): void {

		// verify the email is secure
		$newAuthorEmail = trim($newAuthorEmail);
		$newAuthorEmail = filter_var($newAuthorEmail, FILTER_VALIDATE_EMAIL);
		if(empty($newAuthorEmail) === true) {
			throw(new \InvalidArgumentException("profile email is empty or insecure"));
		}

		// verify the email will fit in the database
		if(strlen($newAuthorEmail) > 128) {
			throw(new \RangeException("profile email is too large"));
		}

		// store the email
		$this->authorEmail = $newAuthorEmail;
	}

	public function getAuthorHash(): string {
		return $this->authorHash;
	}

	/**
	 * mutator method for profile hash password
	 *
	 * @param string $newAuthorHash
	 * @throws \InvalidArgumentException if the hash is not secure
	 * @throws \RangeException if the hash is not 128 characters
	 * @throws \TypeError if profile hash is not a string
	 */
	public function setAuthorHash(string $newAuthorHash): void {
		//enforce that the hash is properly formatted
		$newAuthorHash = trim($newAuthorHash);
		if(empty($newAuthorHash) === true) {
			throw(new \InvalidArgumentException("profile password hash empty or insecure"));
		}

		//enforce the hash is really an Argon hash
		$authorHashInfo = password_get_info($newAuthorHash);
		if($authorHashInfo["algoName"] !== "argon2i") {
			throw(new \InvalidArgumentException("profile hash is not a valid hash"));
		}

		//enforce that the hash is exactly 97 characters.


		//store the hash
		$this->authorHash = $newAuthorHash;
	}


	public function getAuthorAvatarUrl() : string {
		return($this->authorAvatarUrl);
	}

	/**
	 * mutator method for at handle
	 *
	 * @param string $newAuthorAvatarUrl new value of profile avatar URL
	 * @throws \InvalidArgumentException if $newProfileAvatarUrl is not a string or insecure
	 * @throws \RangeException if $newProfileAvatarUrl is > 255 characters
	 * @throws \TypeError if $newAtHandle is not a string
	 **/
	public function setAuthorAvatarUrl(string $newAuthorAvatarUrl) : void {

		$newAuthorAvatarUrl = trim($newAuthorAvatarUrl);
		$newAuthorAvatarUrl = filter_var($newAuthorAvatarUrl, FILTER_SANITIZE_STRING, FILTER_FLAG_NO_ENCODE_QUOTES);

		// verify the avatar URL will fit in the database
		if(strlen($newAuthorAvatarUrl) > 255) {
			throw(new \RangeException("image cloudinary content too large"));
		}
		// store the image cloudinary content
		$this->authorAvatarUrl = $newAuthorAvatarUrl;
	}

	public function getAuthorUsername(): string {
		return ($this->authorUsername);
	}

	public function setAuthorUsername(string $newAuthorUsername) : void {
		// verify the at handle is secure
		$newAuthorUsername = trim($newAuthorUsername);
		$newAuthorUsername = filter_var($newAuthorUsername, FILTER_SANITIZE_STRING, FILTER_FLAG_NO_ENCODE_QUOTES);
		if(empty($newAuthorUsername) === true) {
			throw(new \InvalidArgumentException("profile at handle is empty or insecure"));
		}

		// verify the at handle will fit in the database
		if(strlen($newAuthorUsername) > 32) {
			throw(new \RangeException("profile at handle is too large"));
		}

		// store the at handle
		$this->authorUsername = $newAuthorUsername;
	}

	public function insert(\PDO $pdo) : void {

		// create query template
		$query = "INSERT INTO author(authorId, authorActivationToken, authorEmail, authorHash, authorUsername, authorAvatarUrl) VALUES(:authorId, :authorActivationToken, :authorEmail, :authorHash, authorUsername, authorAvatarUrl)";
		$statement = $pdo->prepare($query);

		// bind the member variables to the place holders in the template
		$parameters = ["authorId" => $this->authorId->getBytes(), "authorActivationToken" => $this->authorActivationToken, "authorEmail" => $this->authorEmail, "authorHash" => $this->authorHash, "authorUsername" => $this->authorUsername->getBytes(), "authorAvatarUrl" => $this->authorAvatarUrl];
		$statement->execute($parameters);
	}


	/**
	 * deletes this Tweet from mySQL
	 *
	 * @param \PDO $pdo PDO connection object
	 * @throws \PDOException when mySQL related errors occur
	 * @throws \TypeError if $pdo is not a PDO connection object
	 **/
	public function delete(\PDO $pdo) : void {

		// create query template
		$query = "DELETE FROM author WHERE authorId = :authorId";
		$statement = $pdo->prepare($query);

		// bind the member variables to the place holder in the template
		$parameters = ["authorId" => $this->getAuthorId()->getBytes()];
		$statement->execute($parameters);
	}

	/**
	 * updates this Author in mySQL
	 *
	 * @param \PDO $pdo PDO connection object
	 * @throws \PDOException when mySQL related errors occur
	 * @throws \TypeError if $pdo is not a PDO connection object
	 **/
	public function update(\PDO $pdo) : void {

		// create query template
		$query = "UPDATE author SET authorActivationToken = :authorActivationToken, authorAvatarUrl = :authorAvatarUrl, authorEmail = :authorEmail, authorHash = :authorHash, authorUsername = :authorUsername WHERE authorId = :authorId";
		$statement = $pdo->prepare($query);


		$parameters = ["authorId" => $this->getAuthorId()->getBytes(),"authorActivationToken" => $this->authorActivationToken, "authorAvatarUrl" => $this->authorAvatarUrl, "authorEmail" => $this->authorEmail, "authorHash" => $this->authorHash, "authorUsername" => $this->authorUsername];
		$statement->execute($parameters);
	}


	public function getAllAuthor(\PDO $pdo) {
		// create query template
		$query= "SELECT * FROM author";
		$statement = $pdo->prepare($query);
		$statement->execute();

		// build an array of author
		$authors = new \SplFixedArray($statement->rowCount());
		$statement->setFetchMode(\PDO::FETCH_ASSOC);
		while(($row = $statement->fetch()) !== false) {
			try {
				$author = new Author($row["authorId"],$row["authorActivationToken"],$row["authorAvatarUrl"],$row["authorEmail"],$row["authorHash"],$row["authorUsername"]);
				//To know the length of an array when you have no clue what's in it
				$authors[$authors->key()] = $author;
				$authors->next();
			} catch(\Exception $exception) {
				// if the row couldn't be converted, rethrow it
				throw(new \PDOException($exception->getMessage(), 0, $exception));
			}
		}

		return ($authors);
	}


	public function getAuthor(\PDO $pdo, $authorId): Author {
		//create query template
		$query = "SELECT authorId, authorActivationToken, authorAvatarUrl, authorEmail, authorHash, authorUsername 
					 FROM author 
					 WHERE authorId = :authorId";
		$statement = $pdo->prepare($query);
		try {
			$authorId = self::validateUuid($authorId);
		} catch(\InvalidArgumentException | \RangeException | \Exception | \TypeError $exception) {
			throw(new \PDOException($exception->getMessage(), 0, $exception));
		}

		//bind the author to their respective place holder in the table
		$parameters = ["authorId" => $authorId->getBytes()];
		$statement->execute($parameters);

		$author = null;
		$statement->setFetchMode(\PDO::FETCH_ASSOC);
		$row = $statement->fetch();
		if($row !== false){
			$author = new Author($row["authorId"], $row["authorActivationToken"], $row["authorAvatarUrl"], $row["authorEmail"], $row["authorHash"], $row["authorUsername"]);
		}
		return ($author);


	}
//get author by Email

	/**
	 * gets author by Email from mySQL
	 *
	 * @param \PDO $pdo PDO connection object
	 * @param $authorEmail
	 * @return Author
	 */
	public function getAuthorByEmail(\PDO $pdo, $authorEmail): Author {

//create query template
		$query = "SELECT authorId, authorActivationToken, authorAvatarUrl, authorEmail, authorHash, authorUsername 
		 FROM author 
		 WHERE authorEmail = :authorEmail";
		$statement = $pdo->prepare($query);

		//bind the objects to their respective placeholders in the table
		$parameters = ["authorEmail" => $authorEmail];
		$statement->execute($parameters);

		$author = null;
		$statement->setFetchMode(\PDO::FETCH_ASSOC);
		$row = $statement->fetch();
		if($row !== false){
			$author = new Author($row["authorId"], $row["authorActivationToken"], $row["authorAvatarUrl"], $row["authorEmail"], $row["authorHash"], $row["authorUsername"]);
		}
		return ($author);

	}

}

