<?php

if (isset($_POST['API_act'])) { // && !is_cli()
    switch ($_POST['API_act']) {
        case 'list':
            $db = \Database::execute("SELECT handle, `name`, start_time, end_time FROM app_nlc_sesi");
            $data = [];
            while ($row = $db->fetch_array(MYSQLI_ASSOC)) {
                $data[] = $row;
            }
            echo json_encode($data) . "\n";
            break;
        case 'info':
            $db = \Database::execute("SELECT `name`, start_time, end_time FROM app_nlc_sesi WHERE handle = '?'", $_POST['handle']);
            $data = [];
            while ($row = $db->fetch_array(MYSQLI_ASSOC)) {
                $data[] = $row;
            }
            echo json_encode($data) . "\n";
            break;
        default:
            return false;
    }
    exit();
}

return "default";
