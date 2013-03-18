<?php
require("mysql.inc.php");

// get the language strings
$language = $_COOKIE['lang'];
require("lang.inc.php");

$action=$_GET['action'];

switch ($action) {
    case getStock :
        // return associated stock information for the selected institution
        $id = $_GET['id'];
        var_dump($id);
        if ($id!=0) {
            $query = "SELECT id, name FROM bestand WHERE inst_id=$id";
        }
        else {
            $query = "SELECT id, name FROM bestand";
            $result = "<option value='0' selected='selected'>".$spr['all']."</option>";
        }
        $objResult=mysql_query($query);
        while($row = mysql_fetch_assoc($objResult)){
            $result .= "<option value='".$row['id']."'>".$row['name']."</option>";
        }

        echo $result;
        break;
}
