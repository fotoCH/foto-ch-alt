<?php

if(array_key_exists('wanted', $_GET)) {
    $yp = new YearProvider();
    $yp->setYears($_GET['wanted']);
    if(array_key_exists('nolimit', $_GET)) {
        $yp->nolimit();
    }
    $yp->getEvents();
    $yp->output();
}


class YearProvider {
    private $years = array();
    private $events = array();
    private $limit = " LIMIT 4";

    public function getEvents() {
        foreach($this->years as $year) {
            $this->getEvent($year);
        }
    }

    private function getEvent($year) {
        $this->events[$year] = array();
        $this->photos($year);
        $this->literature($year);
        $this->exhibition($year);
        $this->startedWork($year);
        $this->stoppedWork($year);
        $this->born($year);
        $this->died($year);
    }

    private function literature($year) {
        $query = "SELECT * FROM literatur";
        $query.= " WHERE jahr = '".$year."'";
        $query.= $this->limit;
        $rs = mysql_query($query);
        $this->events[$year]['literature'] = array();

        while($row = mysql_fetch_assoc($rs)) {
            array_push($this->events[$year]['literature'], array(
                "id" => $row['id'],
                "author" => $row['verfasser_vorname'].' '.$row['verfasser_name'],
                "title" => $row['titel']
            ));
        }
    }

    private function exhibition($year) {
        $query = "SELECT * FROM ausstellung";
        $query.= " WHERE jahr = '".$year."'";
        $query.= $this->limit;
        $rs = mysql_query($query);
        $this->events[$year]['exhibitions'] = array();

        while($row = mysql_fetch_assoc($rs)) {
            array_push($this->events[$year]['exhibitions'], array(
                "id" => $row['id'],
                "place" => $row['ort'],
                "title" => $row['titel'],
                "institution" => $row['institution']
            ));
        }
    }

    private function photos($year) {
        $query = "SELECT * FROM fotos";
        $query.= " WHERE dc_created LIKE '".$year."%'";
        $query.= $this->limit;
        $rs = mysql_query($query);
        $this->events[$year]['photos'] = array();

        while($row = mysql_fetch_assoc($rs)) {
            array_push($this->events[$year]['photos'], array(
                "id" => $row['id'],
                "path" => $row['image_path'],
                "title" => $row['dc_title']
            ));
        }
    }

    private function born($year) {
        $query = "SELECT * FROM fotografen";
        $query.= " INNER JOIN namen ON fotografen.id = namen.fotografen_id";
        $query.= " WHERE geburtsdatum LIKE '".$year."%' AND unpubliziert = 0";
        $query.= $this->limit;
        $rs = mysql_query($query);
        $this->events[$year]['born'] = array();

        while($row = mysql_fetch_assoc($rs)) {
            array_push($this->events[$year]['born'], array(
                "photograph_id" => $row['fotografen_id'],
                "photographer_name" => $row['vorname']." ".$row['nachname'],
                "birthplace" => $row['geburtsort']
            ));
        }
    }

    private function died($year) {
        $query = "SELECT * FROM fotografen";
        $query.= " INNER JOIN namen ON fotografen.id = namen.fotografen_id";
        $query.= " WHERE todesdatum LIKE '".$year."%' AND unpubliziert = 0";
        $query.= $this->limit;
        $rs = mysql_query($query);
        $this->events[$year]['deaths'] = array();

        while($row = mysql_fetch_assoc($rs)) {
            array_push($this->events[$year]['deaths'], array(
                "photograph_id" => $row['fotografen_id'],
                "photographer_name" => $row['vorname']." ".$row['nachname'],
                "deathplace" => $row['todesort']
            ));
        }
    }

    private function stoppedWork($year) {
        $query = "SELECT * FROM arbeitsperioden";
        $query.= " INNER JOIN namen on arbeitsperioden.fotografen_id = namen.fotografen_id";
        $query.= " INNER JOIN fotografen on arbeitsperioden.fotografen_id = fotografen.id";
        $query.= " WHERE bis = ".$year." AND arbeitsort <> '' AND um_von = 0 AND fotografen.unpubliziert = 0";
        $query.= $this->limit;
        $rs = mysql_query($query);
        $this->events[$year]['workstop'] = array();

        while($row = mysql_fetch_assoc($rs)) {
            array_push($this->events[$year]['workstop'], array(
                "photograph_id" => $row['fotografen_id'],
                "photographer_name" => $row['vorname']." ".$row['nachname'],
                "workplace" => $row['arbeitsort'],
            ));
        }
    }

    public function startedWork($year) {
        $query = "SELECT * FROM arbeitsperioden";
        $query.= " INNER JOIN namen on arbeitsperioden.fotografen_id = namen.fotografen_id";
        $query.= " INNER JOIN fotografen on arbeitsperioden.fotografen_id = fotografen.id";
        $query.= " WHERE von = ".$year." AND arbeitsort <> '' AND um_von = 0 AND fotografen.unpubliziert = 0";
        $query.= $this->limit;
        $rs = mysql_query($query);
        $this->events[$year]['workbegin'] = array();

        while($row = mysql_fetch_assoc($rs)) {
            array_push($this->events[$year]['workbegin'], array(
                "photograph_id" => $row['fotografen_id'],
                "photographer_name" => $row['vorname']." ".$row['nachname'],
                "workplace" => $row['arbeitsort'],
            ));
        }
    }

    public function output() {
        jsonout($this->events);
    }

    public function nolimit() {
        $this->limit = "";
    }

    public function setYears($years) {
        $this->years = explode(",", $years);
    }

}


?>