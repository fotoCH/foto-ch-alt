<?php

$userManagement = new UserManagement();

if ($userManagement->allowed()) {
    if ($userManagement->checkRequest()) {
        $userManagement->request();
    } else {
        $userManagement->invalid();
    }
} else {
    $userManagement->denied();
}

class UserManagement
{
    private $error = '';
    private $action = '';

    private function getUsers()
    {
        /*
       SELECT u.username AS username, u.vorname AS vorname, u.nachname AS nachname, u.inst_comment AS inst_comment, u.level AS level, GROUP_CONCAT( bu.bestand_id
       SEPARATOR  ',' ) AS stocks
       FROM users u
       JOIN bestand_users bu ON bu.user_id = u.id
       ORDER BY nachname, vorname
       */
        $query_user = "SELECT * FROM users u ORDER BY nachname, vorname";
        $result_user = getfromselect($query_user);

        $query_stocks = "SELECT b.name as name, bu.user_id as user_id, b.id as id FROM bestand b JOIN bestand_users bu ON bu.bestand_id = b.id";
        $result_stocks = getfromselect($query_stocks);

        foreach ($result_user as $userKey => $user) {
            $result_user[$userKey]['stocks'] = array();
            if (count($result_stocks) > 0) {
                // only iterate over stocks if left
                foreach ($result_stocks as $key => $relatedStock) {
                    if ($relatedStock['user_id'] == $user['id']) {
                        $result_user[$userKey]['stocks'][] = array('name' => $relatedStock['name'], 'id' => $relatedStock['id']);
                        // remove stock if realtion is set
                        unset($result_stocks[$key]);
                    }
                }
            }
        }
        jsonout($result_user);
    }

    private function getStocks()
    {
	global $sqli;
        $stocks = array();

        // copied from filters->photoStocks (only stocks with photos)
        $query = "SELECT DISTINCT dcterms_ispart_of as 'value' FROM fotos";
        $set = mysqli_query($sqli, $query);
        while ($row = mysqli_fetch_assoc($set)) {
            $possible = $row['value'];
            if (!array_key_exists($possible, $stocks)) {
                $stockQuery = "SELECT name FROM bestand WHERE id=" . $possible;
                $result = mysqli_query($sqli, $stockQuery);
                while ($stock = mysqli_fetch_assoc($result)) {
                    array_push($stocks, array(
                        "id" => $possible,
                        "value" => $stock['name']
                    ));
                }
            }
        }
        jsonout($stocks);
    }

    private function changeUserLevel()
    {
	global sqli;
        if (is_numeric($_GET['id']) && is_numeric($_GET['level'])) {
            $update_query = "UPDATE users SET level=" . $_GET['level'] . " WHERE id=" . $_GET['id'];

            mysqli_query($sqli, $update_query) ? jsonout(array('changeUserLevel' => 'success')) : jsonout(array('changeUserLevel' => 'database error (' . mysqli_error($sqli) . ')'));
            return;
        } else {
            jsonout(array('changeUserLevel' => 'Paremeter are not numeric or not set.'));
            return;
        }
    }

    private function deleteUser()
    {
	global $sqli;
        if (is_numeric($_GET['id'])) {
            $delete_query = "DELETE FROM users WHERE id=" . $_GET['id'];

            $delete_relations_query = "DELETE FROM bestand_users WHERE user_id=" . $_GET['id'];
            mysqli_query($sqli, $delete_relations_query);

            mysqli_query($sqli, $delete_query) ? jsonout(array('deleteUser' => 'success')) : jsonout(array('deleteUser' => 'database error (' . mysqli_error($sqli) . ')'));
            return;
        } else {
            jsonout(array('deleteUser' => 'Paremeter are not numeric or not set.'));
            return;
        }
    }

function array_map_callback($a)
{
  global $sqli;
  return mysqli_real_escape_string($sqli, $a);
}


    private function addUser()
    {

	global $sqli;
        // todo POST request!
        /*
        $user = json_decode(file_get_contents("php://input"));
        print_r($user);
        */

        $userData = $_REQUEST;
        $stocks = array_filter(explode(',', $_REQUEST['stocks']));

        unset($userData['a']);
        unset($userData['action']);
        unset($userData['stocks']);

        $userData = array_map('array_map_callback', $userData);
        $userData['password'] = md5($userData['password']);

        $query = "INSERT INTO users (";
        $query .= implode(', ', array_keys($userData)) . ")";
        $query .= " VALUES('" . implode("', '", $userData) . "')";

        if (count($stocks) > 0) {
            $insert_result = mysqli_query($sqli, $query);
            if ($insert_result) {
                $user_id = mysqli_fetch_assoc(mysqli_query($sqli, "SELECT id FROM users WHERE username='" . $userData['username'] . "' LIMIT 1"))['id'];
                $stocks_query = "INSERT INTO bestand_users (bestand_id, user_id)";
                $stocks_values = array();
                foreach ($stocks as $stock) {
                    $stocks_values[] = "(" . $stock . "," . $user_id . ")";
                }
                $stocks_query .= " VALUES " . implode(',', $stocks_values) . ";";
                return mysqli_query($sqli, $stocks_query) ? jsonout(array('addUser' => 'success')) : jsonout(array('addUser' => 'database error: ' . mysqli_error($sqli)));
            } else {
                jsonout(array('addUser' => 'database error: ' . mysqli_error($sqli)));
            }
        } else {
            return mysqli_query($sqli, $query) ? jsonout(array('addUser' => 'success')) : jsonout(array('addUser' => 'database error: ' . mysqli_error($sqli)));
        }

    }


    private function changeStocks()
    {
	global $sqli;
        $stocks = array_filter(explode(',', $_REQUEST['stocks']));
        $userId = $_REQUEST['user'];

        if (is_numeric($userId)) {
            // delete former relations
            $delete_query = "DELETE FROM bestand_users WHERE user_id=" . $userId;
            if (mysqli_query($sqli, $delete_query)) {
                if (count($stocks) > 0) {
                    // insert new relations
                    $stocks_query = "INSERT INTO bestand_users (bestand_id, user_id)";
                    $stocks_values = array();
                    foreach ($stocks as $stock) {
                        $stocks_values[] = "(" . $stock . "," . $userId . ")";
                    }
                    $stocks_query .= " VALUES " . implode(',', $stocks_values) . ";";
                    return mysqli_query($sqli, $stocks_query) ? jsonout(array('changeStocks' => 'success')) : jsonout(array('changeStocks' => 'database error: ' . mysqli_error($sqli)));
                }
                return jsonout(array('changeStocks' => 'success'));

            } else {
                return jsonout(array('changeStocks' => 'database error: ' . mysqli_error($sqli)));
            }
        }
    }


    /**
     *  START Meta functions from request.php
     */

    public function request()
    {
        // is directly allowed to update?
        if ($this->allowed(USER_WORKER)) {
            switch ($this->action) {
                case 'getUsers':
                    $this->getUsers();
                    break;
                case 'changeStocks':
                    $this->changeStocks();
                    break;
                case 'changeUserLevel':
                    $this->changeUserLevel();
                    break;
                case 'deleteUser':
                    $this->deleteUser();
                    break;
                case 'addUser':
                    $this->addUser();
                    break;
                case 'getStocks':
                    $this->getStocks();
                    break;
                default:
                    $this->getUsers();
                    break;
            }
        } else {
            $this->denied();
        }
    }

    public function checkRequest()
    {
        if (array_key_exists('action', $_GET)) {
            $this->action = $_GET['action'];
            return true;
        }

        $this->error = 'action undefined';
        return false;
    }

    public function allowed($level = false)
    {
        if (!$level) {
            return auth_level(USER_GUEST_FOTOS);
        } else {
            return auth_level($level);
        }
    }

    public function denied()
    {
        jsonout(array(
            "access" => "denied"
        ));
    }

    public function invalid()
    {
        jsonout(array(
            "request" => "invalid",
            "error" => $this->error
        ));
    }
    /**
     *  END Meta functions from request.php
     */

}

?>