<?php
function pushfields(&$o, $i, $list)
{
  foreach ($list as $l) {
    $o[$l] = $i[$l];
  }
}

function getClean($s)
{
  global $sqli;
  if (array_key_exists($s, $_REQUEST)) {
    return mysqli_real_escape_string($sqli, $_REQUEST[$s]);
  }
  return false;
}

function jsonout($out, $plain = false)
{
  // converting to utf8 to avoid warnings
  //$json = json_encode ( array_utf8($out) );
  $json = json_encode($out);
  if (array_key_exists('callback', $_GET)) {

    header('Content-Type: text/javascript; charset=utf-8');
    header('Access-Control-Allow-Origin: http://www.example.com/');
    header('Access-Control-Max-Age: 3628800');
    header('Access-Control-Allow-Methods: GET, POST, PUT, DELETE');
    header('Access-Control-Allow-Headers: AuthToken, Origin, X-Requested-With, Content-Type, Accept');

    $callback = $_GET ['callback'];
    echo $callback . '(' . $json . ');';
  } else {
    // normal JSON string
    if (!headers_sent()) {
      header('Access-Control-Allow-Origin: *');
      header('Content-Type: application/json; charset=utf-8');
      header('Access-Control-Allow-Headers: X-AuthToken, AuthToken, Origin, X-Requested-With, Content-Type, Accept');
    }

    echo $json;
    flush();
  }

}

function jsonfile($f)
{
  // normal JSON string
  header('Access-Control-Allow-Origin: *');
  header('Content-Type: application/json; charset=utf-8');
  header('Access-Control-Allow-Headers: X-AuthToken, AuthToken, Origin, X-Requested-With, Content-Type, Accept');

  if (file_exists($f)) {
    readfile($f);
  }
}


function array_utf8($array)
{
  if (!is_array($array)) {
    return $array;
  }
  foreach ($array as $n => $v) {
    if (is_array($v)) {
      $array[$n] = array_utf8($v);
    } else {
      if (mb_detect_encoding($v, 'UTF-8', true) === false) {
        $array[$n] = utf8_encode($v);
      }
    }
  }
  return $array;
}


function getToken($u, $l, $i)
{
  global $sqli;
  $sql = "SELECT * FROM auth WHERE user='$u'";
  $res = get1fromselect($sql);
  if (!$res) {
    $sql = "INSERT INTO `auth` SET `user`='$u', `token`=UUID(), `expires`=DATE_ADD(NOW(), INTERVAL 2 HOUR), `level`=$l, `inst_comment`=$i;";
    mysqli_query($sqli, $sql);
    $sql = "SELECT * FROM auth WHERE user='$u'";
    $res = get1fromselect($sql);
  }
  return $res['token'];
}

function testToken($t)
{
  $sql = "SELECT * FROM auth WHERE token='$t'";
  $res = get1fromselect($sql);
  if ($res) {

    return $res['level'];
  }
  return 0;
}

function getTokenInfo($t)
{
  $sql = "SELECT * FROM auth WHERE token='$t'";
  $res = get1fromselect($sql);
  if ($res) {

    return $res;
  }
  return 0;
}


function logOff($t)
{
  global $sqli;
  $sql = "SELECT * FROM auth WHERE token='$t'";
  $res = get1fromselect($sql);
  if ($res) {
    $sql = "DELETE FROM auth WHERE token='$t'";
    mysqli_query($sqli, $sql);
    return true;
  }
  return false;
}

function formlebensdaten($gdate, $gcode, $tdate, $tcode)
{  // formatiert lebenszeit in Liste
  $code = 10 * $gcode + $tcode;
  switch ($code) {
    case 0:
      return substr($gdate, 8, 2) . '.' . substr($gdate, 5, 2) . '.' . substr($gdate, 0, 4) . ' - ' . substr($tdate, 8, 2) . '.' . substr($tdate, 5, 2) . '.' . substr($tdate, 0, 4);
    case 1:
      return substr($gdate, 8, 2) . '.' . substr($gdate, 5, 2) . '.' . substr($gdate, 0, 4) . ' - ' . substr($tdate, 0, 4);
    case 2:
      return substr($gdate, 8, 2) . '.' . substr($gdate, 5, 2) . '.' . substr($gdate, 0, 4) . ' - ';
    case 8:
      return substr($gdate, 8, 2) . '.' . substr($gdate, 5, 2) . '.' . substr($gdate, 0, 4);
    case 9:
      return '*' . substr($gdate, 8, 2) . '.' . substr($gdate, 5, 2) . '.' . substr($gdate, 0, 4);
    case 10:
      return substr($gdate, 0, 4) . ' - ' . substr($tdate, 8, 2) . '.' . substr($tdate, 5, 2) . '.' . substr($tdate, 0, 4);
    case 11:
      return substr($gdate, 0, 4) . ' - ' . substr($tdate, 0, 4);
    case 12:
      return substr($gdate, 0, 4) . ' - ';
    case 18:
      return substr($gdate, 0, 4);
    case 19:
      return '*' . substr($gdate, 0, 4);
    case 20:
      return ' - ' . substr($tdate, 8, 2) . '.' . substr($tdate, 5, 2) . '.' . substr($tdate, 0, 4);
    case 21:
      return ' - ' . substr($tdate, 0, 4);
    case 22:
      return '†';
    case 28:
      return "";
    case 29:
      return "*";
  }
  return date("d.m.Y", strtotime($date));
}

function formumfeldn($t)
{  // expandiert Links im Umfeld
  global $sqli;
  $suchmuster = "/<.link:(.\d+)>/";
  $tref = preg_match_all($suchmuster, $t, $treffer);
  //print_r($treffer);
  //$suchmuster = "llink:";

  for ($i = 0; $i < count($treffer[0]); $i++) {
    $id = $treffer[1][$i];
    //$nid=$treffer[0][$i];

    if (substr($id, 0, 1) == 'n') {
      $id = substr($id, 1);
      $n = 1;

      $result = mysqli_query($sqli, "SELECT *  FROM (fotografen INNER JOIN namen ON fotografen.id=namen.fotografen_id) WHERE namen.id=$id ORDER BY namen.id Asc");
      //echo("SELECT *  FROM (fotografen INNER JOIN namen ON fotografen.id=namen.fotografen_id) WHERE namen.id=$id ORDER BY namen.id Asc");

    } else {
      $result = mysqli_query($sqli, "SELECT *  FROM (fotografen INNER JOIN namen ON fotografen.id=namen.fotografen_id) WHERE fotografen_id=$id ORDER BY namen.id Asc");
    }
    $fetch = mysqli_fetch_array($result);
    $name = $fetch['vorname'] . ' ' . $fetch['namenszusatz'] . ' ' . $fetch['nachname'];
    // echo $name;
    if ($n == 1) $id = $fetch['fotografen_id'];
    if ($fetch['unpubliziert'] == 1) {
      if (auth_level(USER_GUEST_READER)) {
        $t = str_replace($treffer[0][$i], '<span class="text2g"><a href="#/fotographer/detail?id=' . $id . '">' . $name . '</a></span>', $t);
      } else {
        $t = str_replace($treffer[0][$i], $name, $t);
      }
    } else {
      /* !!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!! fotograph  */
      $t = str_replace($treffer[0][$i], '<a data-target-id="' . trim($id) . '" data-target-type="photographer">' . $name . '</a>', $t);
      //$t=str_replace($treffer[0][$i],'<a ui-sref="fotographerDetail( {id: \''.$id.'\'} ">'.$name.'</a>',$t);
      //$t=str_replace($treffer[0][$i],'<section-fotographer-link name="'.$name.'" id="\''.$id.'\'"></section-fotographer-link>',$t);
    }
  }
  return (str_replace('<br>', '<br />', $t));
}

function formdatesimp2($date, $code)
{
  // formatiert Datum
  // Code
  // 0: TT.MM.YYYY
  // 1: YYYY
  // sonst: leer

  switch ($code) {
    case 0:
      return (substr($date, 8, 2) + 0) . '.' . (substr($date, 5, 2) + 0) . '.' . substr($date, 0, 4);
    case 1:
      return substr($date, 0, 4);
    case 2:
      return "";
    case 8:
      return "";
    case 9:
      return "";
  }
  return date("d.m.Y", strtotime($date));
}

function get1fromtable($t, $w)
{
  global $sqli;
  $sql = "SELECT * FROM $t WHERE $w LIMIT 1";
  $result = mysqli_query($sqli, $sql);
  if (mysqli_num_rows($result) != 0) {
    $res = mysqli_fetch_assoc($result);

    return ($res);
  } else {
    return false;
  }
}

function getfromtable($t, $w)
{
  global $sqli;
  $res = array();
  $sql = "SELECT * FROM $t WHERE $w";
  $result = mysqli_query($sqli, $sql);
  //echo $sql;
  if (mysqli_num_rows($result) != 0) {
    while ($r = mysqli_fetch_assoc($result)) {
      $res[] = $r;
    }

    return ($res);
  } else {
    return false;
  }
}

function getfromtableUser($t, $w)
{  // liefert alle Stundeneinträge des Monats minestens aber die neuesten 20
  global $sqli;
  $res = array();
  $sql = "SELECT * FROM $t WHERE $w";
  $result = mysqli_query($sqli, $sql);
  //echo $sql;
  if (mysqli_num_rows($result) != 0) {
    $count = 0;
    $fertig = 0;
    $h = getErster();

    while (($r = mysqli_fetch_assoc($result)) && ($fertig == 0)) {
      //print_r($r);
      //echo $r['datum'];
      $count++;
      if ($count <= 20 || $r['datum'] >= $h) {
        $res[] = $r;
      } else $fertig = 1;
    }

    return ($res);
  } else {
    return false;
  }
}

function getfromselect($s)
{
  global $sqli;
  $res = array();
  $sql = $s;
  $result = mysqli_query($sqli, $sql);
  //echo $sql;
  if (mysqli_num_rows($result) != 0) {
    while ($r = mysqli_fetch_assoc($result)) {
      $res[] = $r;
    }

    return ($res);
  } else {
    return false;
  }
}

function get1fromselect($s)
{
  global $sqli;
  $sql = $s;
  $result = mysqli_query($sqli, $sql);
  //echo $sql;
  if (mysqli_num_rows($result) != 0) {
    $res = mysqli_fetch_assoc($result);
    return ($res);
  } else {
    return false;
  }
}

function getfromtablebyid($t, $w)
{
  global $sqli;
  $res = array();
  $sql = "SELECT * FROM $t WHERE $w";
  $result = mysqli_query($sqli, $sql);
  if (mysqli_num_rows($result) != 0) {
    while ($r = mysqli_fetch_assoc($result)) {
      $res[$r['id']] = $r;
    }

    return ($res);
  } else {
    return false;
  }
}


function clangues(&$fetch, $key)
{
  global $language;
  global $clanguage;
  $l = $language;
  $langs = array('de', 'en', 'fr', 'it', 'rm');
  foreach ($langs as $l) {
    $e = $fetch[$key . clangexl($l)];
    if ($key == 'umfeld') {
      $e = formumfeldn($e);
    }
    if ($e) {
      $out[$l] = $e;
    }
  }
  return ($out);
}

?>