<?php
/******
PhpPiratebox
Copyright 2013 Sergio Monedero
Released under GPLv3, see http://www.gnu.org/licenses/gpl-3.0.html
******/

require("../config.php");   // $wall_messages_file, $max_wall_messages

function getMessageArray($wall_messages_file) {
    $messages = array();

    if (file_exists($wall_messages_file) && filesize($wall_messages_file) > 0) {
        $file_open_handler = fopen($wall_messages_file, "r");
        $json_data = fread($file_open_handler, filesize($wall_messages_file));
        fclose($file_open_handler);
        $messages = json_decode($json_data);
    }

    return $messages;
}

$response = array();

if (isset($_POST["action"])) {
    switch ($_POST["action"]) {
        case "postMessage":
            if (!(isset($_POST["message"]))) {
                $response["error"] = "Wall items must have a message";
            } else {
                $message = strip_tags($_POST["message"]);
                if (strlen($message) < 1) {
                    $response["error"] = "Message is empty.";
                } else {
                    $username = (isset($_POST["username"]) ? strip_tags($_POST["username"]) : null);

                    $message_data = array();
                    $message_data["message"] = $message;
                    $message_data["username"] = ((strlen($username) > 0) ? $username : null);
                    $message_data["date"] = date("h:m d/m/y");

                    $saved_messages = getMessageArray($wall_messages_file);
                    $number_of_messages = count($saved_messages);
                    if ($number_of_messages > $max_wall_messages) {
                        unset($saved_messages[$number_of_messages - 1]);
                    }

                    $messages = array();
                    $messages[] = $message_data;
                    foreach ($saved_messages as $message) {
                        $messages[] = $message;
                    }

                    $json_data = json_encode($messages);

                    $file_write_handler = fopen($wall_messages_file, "w+");
                    fwrite($file_write_handler, $json_data);
                    fclose($file_write_handler);

                    $response["content"]["message"] = "Message was published successfully";
                }
            }
            break;
        case "getMessages":
            $messages = getMessageArray($wall_messages_file);
            $response["content"]["messages"] = $messages;
            break;
        default:
            $response["error"] = "Invalid action";
    }
} else {
    $response["error"] = "No action defined";
}

$response["isError"] = (isset($response["error"]) ? true : false);

echo(json_encode($response));
?>
