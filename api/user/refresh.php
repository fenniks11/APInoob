<?php
// required headers
header("Access-Control-Allow-Origin: *");
header("Content-Type: application/json; charset=UTF-8");
header("Access-Control-Allow-Methods: POST");
header("Access-Control-Max-Age: 3600");
header("Access-Control-Allow-Headers: Content-Type, Access-Control-Allow-Headers, Authorization, X-Requested-With");

// files for decoding jwt will be here
// required to encode json web token
include_once '../config/core.php';
require_once '../libs/php-jwt-main/src/BeforeValidException.php';
require_once '../libs/php-jwt-main/src/ExpiredException.php';
require_once '../libs/php-jwt-main/src/SignatureInvalidException.php';
require_once '../libs/php-jwt-main/src/JWT.php';
require_once '../libs/php-jwt-main/src/JWT.php';
require_once '../libs/php-jwt-main/src/Key.php';

use \Firebase\JWT\JWT;
use \Firebase\JWT\Key;

// database connection will be here
// files needed to connect to database
include_once '../config/database.php';
include_once '../objects/user.php';

// get database connection
$database = new Database();
$db = $database->getConnection();

// instantiate user object
$user = new User($db);

// retrieve given jwt heres
// get posted data
$data = json_decode(file_get_contents("php://input"));

// get jwt
$refresh_token = isset($data->refresh_token) ? $data->refresh_token : "";

// decode refresh_token here
// if refresh_token is not empty
if ($refresh_token) {

    // if decode succeed, show user details
    try {
        $decoded = JWT::decode($refresh_token, new Key($key, 'HS256'));
        // decode refresh_token
        if ($decoded == true) {
            $token = array(
                "iat" => $issued_at,
                "exp" => $expiration_time,
                "iss" => $issuer,
                "data" => array(
                    "id" => $decoded->data->id,
                    "username" => $decoded->data->username
                )
            );

            $jwt = JWT::encode($token, $key, 'HS256');

            // set response code
            http_response_code(200);

            // response in json format
            echo json_encode(
                array(
                    "message" => "Successful get new token access.",
                    "jwt" => $jwt,
                    "expiration_time" => $expiration_time
                )
            );
        } else {
            http_response_code(401);

            // show error message
            echo json_encode(array("message" => "Unable to get new token."));
        }
    }

    // catch failed decoding will be here
    // if decode fails, it means jwt is invalid
    catch (Exception $e) {

        // set response code
        http_response_code(401);

        // show error message
        echo json_encode(array(
            "message" => "Access denied.",
            "error" => $e->getMessage()
        ));
    }
}

// error message if jwt is empty will be here
// show error message if jwt is empty
else {

    // set response code
    http_response_code(401);

    // tell the user access denied
    echo json_encode(array("message" => "Access denied."));
}
