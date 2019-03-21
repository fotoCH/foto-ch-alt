<?php

$query = "SELECT text_".$language." as text , bild_".$language." as bild, link_".$language." as link FROM logos ORDER by id";
$result = mysqli_query($sqli, $query);
while($fetch = mysqli_fetch_assoc($result)){
            $pages[]=$fetch;
}

jsonout($pages);
?>
