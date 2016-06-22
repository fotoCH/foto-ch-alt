<?php

/*
define("USER_GUEST_READER",2);
define("USER_GUEST_FOTOS", 3);
define("USER_GUEST_READER_PARTNER", 4);
define("USER_WORKER", 8);
define("USER_SUPER_USER", 9);
*/

$rh = new RequestHandler();

if($rh->allowed()) {
    if($rh->checkRequest()) {
        $rh->request();
    } else {
        $rh->invalid();
    }
} else {
    $rh->denied();
}


class RequestHandler {
    private $error = '';

    private $value = '';
    private $type = '';
    private $field = '';
    private $entry = '';
    private $institution = false;

    private $typeTable = array(
        'photo' => 'fotos'
    );

    public function request() {
        // is directly allowed to update?
        if($this->allowed(USER_WORKER)) {
            switch($this->action) {
                case 'pendingAmount':
                    $this->getPending();
                    break;
                case 'pendingList':
                    $this->getRequests();
                    break;
                case 'rejectedList':
                    $this->getRequests('rejected');
                    break;
                case 'acceptedList':
                    $this->getRequests('accepted');
                    break;
                case 'reject':
                    $this->reject();
                    break;
                case 'accept':
                    $this->accept();
                    break;
                default:
                    $this->updateValue();
                    break;
            }
            // direct update
            // TODO
        } else if ( ! $this->action ) {
            if($this->institution && testInstitution($this->institution)) {
                $this->appendToQueue();
            } else {
                $this->denied();
            }
        }
    }

    private function reject() {
        if(! array_key_exists('id', $_GET)) {
            return;
        }

        $query = "UPDATE request_queue";
        $query.= " SET action = 'rejected', action_user = '".auth_user('user')."'";
        $query.= " WHERE id = ".$_GET['id'];

        if(mysql_query($query)) {
            jsonout(array('rejection' => 'success'));
        } else {
            jsonout(array('rejection' => 'failed'));
        }
    }

    private function accept() {
        if(! array_key_exists('id', $_GET)) {
            return;
        }


        $this->updateQueueItem($_GET['id']);
        $this->updateValue();


        // update request_queue item
        $query = "UPDATE request_queue";
        $query.= " SET action = 'accepted', action_user = '".auth_user('user')."'";
        $query.= " WHERE id = ".$_GET['id'];

        if(mysql_query($query)) {
            jsonout(array('acception' => 'success'));
        } else {
            jsonout(array('acception' => 'failed'));
        }
    }

    private function updateValue() {
        // some checks...
        if(! is_numeric($this->entry)) {
            return;
        }
        if(array_key_exists('overwrite', $_GET)) {
            $this->value = $_GET['overwrite'];
        }

        $query = "UPDATE ".$this->typeTable[$this->type];
        $query.= " SET ".$this->field." = '".$this->value."'";
        $query.= " WHERE id=".$this->entry;

        mysql_query($query);
    }

    private function updateQueueItem($id) {
        $sql = "SELECT * FROM request_queue WHERE id =".$id;
        $result = mysql_query($sql);
        while($row = mysql_fetch_assoc($result)) {
            $this->entry = $row['entry'];
            $this->field = $row['field'];
            $this->type = $row['type'];
            $this->value = $row['request'];
        }
    }

    private function getPending() {
        $query = "SELECT COUNT(*) as 'num' FROM request_queue WHERE action IS NULL";
        $set = mysql_query($query);
        $count = 0;
        while($row = mysql_fetch_assoc($set)) {
            $count = $row['num'];
        }

        jsonout(array(
            'count' => $count
        ));
    }

    private function getRequests($type = 'pending') {
        $query = "SELECT * FROM request_queue WHERE";
        if($type == 'pending') {
            $query.= " action IS NULL";
        } elseif($type == 'accepted') {
            $query.= " action = 'accepted'";
        } elseif($type == 'rejected') {
            $query.= " action = 'rejected'";
        }
        $query.= " ORDER BY date DESC";

        $result = mysql_query($query);
        $data = [];
        while($row = mysql_fetch_assoc($result)) {
            $this->field = $row['field'];
            $this->type = $row['type'];
            $this->entry = $row['entry'];
            $data[] = array_merge( $row, array('current' => $this->currentValue()));
        }

        jsonout($data);
    }

    public function appendToQueue() {
        if( $this->currentValue() == $this->value ) {
            jsonout(array(
                "queue" => "no_change"
            ));
        } else {
            $query = "INSERT INTO request_queue";
            $query.= " (user, entry, field, type, request) ";
            $query.= "VALUES (
                        '".auth_user('user')."',
                        ".$this->entry.",
                        '".$this->field."',
                        '".$this->type."',
                        '".$this->value."'
                        )";
            mysql_query($query);

            jsonout(array(
                "queue" => "append"
            ));
        }

    }

    private function currentValue() {
        $query = "SELECT ".$this->field." FROM ".$this->typeTable[$this->type]. " WHERE id=".$this->entry. ' LIMIT 1';
        $result = mysql_query($query);
        $count = 0;
        $value = '';
        while($row = mysql_fetch_assoc($result)) {
            $value = $row[$this->field];
        }
        return $value;
    }

    public function allowed($level = false) {
        if(! $level) {
            return auth_level(USER_GUEST_FOTOS);
        } else {
            return auth_level($level);
        }
    }

    public function denied() {
        jsonout(array(
            "access" => "denied"
        ));
    }

    public function checkRequest() {
        if( array_key_exists( 'action' , $_GET)) {
            $this->action = $_GET['action'];
            return true;
        }

        if( ! array_key_exists('entry', $_GET) ) {
            $this->error = 'No entry given.';
            return false;
        }
        if( ! is_numeric($_GET['entry']) ) {
            $this->error = 'Invalid Entry: '. $_GET['entry'];
            return false;
        }
        if( ! array_key_exists('field', $_GET) ) {
            $this->error = 'No field to request given.';
            return false;
        }
        if( ! array_key_exists('type', $_GET) ) {
            $this->error = 'No type to request given.';
            return false;
        }
        if( ! array_key_exists('value', $_GET) ) {
            $this->error = 'No new value set.';
            return false;
        }

        if(! array_key_exists($_GET['type'], $this->typeTable) ) {
            $this->error = 'Unknown Type: '.$_GET['type'];
            return false;
        }

        if( array_key_exists('institution', $_GET)) {
            $this->institution = $_GET['institution'];
        }

        $this->entry = $_GET['entry'];
        $this->field = $_GET['field'];
        $this->type = $_GET['type'];
        $this->value = $_GET['value'];

        return true;
    }

    public function invalid() {
        jsonout(array(
            "request" => "invalid",
            "error" => $this->error
        ));
    }

}

?>