<?php

ini_set('display_errors', '1');
error_reporting(E_ERROR | E_WARNING | E_PARSE | E_NOTICE);

$pm = new ProjectManagement($_GET['type']);

class ProjectManagement {
    private $sqli = null;

    public function __construct($type) {
        global $sqli;
        $this->sqli = $sqli;

        switch($type) {
            case 'create':
                $this->create();
                break;
            case 'list':
                $this->getList();
                break;
            case 'delete':
                $this->delete();
                break;
            case 'get':
                $this->get();
                break;
            case 'update':
                $this->update();
                break;
            default:
                jsonout(['NULL' => 'NULL']);
        }
    }

    private function get() {
        $id = false;
        if(array_key_exists('title', $_GET)) {
            $id = $this->getIdByTitle($_GET['title']);
        }
        if(! array_key_exists('id', $_GET) && !$id) {
            jsonout(['MISSING' => 'ID']);
            return;
        }
        if(array_key_exists('id', $_GET) && ! is_numeric($_GET['id']) && ! is_numeric($id)) {
            jsonout(['STRANGE' => 'ID']);
            return;
        }
        if(! $id) {
            $id = $_GET['id'];
        }

        $selectQuery = "SELECT * FROM projects WHERE id=".mysqli_real_escape_string($this->sqli, $id);
        $result = mysqli_query($this->sqli, $selectQuery);
        while($row = mysqli_fetch_assoc($result)) {
            $row = $this->addUrl($row);
            jsonout($row);
            break;
        }
    }
    private function getIdByTitle($title) {
        $selectQuery = "SELECT title, id FROM projects";
        $result = mysqli_query($this->sqli, $selectQuery);
        while($row = mysqli_fetch_assoc($result)) {
            if($this->urlify($row['title']) == $title) {
                return $row['id'];
            }
        }
        return false;
    }

    private function addUrl($row) {
        if(array_key_exists('title', $row)) {
            $row['url'] = $this->urlify($row['title']);
        }
        return $row;
    }

    private function urlify($name) {
        $fromArray = [
            ':',' ','ä','ö','ü','^','\'','.',',','!','?'
        ];
        $toArray = [
            '','-','ae','oe','ue','','','','','',''
        ];
        return str_replace($fromArray, $toArray, strtolower($name));
    }

    private function getList() {
        $selectQuery = "SELECT * FROM projects";
        $result = mysqli_query($this->sqli, $selectQuery);
        $data = [];
        while($row = mysqli_fetch_assoc($result)) {
            $row = $this->addUrl($row);
            $data[] = $row;
        }
        jsonout($data);
    }

    private function delete() {
        if(! $this->allowedToEdit()) {
            jsonout(['PERMISSION' => 'NOPE']);
            return;
        }
        if(! array_key_exists('id', $_GET)) {
            jsonout(['MISSING' => 'ID']);
            return;
        }
        if(! is_numeric($_GET['id'])) {
            jsonout(['ID' => 'NOT NUMERIC']);
            return;
        }

        $deleteQuery = "DELETE FROM projects WHERE id = ".mysqli_real_escape_string($this->sqli, $_GET['id']);
        $result = mysqli_query($this->sqli, $deleteQuery);
        jsonout(['DEL' => 'YES']);
    }


    private function create() {
        if(! $this->allowedToEdit()) {
            jsonout(['PERMISSION' => 'NOPE']);
            return;
        }
        $values = ['title', 'author', 'description', 'text', 'text_secondary', 'literature', 'people', 'images', 'exhibitions'];
        $insertQuery = "INSERT INTO projects (`title`, `author`, `description`, `text`, `text_secondary`, `literature`, `people`, `images`, `exhibitions`)";
        $insertQuery.= " VALUES (";
        $vals = [];

        $postdata = file_get_contents("php://input");
        $data = json_decode($postdata, true);


        foreach($values as $v) {
            if(array_key_exists($v, $data)) {
                $vals[] = "'".str_replace("_", " " , mysqli_real_escape_string($this->sqli, $data[$v]))."'";
            } else {
                $vals[] = 'NULL';
            }
        }
        $insertQuery.= implode(", ", $vals ).") ";
        mysqli_query($this->sqli, $insertQuery);
        jsonout(['DONE']);
    }

    private function update() {
        if(! $this->allowedToEdit()) {
            jsonout(['PERMISSION' => 'NOPE']);
            return;
        }
        $vals = [];

        $postdata = file_get_contents("php://input");
        $data = json_decode($postdata, true);

        if(! array_key_exists('id', $data)) {
            jsonout(['NO' => 'ID']);
            return;
        }
        if(! is_numeric($data['id'])) {
            jsonout(['STRANGE' => 'ID']);
            return;
        }

        $values = ['title', 'author', 'description', 'text', 'literature', 'people', 'images', 'text_secondary', 'exhibitions'];
        $updateQuery = "UPDATE projects ";

        foreach($values as $v) {
            if(array_key_exists($v, $data)) {
                $vals[] = "`".$v."` = '".str_replace("_", " " , mysqli_real_escape_string($this->sqli, $data[$v]))."'";
            }
        }
        $updateQuery.= "SET ".implode(", ", $vals )." WHERE id=".$data['id'];
        mysqli_query($this->sqli, $updateQuery);
        jsonout(['DONE']);
    }

    private function allowedToEdit() {
        if (auth_level(USER_WORKER)) {
            return true;
        }
        return false;
    }
}

?>