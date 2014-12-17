<?php

session_start();

function respond($status, $response) {
    if (!$status) {
        $output = array("Status" => false, "Error" => $response);
    } else {
        $server_info = array(
            "Hostname" => gethostname(),
            "IpAddress" => filter_input(INPUT_SERVER, "SERVER_ADDR"),
            "LoadAverage" => sys_getloadavg()
        );
        $output = array("Status" => true, "Response" => $response, "ServerInfo" => $server_info);
    }
    header("Content-Type: text/json");
    echo json_encode($output);
    die();
}

error_reporting(E_ALL);
ini_set("display_errors", "1");
$db_host = "localhost";
$db_user = "lbchat_dev";
$db_password = "Le53g58GCs";
$db_name = "lbchat_dev";

$mysqli = new mysqli($db_host, $db_user, $db_password, $db_name);

$action = filter_input(INPUT_GET, "_q");

switch ($action) {
    case "present": {
            $mysqli->query("UPDATE `lbchat_session` SET `end_date` = CURRENT_TIMESTAMP WHERE `lbchat_session`.`end_date` IS NULL AND UNIX_TIMESTAMP() - UNIX_TIMESTAMP(`lbchat_session`.`presence_date`) > 30");

            $user_ip = filter_input(INPUT_SERVER, "REMOTE_ADDR");
            $query_sid = filter_input(INPUT_GET, "_s");
            if (!isset($_SESSION["lbchat_session_id"]) && $query_sid != null) {
                $result = $mysqli->query("SELECT * FROM `lbchat_session` WHERE `id` = \"$query_sid\" AND `ip_address` = \"$user_ip\" AND ");
                if ($result->num_rows > 0) {
                    $row = $result->fetch_array(MYSQL_ASSOC);
                    $_SESSION["lbchat_session_id"] = mysqli_insert_id($mysqli);
                }
            }

            if (isset($_SESSION["lbchat_session_id"])) {
                $session_id = $_SESSION["lbchat_session_id"];
                $session_result = $mysqli->query("SELECT *, UNIX_TIMESTAMP() - UNIX_TIMESTAMP(`lbchat_session`.`presence_date`) as `delta` FROM `lbchat_session` WHERE `id` = \"$session_id\" AND `end_date` IS NULL");
                if ($session_result->num_rows > 0) {
                    $session_row = $session_result->fetch_array(MYSQLI_ASSOC);
                    if ($session_row["delta"] > 30) {
                        respond(false, "SessionTimedOut");
                    } else {
                        $user_list = array();
                        $result = $mysqli->query("SELECT `lbchat_user`.`username` as `username`, `lbchat_user`.`id` as `userid`, UNIX_TIMESTAMP() - UNIX_TIMESTAMP(`lbchat_session`.`presence_date`) as `last_seen` FROM `lbchat_session` LEFT JOIN `lbchat_user` ON `lbchat_user`.`id` = `lbchat_session`.`user_id`  WHERE `lbchat_session`.`end_date` IS NULL AND UNIX_TIMESTAMP() - UNIX_TIMESTAMP(`lbchat_session`.`presence_date`) < 30");
                        while ($row = $result->fetch_array(MYSQL_ASSOC)) {
                            $user_list[] = array("Username" => $row["username"], "Id" => $row['userid'], "LastSeen" => $row["last_seen"]);
                        }
                        $mysqli->query("UPDATE `lbchat_session` SET `presence_date` = CURRENT_TIMESTAMP WHERE `id` = \"$session_id\"");
                        respond(true, array("UserList" => $user_list));
                    }
                }
            }
            respond(false, "Unauthorized");
            break;
        }
    case "login": {
            $username = filter_input(INPUT_POST, "username");
            $password = sha1(filter_input(INPUT_POST, "password"));
            $ipaddr = filter_input(INPUT_SERVER, "REMOTE_ADDR");
            $result = $mysqli->query("SELECT `id`, `username` FROM `lbchat_user` WHERE `username` = \"$username\" AND `password` = \"$password\"");
            if ($result->num_rows > 0) {
                $row = $result->fetch_array(MYSQL_ASSOC);
                $user_id = $row["id"];
                $insert_result = $mysqli->query("INSERT INTO `lbchat_session` (`user_id`, `ip_address`) VALUES (\"$user_id\", \"$ipaddr\")");
                if ($insert_result) {
                    $_SESSION["lbchat_session_id"] = mysqli_insert_id($mysqli);
                    $mysqli->query("UPDATE `lbchat_user` SET `last_login_date` = CURRENT_TIMESTAMP, `last_ip_address` = \"$ipaddr\"");
                } else {
                    respond(false, "InternalError");
                }
                respond(true, array("Username" => $row["username"], "Id" => $row["id"], "IpAddress" => $ipaddr, "SessionId" => $_SESSION["lbchat_session_id"]));
            } else {
                respond(false, "InvalidCredentials");
            }
        }
    case "register": {
            $username = trim(filter_input(INPUT_POST, "username"));
            $password = filter_input(INPUT_POST, "password");
            $password_hash = sha1($password);

            if (strlen($username) < 5) {
                respond(false, "UsernameTooShort");
            } else if (strlen($password) < 5) {
                respond(false, "PasswordTooShort");
            }

            $check_query = $mysqli->query("SELECT COUNT(*) as `count` FROM `lbchat_user` WHERE `username` = \"$username\"");
            $check_row = $check_query->fetch_array(MYSQL_ASSOC);
            if ($check_row["count"] > 0) {
                respond(false, "UsernameAlreadyTaken");
            } else {
                $mysqli->query("INSERT INTO `lbchat_user` (`username`, `password`) VALUES (\"$username\", \"$password_hash\")");
                respond(true, array("Id" => mysqli_insert_id($mysqli)));
            }
        }
    default: {
            respond(false, "InvalidAction");
            break;
        }
}