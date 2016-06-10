<?php

$search = new StreamedSearch();
$search->setLang($glob['LANG']);
if(array_key_exists('limit', $_GET) && is_numeric($_GET['limit'])) {
    $search->setLimit($_GET['limit']);
}
if(array_key_exists('photolimit', $_GET) && is_numeric($_GET['photolimit'])) {
    $search->setPhotoLimit($_GET['photolimit']);
}
if(array_key_exists('type', $_GET)) {
    $search->setType($_GET['type']);
}
if(array_key_exists('sort', $_GET)); {
    $search->setSorting($_GET['sort']);
}
if(array_key_exists('sortdir', $_GET)) {
    $search->setSortDirection($_GET['sortdir']);
}


if($search->activate()) {
    $search->query();
} else {
    $search->response();
}


class StreamedSearch {
    private $lang = '';
    private $limitResults = 100;
    private $limitPhotoResults = false;
    private $query = "";
    private $type = false;
    private $sorting = false;
    private $sortDirection = 'ASC';
    private $result = array();

    private $levels = array(
        'photographer' => 2,
        'stock' => 2,
        'institution' => 2,
        'exhibition' => 1,
        'literature' => 2,
        'photos' => 2
    );

    private function photoFields() {
        return array(
            "fotos.id",
            "fotos.dc_title as title",
            "fotos.dc_description as description",
            "fotos.dc_identifier as identifier",
            "fotos.dc_right as copyright",
            "fotos.image_path",
            "fotos.zeitraum",
            "namen.vorname AS autor_forename",
            "namen.nachname AS autor_lastname"
        );
    }

    private function photographerFields() {
        return array(
            "fotografen.id as id", 
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
            "fotografen.kanton",
            "(SELECT GROUP_CONCAT(arbeitsort) FROM arbeitsperioden WHERE fotografen.id = arbeitsperioden.fotografen_id) as arbeitsorte"
        );
    }

    private function stockFields() {
        return array(
            "bestand.id",
            "bestand.name",
            "bestand.zeitraum",
            "bestand.bildgattungen", 
            "bestand.link_extern",
            "bestand.signatur",
            "institution.name AS institution",
            "institution.ort AS ort",
            "institution.kanton AS kanton"
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
            "ausstellung.ort",
            "ausstellung.institution",
            "ausstellung.typ"
        );
    }

    private function literatureFields() {
        return array(
            "literatur.id",
            "literatur.titel",
            "literatur.verfasser_name", 
            "literatur.verfasser_vorname", 
            "literatur.jahr",
            "literatur.ort"
        );
    }


    public function query() {
        $levels = 3;
        for($level = 0; $level <= $levels; $level++) {
            $this->getLevelResults($level);
        }
    }

    private function getLevelResults($level) {
        $types = array('photographer', 'stock', 'institution', 'exhibition', 'literature', 'photos');

        foreach($types as $type) {
            if(! $this->enoughOfType($type, $level) ) {
                if(!$this->type || $this->type == $type) {
                    $this->$type($level);
                    $this->response();
                }
            }
        }
    }

    private function photos($level) {
        $sql='';
        if($this->sorting) {
            $sql.= "SELECT * FROM (";
        }
        $sql.= "SELECT DISTINCT ".implode(", ", $this->photoFields())." FROM fotos";
        $sql.=" INNER JOIN fotografen on fotografen.id = fotos.dc_creator";
        $sql.=" RIGHT JOIN namen on fotografen.id = namen.fotografen_id";

        if($level >= 1) {
            $sql.=" INNER JOIN bestand on fotos.dcterms_ispart_of = bestand.id";
            $sql.=" INNER JOIN institution on bestand.inst_id = institution.id";
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
                $sql.= "fotos.dc_title LIKE '%".$term."%'";
                $sql.= " OR fotos.dc_description LIKE '%".$term."%'";
                $sql.= " OR fotos.dc_created LIKE '%".$term."%'";
                $sql.= " OR fotos.dcterms_spatial LIKE '%".$term."%'";
            }
            if($level >= 1) {
                $sql.= " OR namen.vorname LIKE '%".$term."%'";
                $sql.= " OR namen.nachname LIKE '%".$term."%'";
                $sql.= " OR bestand.name".$this->lang." LIKE '%".$term."%'";
                $sql.= " OR institution.name".$this->lang." LIKE '%".$term."%'";
            }
            if($level >= 2) {
                $sql.= " OR fotos.dcterms_medium LIKE '%".$term."%'";
                $sql.= " OR fotos.dcterms_subject LIKE '%".$term."%'";
            }
            $sql.= ')';
        }
        if($level >= 1) {
            $sql.= " AND fotografen.unpubliziert = 0";
        }
        if($this->limitPhotoResults) {
            $sql.= " LIMIT ".$this->limitPhotoResults;
        } else {
            $sql.= " LIMIT ".$this->limitResults;
        }

        if($this->sorting) {
            $sql.= ") AS T1 ORDER BY ".$this->sorting.' '.$this->sortDirection;
        }

        $result = mysql_query($sql);
        $this->results['photos_results'] = array();
        while($assoc = mysql_fetch_assoc($result)) {
            array_push($this->results['photos_results'], $assoc);
        }
        $this->results['photos_count'] = count($this->results['photos_results']);

    }

    private function literature($level = 0) {
        $sql='';
        if($this->sorting) {
            $sql.= "SELECT * FROM (";
        }
        $sql.="SELECT DISTINCT ".implode(", ", $this->literatureFields())." FROM literatur";
        if($level >= 1) {
            $sql.= " LEFT JOIN literatur_fotograf on literatur_fotograf.literatur_id = literatur.id";
            $sql.= " INNER JOIN fotografen on literatur_fotograf.fotografen_id = fotografen.id";
            $sql.= " LEFT JOIN literatur_institution on literatur_institution.literatur_id = literatur.id";
            $sql.= " LEFT JOIN institution on literatur_institution.institution_id = institution.id";
            $sql.= " RIGHT JOIN namen on fotografen.id = namen.fotografen_id";
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
        if($level >= 1) {
            $sql.= " AND fotografen.unpubliziert = 0";
        }
        $sql.= " LIMIT ".$this->limitResults;

        if($this->sorting) {
            $sql.= ") AS T1 ORDER BY ".$this->sorting.' '.$this->sortDirection;
        }


        $result = mysql_query($sql);
        $this->results['literature_results'] = array();
        while($assoc = mysql_fetch_assoc($result)) {
            array_push($this->results['literature_results'], $assoc);
        }
        $this->results['literature_count'] = count($this->results['literature_results']);
    }

    private function exhibition($level = 0) {
        $sql='';
        if($this->sorting) {
            $sql.= "SELECT * FROM (";
        }
        $sql.="SELECT DISTINCT ".implode(", ", $this->exhibitionFields())." FROM ausstellung";
        $sql.= " LEFT JOIN ausstellung_fotograf ON ausstellung.id = ausstellung_fotograf.ausstellung_id";
        //$sql.= " INNER JOIN fotografen ON ausstellung_fotograf.fotograf_id = fotografen.id";
        //$sql.= " INNER JOIN namen on fotografen.id = namen.fotografen_id";

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
                //$sql.= " OR namen.vorname LIKE '%".$term."%'";
                //$sql.= " OR namen.nachname LIKE '%".$term."%'";
            }
            $sql.= ')';
        }
        $sql.= " LIMIT ".$this->limitResults;

        if($this->sorting) {
            $sql.= ") AS T1 ORDER BY ".$this->sorting.' '.$this->sortDirection;
        }

        $result = mysql_query($sql);
        $this->results['exhibition_results'] = array();
        while($assoc = mysql_fetch_assoc($result)) {
            array_push($this->results['exhibition_results'], $assoc);
        }
        $this->results['exhibition_count'] = count($this->results['exhibition_results']);

    }

    private function institution($level = 0) {
        $sql='';
        if($this->sorting) {
            $sql.= "SELECT * FROM (";
        }
        $sql.="SELECT DISTINCT ".implode(", ", $this->institutionFields())." FROM institution";
        if($level >= 1) {
            $sql.= " RIGHT JOIN bestand on institution.id = bestand.inst_id";
            $sql.= " RIGHT JOIN bestand_fotograf on bestand.id = bestand_fotograf.bestand_id";
            $sql.= " INNER JOIN fotografen on bestand_fotograf.fotografen_id = fotografen.id";
            $sql.= " RIGHT JOIN namen on fotografen.id = namen.fotografen_id";
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
        $sql.= " AND institution.gesperrt = 0";
        if($level >= 1) {
            $sql.= " ORDER BY bestand.nachlass DESC";
        }
        $sql.= " LIMIT ".$this->limitResults;

        if($this->sorting) {
            $sql.= ") AS T1 ORDER BY ".$this->sorting.' '.$this->sortDirection;
        }


        $result = mysql_query($sql);
        $this->results['institution_results'] = array();
        while($assoc = mysql_fetch_assoc($result)) {
            array_push($this->results['institution_results'], $assoc);
        }
        $this->results['institution_count'] = count($this->results['institution_results']);
    }

    private function stock($level = 0) {
        $sql='';
        if($this->sorting) {
            $sql.= "SELECT * FROM (";
        }
        $sql.="SELECT DISTINCT ".implode(", ", $this->stockFields())." FROM bestand";
        $sql.= " INNER JOIN institution ON bestand.inst_id = institution.id";
        if($level >= 1) {
            $sql.= " RIGHT JOIN bestand_fotograf ON bestand.id = bestand_fotograf.bestand_id";
            $sql.= " RIGHT JOIN namen ON bestand_fotograf.fotografen_id = namen.fotografen_id";
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
                $sql.= "bestand.name LIKE '%".$term."%'";
                $sql.= " OR bestand.zeitraum LIKE '%".$term."%'";
            }
            if($level >= 1) {
                $sql.= " OR namen.nachname LIKE '%".$term."%'";
                $sql.= " OR namen.vorname LIKE '%".$term."%'";
            }
            $sql.= ')';
        }
        $sql.= " AND bestand.gesperrt = 0";
        $sql.= " LIMIT ".$this->limitResults;

        if($this->sorting) {
            $sql.= ") AS T1 ORDER BY ".$this->sorting.' '.$this->sortDirection;
        }


        $result = mysql_query($sql);
        $this->results['stock_results'] = array();
        while($assoc = mysql_fetch_assoc($result)) {
            array_push($this->results['stock_results'], $assoc);
        }
        $this->results['stock_count'] = count($this->results['stock_results']);

    }

    private function photographer($level = 0) {
        $sql = '';
        if($this->sorting) {
            $sql.= "SELECT * FROM (";
        }
        $sql.= "SELECT DISTINCT ".implode(", ", $this->photographerFields())." FROM namen";
        $sql.= " LEFT JOIN fotografen on namen.fotografen_id = fotografen.id";
        /* removed due to performance...
        if($level >= 1) {
            $sql.= " LEFT JOIN arbeitsperioden on arbeitsperioden.fotografen_id = fotografen.id";
        }*/
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
            /* removed due to performance...
            if($level >= 1) {
                $sql.= " OR arbeitsperioden.arbeitsort LIKE '%".$term."%'";
            }*/
            if($level >= 1){
                $sql.= " OR fotografen.werdegang".$this->lang." LIKE '%".$term."%'";
                $sql.= " OR fotografen.schaffensbeschrieb".$this->lang." LIKE '%".$term."%'";
            }
            $sql.= ")";
        }
        $sql.= " AND fotografen.unpubliziert = 0";

        $sql.= " ORDER BY (CASE WHEN namen.nachname LIKE '".$q[0]."%' THEN 100 ELSE 0 END) DESC";
        $sql.= " LIMIT ".$this->limitResults;

        if($this->sorting) {
            $sql.= ") AS T1 ORDER BY ".$this->sorting.' '.$this->sortDirection;
        }

        // TODO: Add prioritazion with "order by (case when x = 'hello' then 1 else 2 end)"

        $result = mysql_query($sql);
        $this->results['photographer_results'] = array();
        while($assoc = mysql_fetch_assoc($result)) {
            array_push($this->results['photographer_results'], $assoc);
        }
        $this->results['photographer_count'] = count($this->results['photographer_results']);
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


    public function activate() {
        if(array_key_exists('query', $_GET) && strlen($_GET['query']) >= 2 ) {
            $this->query = $_GET['query'];
            return true;
        }
        return true;
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

    public function setPhotoLimit($limit) {
        $this->limitPhotoResults = $limit;
    }

    public function setType($type) {
        $this->type = $type;
    }

    public function setSorting($sort) {
        $this->sorting = $sort;
    }

    public function setSortDirection($direction) {
        if($direction == 'asc' || $direction == 'desc') {
            $this->sortDirection = $direction;
        }
    }

}

?>