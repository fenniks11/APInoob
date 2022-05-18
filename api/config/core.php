<?php
// show error reporting
error_reporting(E_ALL);

// set your default time-zone
date_default_timezone_set('Asia/Manila');

// variables used for jwt
$key = "asnjandjnsjdaaksd01i32-43252714832skansdjna23712840235-5ir37bbdsgds*&T*U$&**)UJd28293i9y4";
$issued_at = time();
$expiration_time = $issued_at + (60 * 60); // valid for 15 minutes.
$issuer = "http://localhost/APInoob/API/";
