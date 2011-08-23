<?php
$def=new XTemplate ("./templates/ablauf.xtpl");
$def->parse("content");
$out.=$def->text("content");
?>