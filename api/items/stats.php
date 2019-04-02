<?php

$selectQuery = "SELECT f.id, f.geburtsdatum, f.todesdatum, n.vorname, n.nachname, ao.name as ort, ao.kanton ".
               "FROM fotografen as f ".
               "inner join namen as n on f.id = n.fotografen_id ".
               "inner join arbeitsperioden as ap on f.id = ap.fotografen_id ".
               "inner join arbeitsorte as ao on ap.arbeitsort_id = ao.id and ao.kanton != '' ".
               "ORDER BY ao.kanton, ort, n.nachname;";

global $sqli;

$result = mysqli_query($sqli, $selectQuery);
$data = [];
while($row = mysqli_fetch_assoc($result)) {
  $data[] = $row;
}
jsonout($data);

?>
