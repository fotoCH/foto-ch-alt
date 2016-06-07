<?php

$search = new StreamedSearch();
$search->setLang($glob['LANG']);
if(array_key_exists('limit', $_GET) && is_numeric($_GET['limit'])) {
    $search->setLimit($_GET['limit']);
}

if($search->active()) {
    $search->query();
} else {
    $search->response();
}


class StreamedSearch {
    private $lang = '';
    private $limitResults = 100;
    private $query = false;
    private $result = array();

    private $levels = array(
        'photographer' => 4,
        'stock' => 1,
        'institution' => 2,
        'exhibition' => 1,
        'literature' => 2
    );

    private function photographerFields() {
        return array(
            "fotografen.id", 
            "fotografen.bearbeitungsdatum",
            "fotografen.geburtsdatum", 
            "fotografen.gen_geburtsdatum",
            "fotografen.todesdatum",
            "fotografen.gen_todesdatum", 
            "fotografen.autorIn<>'' AS biog",
            "fotografen.showkurzbio",
            "fotografen.unpubliziert",
            "namen.nachname", 
            "namen.vorname", 
            "namen.namenszusatz", 
            "namen.titel", 
            "fotografen.pnd", 
            "fotografen.fotografengattungen_set",
            "fotografen.bildgattungen_set",
            "fotografen.kanton"
        );
    }

    private function stockFields() {
        return array(
            "bestand.id",
            "bestand.name",
            "bestand.zeitraum",
            "bestand.bildgattungen", 
            "bestand.link_extern",
            "bestand.signatur"
        );
    }

    private function institutionFields() {
        return array(
            "institution.id",
            "institution.name".$this->lang." AS name",
            "institution.abkuerzung".$this->lang." AS shortname",
            "institution.art",
            "institution.adresse",
            "institution.plz",
            "institution.ort",
            "institution.kanton",
            "institution.zugang_zur_sammlung".$this->lang." AS access",
            "institution.sammlungszeit_von",
            "institution.sammlungszeit_bis"
        );
    }

    private function exhibitionFields() {
        return array(
            "ausstellung.id",
            "ausstellung.titel",
            "ausstellung.jahr",
            "ausstellung.ort"
        );
    }

    private function literatureFields() {
        return array(
            "literatur.id",
            "literatur.titel",
            "literatur.verfasser_name", 
            "literatur.verfasser_vorname", 
            "literatur.jahr"
        );
    }


    public function query() {
        $this->getLevelResults(0);
        $this->getLevelResults(1);
        $this->getLevelResults(2);
        $this->getLevelResults(3);
    }

    private function getLevelResults($level) {

        if(! $this->enoughOfType('photographer', $level) ) {
            $this->photographer($level);
            $this->response();
        }

        if(! $this->enoughOfType('stock', $level) ) {
            $this->stocks($level);
            $this->response();
        }

        if(! $this->enoughOfType('institution', $level)) {
            $this->institution($level);
            $this->response();
        }

        if(! $this->enoughOfType('exhibition', $level)) {
            $this->exhibition($level);
            $this->response();
        }

        if(! $this->enoughOfType('literature', $level)) {
            $this->literature($level);
            $this->response();
        }
    }

    private function literature($level = 0) {
        $sql = "SELECT DISTINCT ".implode(", ", $this->literatureFields())." FROM literatur";
        if($level >= 1) {
            $sql.= " LEFT JOIN literatur_fotograf on literatur_fotograf.literatur_id = literatur.id";
            $sql.= " INNER JOIN fotografen on literatur_fotograf.fotografen_id = fotografen.id";
            $sql.= " LEFT JOIN literatur_institution on literatur_institution.literatur_id = literatur.id";
            $sql.= " INNER JOIN institution on literatur_institution.institution_id = institution.id";
            $sql.= " INNER JOIN namen on fotografen.id = namen.fotografen_id";
        }

        $q = explode(" ", $this->query);
        $first = true;
        foreach($q as $term) {
            $term = mysql_real_escape_string($term);
            if($first) {
                $first = false;
                $sql.= " WHERE ";
            } else {
                $sql.= " AND ";
            }
            $sql.= '(';
            if($level >= 0) {
                $sql.= "literatur.titel LIKE '%".$term."%'";
                $sql.= " OR literatur.verfasser_name LIKE '%".$term."%'";
                $sql.= " OR literatur.verfasser_vorname LIKE '%".$term."%'";
                $sql.= " OR literatur.jahr LIKE '%".$term."%'";
            }
            if($level >= 1) {
                $sql.= " OR namen.vorname LIKE '%".$term."%'";
                $sql.= " OR namen.nachname LIKE '%".$term."%'";
                $sql.= " OR institution.name".$this->lang." LIKE '%".$term."%'";
                $sql.= " OR institution.abkuerzung".$this->lang." LIKE '%".$term."%'";
            }
            $sql.= ')';
        }
        if(!$first) {
            if($level >= 1) {
                $sql.= " AND fotografen.unpubliziert = 0";
            }
            $sql.= " LIMIT ".$this->limitResults;

            mysql_query('set names utf8');

            $result = mysql_query($sql);
            $this->results['literature_results'] = array();
            while($assoc = mysql_fetch_assoc($result)) {
                array_push($this->results['literature_results'], $assoc);
            }
            $this->results['literature_count'] = count($this->results['literature_results']);
        }



    }

    private function exhibition($level = 0) {
        $sql = "SELECT DISTINCT ".implode(", ", $this->exhibitionFields())." FROM ausstellung";
        $sql.= " LEFT JOIN ausstellung_fotograf ON ausstellung.id = ausstellung_fotograf.ausstellung_id";
        $sql.= " INNER JOIN fotografen ON ausstellung_fotograf.fotograf_id = fotografen.id";
        $sql.= " INNER JOIN namen on fotografen.id = namen.fotografen_id";

        $q = explode(" ", $this->query);
        $first = true;
        foreach($q as $term) {
            $term = mysql_real_escape_string($term);
            if($first) {
                $first = false;
                $sql.= " WHERE ";
            } else {
                $sql.= " AND ";
            }
            $sql.= '(';
            if($level >= 0) {
                $sql.= "ausstellung.institution LIKE '%".$term."%'";
                $sql.= " OR ausstellung.ort LIKE '%".$term."%'";
                $sql.= " OR namen.vorname LIKE '%".$term."%'";
                $sql.= " OR namen.nachname LIKE '%".$term."%'";
            }
            $sql.= ')';
        }
        if(!$first) {
            $sql.= " AND fotografen.unpubliziert = 0";
            $sql.= " LIMIT ".$this->limitResults;

            mysql_query('set names utf8');

            $result = mysql_query($sql);
            $this->results['exhibition_results'] = array();
            while($assoc = mysql_fetch_assoc($result)) {
                array_push($this->results['exhibition_results'], $assoc);
            }
            $this->results['exhibition_count'] = count($this->results['exhibition_results']);
        }

    }

    private function institution($level = 0) {
        $sql = "SELECT DISTINCT ".implode(", ", $this->institutionFields())." FROM institution";
        if($level >= 1) {
            $sql.= " LEFT JOIN bestand on institution.id = bestand.inst_id";
            $sql.= " LEFT JOIN bestand_fotograf on bestand.id = bestand_fotograf.bestand_id";
            $sql.= " INNER JOIN fotografen on bestand_fotograf.fotografen_id = fotografen.id";
            $sql.= " INNER JOIN namen on fotografen.id = namen.fotografen_id";
        }

        $q = explode(" ", $this->query);
        $first = true;
        foreach($q as $term) {
            $term = mysql_real_escape_string($term);
            if($first) {
                $first = false;
                $sql.= " WHERE ";
            } else {
                $sql.= " AND ";
            }
            $sql.= '(';
            if($level >= 0) {
                $sql.= "institution.name".$this->lang." LIKE '%".$term."%'";
                $sql.= " OR institution.abkuerzung".$this->lang." LIKE '%".$term."%'";
                $sql.= " OR institution.ort LIKE '%".$term."%'";
                $sql.= " OR institution.plz LIKE '%".$term."%'";
            }
            if($level >= 1) {
                $sql.= " OR namen.nachname LIKE '%".$term."%'";
                $sql.= " OR namen.vorname LIKE '%".$term."%'";
            }
            $sql.= ')';
        }
        if(!$first) {
            $sql.= " AND institution.gesperrt = 0";
            if($level >= 1) {
                $sql.= " ORDER BY bestand.nachlass DESC";
            }
            $sql.= " LIMIT ".$this->limitResults;
            mysql_query('set names utf8');

            $result = mysql_query($sql);
            $this->results['institution_results'] = array();
            while($assoc = mysql_fetch_assoc($result)) {
                array_push($this->results['institution_results'], $assoc);
            }
            $this->results['institution_count'] = count($this->results['institution_results']);
        }
    }

    private function stocks($level = 0) {
        $sql = "SELECT DISTINCT ".implode(", ", $this->stockFields())." FROM bestand";
        $q = explode(" ", $this->query);
        $first = true;
        foreach($q as $term) {
            $term = mysql_real_escape_string($term);
            if($first) {
                $first = false;
                $sql.= " WHERE ";
            } else {
                $sql.= " AND ";
            }
            $sql.= '(';
            if($level >= 0) {
                $sql.= "bestand.name LIKE '%".$term."%'";
                $sql.= " OR bestand.zeitraum LIKE '%".$term."%'";
            }
            $sql.= ')';
        }
        if(!$first) {
            $sql.= " AND bestand.gesperrt = 0";
            $sql.= " LIMIT ".$this->limitResults;
            mysql_query('set names utf8');

            $result = mysql_query($sql);
            $this->results['stock_results'] = array();
            while($assoc = mysql_fetch_assoc($result)) {
                array_push($this->results['stock_results'], $assoc);
            }
            $this->results['stock_count'] = count($this->results['stock_results']);
        }

    }

    private function photographer($level = 0) {
        $sql = "SELECT DISTINCT ".implode(", ", $this->photographerFields())." FROM namen";
        $sql.= " INNER JOIN fotografen on namen.fotografen_id = fotografen.id";
        if($level >= 1) {
            $sql.= " INNER JOIN arbeitsperioden on arbeitsperioden.fotografen_id = fotografen.id";
        }
        $q = explode(" ", $this->query);
        $first = true;
        foreach($q as $term) {
            $term = mysql_real_escape_string($term);
            if($first) {
                $first = false;
                $sql.= " WHERE ";
            } else {
                $sql.= " AND ";
            }
            if($level >= 0) {
                $sql.= "(namen.nachname LIKE '%".$term."%'";
                $sql.= " OR namen.vorname LIKE '%".$term."%'";
            }
            if($level >= 1) {
                $sql.= " OR arbeitsperioden.arbeitsort LIKE '%".$term."%'";
            }
            if($level >= 2){
                $sql.= " OR fotografen.werdegang".$this->lang." LIKE '%".$term."%'";
            }
            if($level >= 3){
                $sql.= " OR fotografen.schaffensbeschrieb".$this->lang." LIKE '%".$term."%'";
            }
            $sql.= ")";
        }
        // only run query if we got a search term
        if(!$first) {
            $sql.= " AND fotografen.unpubliziert = 0";

            $sql.= " ORDER BY (CASE WHEN namen.nachname LIKE '%".$q[0]."%' THEN 100 ELSE 0 END) DESC";
            $sql.= " LIMIT ".$this->limitResults;

            // TODO: Add prioritazion with "order by (case when x = 'hello' then 1 else 2 end)"

            // make it utf8 for the query... - http://www.winfuture-forum.de/index.php?showtopic=193063
            mysql_query('set names utf8');

            $result = mysql_query($sql);
            $this->results['photographer_results'] = array();
            while($assoc = mysql_fetch_assoc($result)) {
                array_push($this->results['photographer_results'], $assoc);
            }
            $this->results['photographer_count'] = count($this->results['photographer_results']);
        }
    }

    private function enoughOfType($type, $level) {
        // level is higher then accepted for this type...
        if( $level >= $this->levels[$type]) {
            return true;
        }
        // not one result is set yet...
        if(! is_array($this->results) || ! array_key_exists($type.'_results', $this->results)) {
            return false;
        }
        // not enough results yet for the limit...
        if(count($this->results[$type.'_results']) <= $this->limitResults) {
            return false;
        }
        return true;
    }


    public function active() {
        if(array_key_exists('query', $_GET) && strlen($_GET['query']) >= 2 ) {
            $this->query = $_GET['query'];
            return true;
        }
        return false;
    }

    public function setLang($lang) {
        if($lang != 'de') {
            $this->lang = '_'.$lang;
        }
    }

    public function response() {
        jsonout($this->results, true);
    }

    public function setLimit($limit) {
        $this->limitResults = $limit;
    }

}

?>