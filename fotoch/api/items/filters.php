<?php

$filters = new Filters();
if(array_key_exists('type', $_GET)) {
    $filters->output($_GET['type']);
} else {
    $filters->output('No type set.');
}


class Filters {
    private $filters = array();
    private $type = false;

    private function unknown() {
        $this->filters['type'] = 'Unknown Type: ' .$this->type;
    }

    private function fotografengattungen() {
        $this->distinctFieldValues("fotografen.fotografengattungen_set", "fotografen");
    }

    private function bildgattungen() {
        $this->distinctFieldValues("fotografen.bildgattungen_set", "fotografen");
    }

    private function fotografenKanton() {
        $this->distinctFieldValues("fotografen.kanton", "fotografen");
    }

    private function institutionKanton() {
        $this->distinctFieldValues("institution.kanton", "institution", " WHERE institution.gesperrt = 0");
    }

    private function institutionOrt() {
        $this->distinctFieldValues("institution.ort", "institution", " WHERE institution.gesperrt = 0");
    }

    private function ausstellungJahr() {
        $this->distinctFieldValues("ausstellung.jahr", "ausstellung", "", true);
    }

    private function ausstellungTyp() {
        $this->distinctFieldValues("ausstellung.typ", "ausstellung");
    }

    private function ausstellungOrt() {
        $this->distinctFieldValues("ausstellung.ort", "ausstellung");
    }

    private function literaturJahr() {
        $this->distinctFieldValues("literatur.jahr", "literatur");
    }

    private function literaturOrt() {
        $this->distinctFieldValues("literatur.ort", "literatur");
    }

    private function photoStickworte() {
        $this->distinctFieldValues("dcterms_subject", "fotos");
    }

    private function photoStocks() {
        $this->filters['possible_values'] = array();
        $sql = "SELECT DISTINCT dcterms_ispart_of as 'value' FROM fotos";
        $set = mysql_query($sql);
        while($row = mysql_fetch_assoc($set)) {
            $possible = $row['value'];
            if(!array_key_exists($possible, $this->filters['possible_values'])) {
                $stockQuery = "SELECT name FROM bestand WHERE id=".$possible;
                $result = mysql_query($stockQuery);
                while($stock = mysql_fetch_assoc($result)) {
                    array_push($this->filters['possible_values'], array(
                        "id" => $possible, 
                        "value" => $stock['name']
                    ));
                }
            }
        }
        if($desc) {
            rsort($this->filters['possible_values']);
        } else {
            sort($this->filters['possible_values']);
        }
    }

    private function distinctFieldValues($field, $table, $where='', $desc = false) {
        $this->filters['possible_values'] = array();
        $sql = "SELECT DISTINCT ".$field." as 'value' FROM ".$table.$where;
        $set = mysql_query($sql);
        while($row = mysql_fetch_assoc($set)) {
            $possibles = explode(",", $row['value']);
            foreach($possibles as $poss) {
                $poss = trim($poss);
                if($poss == '') {
                    continue;
                }
                if(!in_array($poss, $this->filters['possible_values'])) {
                    array_push($this->filters['possible_values'], $poss);
                }
            }
        }
        if($desc) {
            rsort($this->filters['possible_values']);
        } else {
            sort($this->filters['possible_values']);
        }
    }

    public function output($type) {
        $this->type = $type;
        switch($this->type) {
            case 'fotografengattungen':
                $this->fotografengattungen();
                break;
            case 'bildgattungen':
                $this->bildgattungen();
                break;
            case 'fotografen_kanton':
                $this->fotografenKanton();
                break;
            case 'institution_kanton':
                $this->institutionKanton();
                break;
            case 'institution_ort':
                $this->institutionOrt();
                break;
            case 'ausstellung_jahr':
                $this->ausstellungJahr();
                break;
            case 'ausstellung_typ':
                $this->ausstellungTyp();
                break;
            case 'ausstellung_ort':
                $this->ausstellungOrt();
                break;
            case 'literatur_jahr':
                $this->literaturJahr();
                break;
            case 'literatur_ort':
                $this->literaturOrt();
                break;
            case 'photo_stichworte':
                $this->photoStickworte();
                break;
            case 'photo_stocks':
                $this->photoStocks();
                break;
            default :
                $this->unknown();
                break;
        }
        jsonout($this->filters);
    }

}

?>