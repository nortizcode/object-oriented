<?php

require_once(dirname(__DIR__) . "/Classes/autoload.php");
require_once \Nortiz41\ObjectOriented\Author::


	$authorId= "7214d724-3f4e-4691-bd3d-9540adb91b14";
	$authorActivationToken= bin2hex(random_bytes(16));
	$authorEmail= "nkortiz92@gmail.com";
	$authorHash= "password";
	$authorAvatarUrl= "https://avatar.com";
	$authorUsername= "nathan-ortiz";


$author = new \Nortiz41\ObjectOriented\Author($authorId, $authorActivationToken,$authorEmail, $authorHash, $authorAvatarUrl, $authorUsername);

	var_dump($author);