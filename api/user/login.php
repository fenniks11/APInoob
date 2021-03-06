<?php
// required headers
header("Access-Control-Allow-Origin: http://localhost/rest-api-authentication-example/");
header("Content-Type: application/json; charset=UTF-8");
header("Access-Control-Allow-Methods: POST");
header("Access-Control-Max-Age: 3600");
header("Access-Control-Allow-Headers: Content-Type, Access-Control-Allow-Headers, Authorization, X-Requested-With");

// database connection will be here
// files needed to connect to database
include_once '../config/database.php';
include_once '../objects/user.php';

// get database connection
$database = new Database();
$db = $database->getConnection();

// instantiate user object
$user = new User($db);

// check username existence here
// get posted data
$data = json_decode(file_get_contents("php://input"));

// set product property values
$user->username = $data->username;
$username_exists = $user->usernameExists();

// files for jwt will be here
// generate json web token
include_once '../config/core.php';
include_once '../libs/php-jwt-main/src/BeforeValidException.php';
include_once '../libs/php-jwt-main/src/ExpiredException.php';
include_once '../libs/php-jwt-main/src/SignatureInvalidException.php';
include_once '../libs/php-jwt-main/src/JWT.php';

use \Firebase\JWT\JWT;

// generate jwt will be here
// check if username exists and if password is correct
if ($username_exists && password_verify($data->password, $user->password)) {

    $token = array(
        "iat" => $issued_at,
        "exp" => $expiration_time,
        "iss" => $issuer,
        "data" => array(
            "id" => $user->id,
            "username" => $user->username
        )
    );
    $refresh_token = array(
        "iat" => $issued_at,
        "iss" => $issuer,
        "data" => array(
            "id" => $user->id,
            "username" => $user->username
        )
    );


    // set response code
    http_response_code(200);

    // generate jwt
    $jwt = JWT::encode($token, $key, 'HS256');
    $jwt_refresh = JWT::encode($refresh_token, $key, 'HS256');
    echo json_encode(
        array(
            "message" => "Successful login.",
            "jwt" => $jwt,
            "expired jwt" => $expiration_time,
            "refresh token" => $jwt_refresh
        )
    );
}

// login failed will be here
// login failed
else {

    // set response code
    http_response_code(401);

    // tell the user login failed
    echo json_encode(array("message" => "Login failed."));
}
