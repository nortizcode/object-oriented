<?php

namespace Nortizcode\ObjectOriented;

require_once("autoload.php");
require_once(dirname(__DIR__) . "/vendor/autoload.php");

use NortizCode\ObjectOriented\ValidateDate;
use NortizCode\ObjectOriented\ValidateUuid;
use Ramsey\Uuid\Uuid;


/*
	/**
	 * This is the Author class
	 * @author Nathan Ortiz <nortiz41@cnm.edu
 */

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

	public function getAuthorId(): Uuid {
		return ($this->authorId);
	}

	//mutator method for author id

	public function setAuthorId(string $newAuthorId): void {
		try {
			$uuid = self::validateUuid($newAuthorId);
		} catch(\InvalidArgumentException | \RangeException | \Exception | \TypeError $exception) {
			$exceptionType = get_class($exception);
			throw(new $exceptionType($exception->getMessage(), 0, $exception));
		}

		$this->authorId = $uuid;
	}

	//accessor method

	public function getAuthorActivationToken($newAuthorActivationToken) {
		return ($this->authorActivationToken);
	}

	//mutator method

	public function setAuthorActivationToken($newAuthorActivationToken): void {
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


	public function getAuthorAvatarUrl(): string {
		return ($this->authorAvatarUrl);
	}

	/**
	 * mutator method for at handle
	 *
	 * @param string $newAuthorAvatarUrl new value of profile avatar URL
	 * @throws \InvalidArgumentException if $newProfileAvatarUrl is not a string or insecure
	 * @throws \RangeException if $newProfileAvatarUrl is > 255 characters
	 * @throws \TypeError if $newAtHandle is not a string
	 **/
	public function setAuthorAvatarUrl(string $newAuthorAvatarUrl): void {

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

	public function setAuthorUsername(string $newAuthorUsername): void {
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

	public function insert(\PDO $pdo): void {

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
	public function delete(\PDO $pdo): void {

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
	public function update(\PDO $pdo): void {

		// create query template
		$query = "UPDATE author SET authorActivationToken = :authorActivationToken, authorAvatarUrl = :authorAvatarUrl, authorEmail = :authorEmail, authorHash = :authorHash, authorUsername = :authorUsername WHERE authorId = :authorId";
		$statement = $pdo->prepare($query);


		$parameters = ["authorId" => $this->getAuthorId()->getBytes(), "authorActivationToken" => $this->authorActivationToken, "authorAvatarUrl" => $this->authorAvatarUrl, "authorEmail" => $this->authorEmail, "authorHash" => $this->authorHash, "authorUsername" => $this->authorUsername];
		$statement->execute($parameters);
	}


	public function getAuthorByUsername(\PDO $pdo, string $authorUsername) : \SplFixedArray {
		// create query template
		$authorUsername = trim($authorUsername);
		$author = filter_var($authorUsername, FILTER_SANITIZE_STRING, FILTER_FLAG_NO_ENCODE_QUOTES);
		if(empty($authorUsername) === true) {
			throw(new \PDOException("author username is invalid"));
		}

		// build an array of author
		$authorUsername = str_replace("_", "\\_", str_replace("%", "\\%", $authorUsername));

		$query = "SELECT authorId, authorActivationToken, authorAvatarUrl, authorEmail, authorHash, authorusername FROM author WHERE authorUsername LIKE :authorUsername";
		$statement = $pdo->prepare($query);

		$authorUsername = "%authorUsername%";
		$parameters = ["authorUsername" => $authorUsername];
		$statement->execute($parameters);

		$authors = new \SplFixedArray($statement->rowCount());
		$statement->setFetchMode(\PDO::FETCH_ASSOC);
		while(($row = $statement->fetch()) !== false) {
			try {
				$author = new Author($row["authorId"], $row["authorActivationToken"], $row["authorAvatarUrl"], $row["authorEmail"], $row["authorHash"], $row, ["authorusername"]);
				$authors[$authors->key()] = $author;
				$authors->next();
			} catch(\Exception $exception) {
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
		if($row !== false) {
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
		if($row !== false) {
			$author = new Author($row["authorId"], $row["authorActivationToken"], $row["authorAvatarUrl"], $row["authorEmail"], $row["authorHash"], $row["authorUsername"]);
		}
		return ($author);

	}

}




//
//	class Author implements \JsonSerializable{
//		use ValidateUuid;
//
//		/**
//		 * id for this Author; this is the primary key
//		 * @var Uuid $authorId
//		 */
//		private $authorId;
//
//		/**
//		 * activation token for this Author
//		 * @var string $authorActivationToken
//		 */
//		private $authorActivationToken;
//
//		/*
//		 * avatar url for Author
//		 * @var string $authorAvatarUrl
//		 */
//		private $authorAvatarUrl;
//
//		/*
//		 * email for this Author
//		 * @var string $authorEmail
//		 */
//		private $authorEmail
//
//		/*
//		 * hash for Author
//		 * @var string $authorHash
//		 */
//		private $authorHash;
//
//		/*
//		 * username for Author
//		 * @var string $authorUsername
//		 */
//		private $authorUsername;
//
//
//
//		/**
//		 * constructor for this Author
//		 *
//		 * @param string|Uuid $authorId id of this Author or null if a new Author
//		 * @param string $authorActivationToken activation token to safe guard against malicious accounts
//		 * @param string $authorAvatarUrl string containing an avatar url or null
//		 * @param string $authorEmail string containing email
//		 * @param string $authorHash string containing a password hash
//		 * @param string $authorUsername string containing a username
//		 * @throws \InvalidArgumentException if data types are not valid
//		 * @throws \RangeException if data values are out of bounds (e.g., strings too long, negative integers)
//		 * @throws \TypeError if data types violate type hints
//		 * @throws \Exception if some other exception occurs
//		 * @Documentation https://php.net/manual/en/language.oop5.decon.php
//		 *
//		 */
//
//		public function __construct($newAuthorId, string $newAuthorActivationToken, ?string $newAuthorAvatarUrl, string $newAuthorEmail, string $newAuthorHash, string $newAuthorUsername) {
//			try {
//				$this->setAuthorId($newAuthorId);
//				$this->setAuthorActivationToken($newAuthorActivationToken);
//				$this->setAuthorAvatarUrl($newAuthorAvatarUrl);
//				$this->setAuthorEmail($newAuthorEmail);
//				$this->setAuthorHash($newAuthorHash);
//				$this->setAuthorUsername($newAuthorUsername);
//			}catch(\InvalidArgumentException | \RangeException | \Exception | \TypeError $exception){
//				$exceptionType = get_class($exception);
//				throw(new $exceptionType($exception->getMessage(), 0, $exception));
//			}
//		}
//
//		/*
//		 * accessor method for author id
//		 *
//		 * @return Uuid value of author id
//		 */
//		public function getAuthorId() :Uuid {
//			return ($this->authorId);
//		}
//
//		/*
//		 * mutator method for author id
//		 *
//		 * @param Uuid|string $newAuthorId new value of author id
//		 * @throws \InvalidArgumentException if data types are not valid
//		 * @throws \RangeException if $newAuthorId is out of range
//		 * @throws \TypeError if $newAuthorId is not a uuid or string
//		 */
//
//		public function setAuthorId($newAuthorId): void {
//			try {
//				$uuid = self::validateUuid($newAuthorId);
//			}catch(\InvalidArgumentException | \RangeException | \Exception | \TypeError $exception){
//			$exceptionType = get_class($exception);
//			throw(new $exceptionType($exception->getMessage(), 0, $exception));
//			}
//			$this->authorId = $uuid;
//		}
//
//		/*
//		 * accessor method for author activation token
//		 *
//		 * @return string
//		 */
//		public function getAuthorActivationToken(): ?string{
//			return $this->authorActivationToken;
//		}
//
//		/*
//		 * mutator method for account activation token
//		 *
//		 * @param string $newAuthorActivationToken
//		 * @throws \InvalidArgumentException if the token is not a string or is insecure
//		 * @throws \RangeException if the token is not exactly 32 characters
//		 * @throws \TypeError if the activation token is not a string
//		 *
//		 */
//
//		public function setAuthorActivationToken(string $authorActivationToken) : void{
//			if($newAuthorActivationToken === null){
//				$this->authorActivationToken = null;
//				return;
//			}
//
//			$newAuthorActivationToken = strtolower(trim($newAuthorActivationToken));
//			if(ctype_xdigit($newAuthorActivationToken) ===false){
//				throw(new\RangeException("author activation is not valid"));
//			}
//
//			if(strlen($newAuthorActivationToken) !== 32){
//				throw(new\RangeException("author activation token has to be 32 characters"));
//			}
//
//			$this->authorActivationToken = $newAuthorActivationToken;
//		}
//
//		/**
//		 * accessor method for profile avatar url
//		 * @return string value of the activation token
//		 */
//
//	public function getAuthorAvatarUrl() : string {
//		return($this->authorAvatarUrl);
//	}
//
//	/**
//	 * mutator method for at handle
//	 *
//	 * @param string $newAuthorAvatarUrl new value of profile avatar URL
//	 * @throws \InvalidArgumentException if $newProfileAvatarUrl is not a string or insecure
//	 * @throws \RangeException if $newProfileAvatarUrl is > 255 characters
//	 * @throws \TypeError if $newAtHandle is not a string
//	 **/
//		 public function setAuthorAvatarUrl(string $newAuthorAvatarUrl) : void {
//
//		$newAuthorAvatarUrl = trim($newAuthorAvatarUrl);
//		$newAuthorAvatarUrl = filter_var($newAuthorAvatarUrl, FILTER_SANITIZE_STRING, FILTER_FLAG_NO_ENCODE_QUOTES);
//
//		// verify the avatar URL will fit in the database
//		if(strlen($newAuthorAvatarUrl) > 255) {
//			throw(new \RangeException("image cloudinary content too large"));
//		}
//		// store the image cloudinary content
//		$this->authorAvatarUrl = $newAuthorAvatarUrl;
//	}
//
//
//		/*
//		 * accessor method for author email
//		 *
//		 * @return string author email
//		 *
//		 */
//
//
//		public function getAuthorEmail(): string {
//			return $this->authorEmail
//		}
//
//		/*
//		 * mutator method for author email
//		 *
//		 * @param string $newProfileEmail new value of email
//	 	 * @throws \InvalidArgumentException if $newEmail is not a valid email or insecure
//		 * @throws \RangeException if $newEmail is > 128 characters
//	 	 * @throws \TypeError if $newEmail is not a string
//		 */
//
//		public function setAuthorEmail(string $newAuthorEmail): void {
//			// verify the email is secure
//			$newAuthorEmail = trim($newAuthorEmail);
//			$newAuthorEmail = filter_var($newAuthorEmail, FILTER_VALIDATE_EMAIL);
//			if(empty($newAuthorEmail) === true) {
//				throw(new \InvalidArgumentException("profile email is empty or insecure"));
//			}
//
//			// verify the email will fit in the database
//			if(strlen($newAuthorEmail) > 128) {
//				throw(new \RangeException("author email is too large"));
//			}
//
//			$this->authorEmail= $newAuthorEmail;
//
//		}
//
//		/*
//		 * accessor for author hash
//		 * @return string this author hash
//		 */
//
//		public function getAuthorHash(): string {
//			return $this->authorHash;
//		}
//
//
//		/*
//		 * mutator method for author hash password
//		 *
//		 * @param string $newAuthorHash
//		 * @throws \InvalidArgumentException if the is not an argon hash or is insecure
//		 * @throws \RangeException if the hash is larger than 97 characters
//		 * @throws \TypeError if author hash is not a string
//		 */
//
//		public function setAuthorHash(string $newAuthorHash): void {
//			//enforce that the hash is properly formatted
//			$newAuthorHash = trim($newAuthorHash);
//			if(empty($newAuthorHash) === true) {
//				throw(new \InvalidArgumentException("Author password hash empty or insecure"));
//			}
//
//			//enforce the hash is really an Argon hash
//			$authorHashInfo = password_get_info($newAuthorHash);
//			if($authorHashInfo["algoName"] !== "argon2i") {
//				throw(new \InvalidArgumentException("author hash is not a valid hash"));
//			}
//
//			//enforce that the hash is exactly 97 characters.
//			if(strlen($newAuthorHash) > 97){
//				throw(new \RangeException("author hash is too large"));
//			}
//
//			//store the hash
//			$this->authorHash = $newAuthorHash;
//			}
//
//		/**
//		 * accessor for author username
//		 * @return string
//		 */
//		public function getAuthorUsername(): string {
//
//			return $this->authorUsername;
//		}
//
//		/**
//		 * mutator for author username
//		 * @param string $authorUsername
//		 *
//		 * @throws \InvalidArgumentException if the is not an argon hash or is insecure
//		 * @throws \RangeException if the hash is larger than 97 characters
//		 * @throws \TypeError if author hash is not a string
//		 */
//
//
//	public function setAuthorUsername(string $newAuthorUsername) : void {
//		// verify the at handle is secure
//		$newAuthorUsername = trim($newAuthorUsername);
//		$newAuthorUsername = filter_var($newAuthorUsername, FILTER_SANITIZE_STRING, FILTER_FLAG_NO_ENCODE_QUOTES);
//		if(empty($newAuthorUsername) === true) {
//			throw(new \InvalidArgumentException("profile at handle is empty or insecure"));
//		}
//
//		// verify the at handle will fit in the database
//		if(strlen($newAuthorUsername) > 32) {
//			throw(new \RangeException("profile at handle is too large"));
//		}
//
//		// store the at handle
//		$this->authorUsername = $newAuthorUsername;
//	}
//
//
//
//
//
//
//
//		/**
//		 * @inheritDoc
//		 */
//		public function jsonSerialize(){
//			$fields = get_object_vars($this);
//			$fields["authorId"] = $this->authorId->toString();
//			unset($fields["authorActivationToken"]);
//			unset($fields["authorHash"]);
//			return($fields);
//	}
//	}
