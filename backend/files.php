<?php
/******
PhpPiratebox
Copyright 2013 Sergio Monedero
Released under GPLv3, see http://www.gnu.org/licenses/gpl-3.0.html
******/

require("../config.php");   // $upload_folder_path

function getListOfFiles($path) {
    $files = array();

    $tmp_file_list = scandir($path, SCANDIR_SORT_NONE);
    for ($pos = 0; $pos < count($tmp_file_list); $pos++) {
        if (is_file($path . $tmp_file_list[$pos])) {
            $file = array();
            
            $file["fileName"] = $tmp_file_list[$pos];
            $file["size"] = filesize($tmp_file_list[$pos]);
            $file["lastAccessTime"] = (fileatime($tmp_file_list[$pos])) ? fileatime($tmp_file_list[$pos]) : filectime($tmp_file_list[$pos]) ;
            
            if(!$file["size"]) $file["size"] = 0;
            if(!$file["lastAccessTime"]) $file["lastAccessTime"] = 0;
            
            $files[] = $file;
        }
    }

    return $files;
}

function orderByAtimeSizeASC($a, $b) {
    if ($a["lastAccessTime"] > $b["lastAccessTime"]) {
        return 1;
    } elseif ($a["lastAccessTime"] == $b["lastAccessTime"]) {
        if ($a["size"] > $b["size"]) {
            return -1;
        } else {
            return 1;
        }
    } else {
        return -1;
    }
}

if (isset($_GET["download_file"])) {
    $file_name = str_replace("/","",$_GET["download_file"]);
    $file_path = $upload_folder_path . $file_name;

    if (file_exists($file_path)) {
        touch($file_path);  // Update access time.

        header("Content-type: application/octet-stream");
        header("Content-Disposition: attachment; filename=\"" . $file_name . "\"");
        readfile($file_path);
    } else {
        header("HTTP/1.0 404 Not Found");
    }
} else {
    $response = array();

    if (isset($_POST['action'])) {
        switch ($_POST['action']) {
            case "getFileList":
                $path = $upload_folder_path;
                $files = getListOfFiles($path);
                $response["content"]["fileList"] = $files;
                break;
            case "getFileInfo";
                $response["error"] = "Not implemented";
                break;
            case "getMaxFileSize":
                $response["content"]["maxFileSize"] = ini_get("upload_max_filesize");
                break;
            case "uploadFile":
                if ($_FILES["file"]["error"] > 0) {
                    switch ($_FILES["file"]["error"]) {
                        case UPLOAD_ERR_INI_SIZE:
                            $response["error"] = "The file is too big. Files must be smaller than " . ini_get("upload_max_filesize");
                            break;
                        case UPLOAD_ERR_NO_FILE:
                            $response["error"] = "No file detected.";
                            break;
                        case UPLOAD_ERR_CANT_WRITE:
                            $response["error"] = "Failed to write file to disk.";
                            break;
                        default:
                            $response["error"] = "Unknown error. Error code: " . $_FILES["file"]["error"];
                            break;
                    }
                } else {
                    $fileList = getListOfFiles($upload_folder_path);
                    $sortedFileList = usort($fileList, "orderByAtimeSizeASC");

                    if(disk_free_space($upload_folder_path)!=false) {
                        while (disk_free_space($upload_folder_path) < $_FILES["file"]["size"]) {
                            for ($pos = 0; $pos < sizeof($sortedFileList); $pos++) {
                                if ($sortedFileList[$pos]["size"] >= $_FILES["file"]["size"]) {
                                    if (unlink($upload_folder_path . $sortedFileList[$pos]["filename"])) {
                                        break;
                                    }
                                }
                            }
                        }
                    }

                    $upload_complete = false;

                    $file_name = $_FILES["file"]["name"];
                    $counter = 0;
                    do {
                        if ($counter > 0) {
                            $x = strlen($_FILES["file"]["name"]);
                            while ($x > 0 && substr($_FILES["file"]["name"], $x, 1) != ".") {
                                $x--;
                            }

                            if ($x > 0) {
                                $file_name = substr($_FILES["file"]["name"], 0, $x) . "_" . $counter . substr($_FILES["file"]["name"], $x);
                            } else {
                                $file_name = $_FILES["file"]["name"] . "_" . $counter;
                            }
                        }

                        $tmp_file_path = $upload_folder_path . $file_name;
                        if (file_exists($tmp_file_path)) {
                            $counter++;
                        } else {
                            if(move_uploaded_file($_FILES["file"]["tmp_name"], $tmp_file_path)) {
                                chmod($tmp_file_path, 0666);
                                $response["message"] = "File was uploaded successfully. Saved as \"$file_name\".";
                            } else {
                                $response["error"] = "move_uploaded_file() failed. Check permissions.";
                            }
                            $upload_complete = true;
                        }
                    } while (!$upload_complete);

                    
                }
                break;
            default:
                $response["error"] = "Invalid action.";
                break;
        }
    } else {
        $response["error"] = "No action defined.";
    }

    $response["isError"] = (isset($response["error"]) ? true : false);

    echo(json_encode($response));
}
?>
