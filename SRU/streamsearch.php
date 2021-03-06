<?php  // angepasste Version fuer SRU

class StreamedSearch
{
    private $lang = '';
    private $limitResults = 100;
    private $limitPhotoResults = false;
    private $query = "";
    private $type = false;
    private $sorting = false;
    private $sortDirection = 'ASC';
    private $result = array();
    private $offset = 0;
    private $direct = array();
    private $onlyGeoRefImages = false;

    private $levels = array(
        'photographer' => 2,
        'stock' => 2,
        'institution' => 2,
        'exhibition' => 1,
        'literature' => 2,
        'photos' => 2
    );

    private function photoFields()
    {
        return array(
            "fotos.id",
            "fotos.lat",
            "fotos.lon",
            "fotos.dc_title as title",
            "fotos.dc_description as description",
            "fotos.dcterms_subject as descriptor",
            "fotos.dcterms_spatial as ort",
            "fotos.dc_identifier as identifier",
            "fotos.dc_right as copyright",
            "fotos.image_path",
            "fotos.zeitraum",
            "namen.vorname AS autor_forename",
            "namen.nachname AS autor_lastname",
            "institution.name AS institution",
            "bestand.name as bestand"
        );
    }

    private function photographerFields()
    {
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
            "(SELECT GROUP_CONCAT(DISTINCT arbeitsort) FROM arbeitsperioden WHERE fotografen.id = arbeitsperioden.fotografen_id) as arbeitsorte"
        );
    }

    private function stockFields()
    {
        return array(
            "bestand.id",
            "bestand.name",
            "bestand.zeitraum",
            "bestand.bildgattungen",
            "bestand.link_extern",
            "bestand.signatur",
            "bestand.umfang",
            "institution.name AS institution",
            "institution.ort AS ort",
            "institution.kanton AS kanton"
        );
    }

    private function institutionFields()
    {
        return array(
            "institution.id",
            "institution.name",
            "institution.name_fr",
            "institution.name_it",
            "institution.name_rm",
            "institution.name_en",
            "institution.abkuerzung" . $this->lang . " AS shortname",
            "institution.art",
            "institution.adresse",
            "institution.plz",
            "institution.ort",
            "institution.kanton",
            "institution.zugang_zur_sammlung" . $this->lang . " AS access",
            "institution.sammlungszeit_von",
            "institution.sammlungszeit_bis"
        );
    }

    private function exhibitionFields()
    {
        return array(
            "ausstellung.id",
            "ausstellung.titel",
            "ausstellung.jahr",
            "ausstellung.ort",
            "ausstellung.institution",
            "REPLACE( REPLACE( ausstellung.typ,  'E',  '%s' ) ,  'G', '%s' ) as typ"
        );
    }

    private function literatureFields()
    {
        return array(
            "literatur.id",
            "literatur.titel",
            "literatur.verfasser_name",
            "literatur.verfasser_vorname",
            "literatur.jahr",
            "literatur.ort",
            "literatur.verlag"
        );
    }


    public function query()
    {
        $levels = 3;
        for ($level = 0; $level <= $levels; $level++) {
            $this->getLevelResults($level);
        }
    }

    private function getLevelResults($level)
    {
        $types = array('photographer', 'stock', 'institution', 'exhibition', 'literature', 'photos');

        foreach ($types as $type) {
            if (!$this->enoughOfType($type, $level)) {
                if (!$this->type || $this->type == $type) {
                    $this->$type($level);
                    $this->response();
                }
            }
        }
    }

    private function photos($level)
    {
        global $sqli;
        $sql = '';
        $sql .= "SELECT SQL_CALC_FOUND_ROWS DISTINCT " . implode(", ", $this->photoFields()) . " FROM fotos";
        $sql .= " INNER JOIN fotografen on fotografen.id = fotos.dc_creator";
        $sql .= " RIGHT JOIN namen on fotografen.id = namen.fotografen_id";
        $sql .= " INNER JOIN bestand on fotos.dcterms_ispart_of = bestand.id";
        $sql .= " INNER JOIN institution on bestand.inst_id = institution.id";

        $q = explode(" ", $this->query);
        $first = true;
        foreach ($q as $term) {
            $term = mysqli_real_escape_string($sqli, $term);
            if ($first) {
                $first = false;
                $sql .= " WHERE ";
            } else {
                $sql .= " AND ";
            }
            $sql .= '(';
            if ($level >= 0) {
                $sql .= "fotos.dc_title LIKE '%" . $term . "%'";
                $sql .= " OR fotos.dc_description LIKE '%" . $term . "%'";
                $sql .= " OR fotos.dc_created LIKE '%" . $term . "%'";
                $sql .= " OR fotos.dcterms_subject LIKE '%" . $term . "%'";
                $sql .= " OR fotos.dcterms_spatial LIKE '%" . $term . "%'";
            }
            if ($level >= 1) {
                $sql .= " OR namen.vorname LIKE '%" . $term . "%'";
                $sql .= " OR namen.nachname LIKE '%" . $term . "%'";
                $sql .= " OR bestand.name" . " LIKE '%" . $term . "%'";
                $sql .= " OR institution.name" . " LIKE '%" . $term . "%'";
            }
            if ($level >= 2) {
                $sql .= " OR fotos.dcterms_medium LIKE '%" . $term . "%'";
                $sql .= " OR fotos.dcterms_subject LIKE '%" . $term . "%'";
            }
            $sql .= ')';
        }
        if ($level >= 1) {
            $sql .= " AND fotografen.unpubliziert = 0";
        }

        if ($this->onlyGeoRefImages) {
            $sql .= " AND fotos.lat <> 0";
            $this->limitResults = 9999999;
        }

        $sql .= $this->appendDirectQuery();

        $sql .= " GROUP BY fotos.id ";

        if ($this->sorting) {
            $sql .= " ORDER BY " . $this->sorting . ' ' . $this->sortDirection;
        } else {
            $sql .= " ORDER BY id ASC";
        }

        if ($this->limitPhotoResults) {
            $sql .= " LIMIT " . $this->limitPhotoResults;
        } else {
            $sql .= " LIMIT " . $this->limitResults;
        }
        $sql .= " OFFSET " . $this->offset;

        $result = mysqli_query($sqli, $sql);
        $count_result = mysqli_query($sqli, "Select FOUND_ROWS() as total_count");
        $this->results['photos_results'] = array();
        while ($assoc = mysqli_fetch_assoc($result)) {
            array_push($this->results['photos_results'], $assoc);
        }
        $this->results['photos_count'] = count($this->results['photos_results']);
        $this->results['photos_total_count'] = mysqli_fetch_assoc($count_result)['total_count'];

    }

    private function literature($level = 0)
    {
        global $sqli;
        $sql = '';
        $sql .= "SELECT SQL_CALC_FOUND_ROWS DISTINCT " . implode(", ", $this->literatureFields()) . " FROM literatur";
        if ($level >= 1) {
            $sql .= " LEFT JOIN literatur_fotograf on literatur_fotograf.literatur_id = literatur.id";
            $sql .= " INNER JOIN fotografen on literatur_fotograf.fotografen_id = fotografen.id";
            $sql .= " LEFT JOIN literatur_institution on literatur_institution.literatur_id = literatur.id";
            $sql .= " LEFT JOIN institution on literatur_institution.institution_id = institution.id";
            $sql .= " RIGHT JOIN namen on fotografen.id = namen.fotografen_id";
        }

        $q = explode(" ", $this->query);
        $first = true;
        foreach ($q as $term) {
            $term = mysqli_real_escape_string($sqli, $term);
            if ($first) {
                $first = false;
                $sql .= " WHERE ";
            } else {
                $sql .= " AND ";
            }
            $sql .= '(';
            if ($level >= 0) {
                $sql .= "literatur.titel LIKE '%" . $term . "%'";
                $sql .= " OR literatur.verfasser_name LIKE '%" . $term . "%'";
                $sql .= " OR literatur.verfasser_vorname LIKE '%" . $term . "%'";
                $sql .= " OR literatur.jahr LIKE '%" . $term . "%'";
            }
            if ($level >= 1) {
                $sql .= " OR namen.vorname LIKE '%" . $term . "%'";
                $sql .= " OR namen.nachname LIKE '%" . $term . "%'";
                $sql .= " OR institution.name" . $this->lang . " LIKE '%" . $term . "%'";
                $sql .= " OR institution.abkuerzung" . $this->lang . " LIKE '%" . $term . "%'";
            }
            $sql .= ')';
        }
        if ($level >= 1) {
            $sql .= " AND fotografen.unpubliziert = 0";
        }

        $sql .= $this->appendDirectQuery();

        if ($this->sorting) {
            $sql .= " ORDER BY " . $this->sorting . ' ' . $this->sortDirection;
        } else {
            $sql .= " ORDER BY titel ASC";
        }
        $sql .= " LIMIT " . $this->limitResults;
        $sql .= " OFFSET " . $this->offset;

        $result = mysqli_query($sqli, $sql);
        $count_result = mysqli_query($sqli, "Select FOUND_ROWS() as total_count");
        $this->results['literature_results'] = array();
        while ($assoc = mysqli_fetch_assoc($result)) {
            array_push($this->results['literature_results'], $assoc);
        }
        $this->results['literature_count'] = count($this->results['literature_results']);
        $this->results['literature_total_count'] = mysqli_fetch_assoc($count_result)['total_count'];
    }

    private function exhibition($level = 0)
    {
        global $sqli;
        $cacheObj = new fotoCache('exhibition_');
        $key = md5($_SERVER['QUERY_STRING']);
        if ($cacheObj->isCached($key)) {
            $this->results = $cacheObj->getCache($key);
        } else {
            $langfield = $this->lang ? str_replace('_', '', $this->lang) : 'de';
            $translation_result = mysqli_query($sqli, "SELECT name, " . $langfield . " as translation FROM sprache WHERE name='einzelausstellung' OR name='gruppenausstellung'");
            while ($row = mysqli_fetch_assoc($translation_result)) {
                $translations[$row['name']] = $row['translation'];
            }

            $sql = '';
            $sql .= "SELECT SQL_CALC_FOUND_ROWS DISTINCT " . sprintf(implode(", ", $this->exhibitionFields()), $translations['einzelausstellung'], $translations['gruppenausstellung']) . " FROM ausstellung";
            $sql .= " LEFT JOIN ausstellung_fotograf ON ausstellung.id = ausstellung_fotograf.ausstellung_id";
            $sql .= " INNER JOIN fotografen ON ausstellung_fotograf.fotograf_id = fotografen.id";
            $sql .= " INNER JOIN namen on fotografen.id = namen.fotografen_id";

            $q = explode(" ", $this->query);
            $first = true;
            foreach ($q as $term) {
                $term = mysqli_real_escape_string($sqli, $term);
                if ($first) {
                    $first = false;
                    $sql .= " WHERE ";
                } else {
                    $sql .= " AND ";
                }
                $sql .= '(';
                if ($level >= 0) {
                    $sql .= "ausstellung.institution LIKE '%" . $term . "%'";
                    $sql .= " OR ausstellung.titel LIKE '%" . $term . "%'";
                    $sql .= " OR ausstellung.ort LIKE '%" . $term . "%'";
                    $sql .= " OR namen.vorname LIKE '%" . $term . "%'";
                    $sql .= " OR namen.nachname LIKE '%" . $term . "%'";
                }
                $sql .= ')';
            }

            $sql .= $this->appendDirectQuery();

            if ($this->sorting) {
                $sql .= " ORDER BY " . $this->sorting . ' ' . $this->sortDirection;
            } else {
                $sql .= " ORDER BY jahr DESC";
            }

            $sql .= " LIMIT " . $this->limitResults;
            $sql .= " OFFSET " . $this->offset;
            $result = mysqli_query($sqli, $sql);
            $count_result = mysqli_query($sqli, "Select FOUND_ROWS() as total_count");
            $this->results['exhibition_results'] = array();
            while ($assoc = mysqli_fetch_assoc($result)) {
                array_push($this->results['exhibition_results'], $assoc);
            }

            $this->results['exhibition_count'] = count($this->results['exhibition_results']);
            $this->results['exhibition_total_count'] = mysqli_fetch_assoc($count_result)['total_count'];

            $cacheObj->cache($key, $this->results);
        }
    }

    private function institution($level = 0)
    {
        global $sqli;
        $sql = '';
        $sql .= "SELECT SQL_CALC_FOUND_ROWS DISTINCT " . implode(", ", $this->institutionFields()) . " FROM institution";
        /*if($level >= 1) {

            $sql.= " RIGHT JOIN bestand on institution.id = bestand.inst_id";
            $sql.= " RIGHT JOIN bestand_fotograf on bestand.id = bestand_fotograf.bestand_id";
            $sql.= " INNER JOIN fotografen on bestand_fotograf.fotografen_id = fotografen.id";
            $sql.= " RIGHT JOIN namen on fotografen.id = namen.fotografen_id";
        }*/

        $q = explode(" ", $this->query);
        $first = true;
        foreach ($q as $term) {
            $term = mysqli_real_escape_string($sqli, $term);
            if ($first) {
                $first = false;
                $sql .= " WHERE ";
            } else {
                $sql .= " AND ";
            }
            $sql .= '(';
            if ($level >= 0) {
                $sql .= "institution.name" . $this->lang . " LIKE '%" . $term . "%'";
                $sql .= " OR institution.abkuerzung" . $this->lang . " LIKE '%" . $term . "%'";
                $sql .= " OR institution.ort LIKE '%" . $term . "%'";
                $sql .= " OR institution.plz LIKE '%" . $term . "%'";
            }
            if ($level >= 1) {
                // look for term in other languages (e.g. "archives")
                if ("institution.name" != "institution.name" . $this->lang) {
                    $sql .= " OR institution.name LIKE '%" . $term . "%'";
                }
                if ("institution.name_fr" != "institution.name" . $this->lang) {
                    $sql .= " OR institution.name_fr LIKE '%" . $term . "%'";
                }
                if ("institution.name_it" != "institution.name" . $this->lang) {
                    $sql .= " OR institution.name_it LIKE '%" . $term . "%'";
                }
                if ("institution.name_rm" != "institution.name" . $this->lang) {
                    $sql .= " OR institution.name_rm LIKE '%" . $term . "%'";
                }
                if ("institution.name_en" != "institution.name" . $this->lang) {
                    $sql .= " OR institution.name_en LIKE '%" . $term . "%'";
                }
            }
            /*
            if($level >= 1) {
                $sql.= " OR namen.nachname LIKE '%".$term."%'";
                $sql.= " OR namen.vorname LIKE '%".$term."%'";
            }*/
            $sql .= ')';
        }
        $sql .= " AND institution.gesperrt = 0";

        $sql .= $this->appendDirectQuery();

        /*
        if($level >= 1 && ! $this->sorting) {

            $sql.= " ORDER BY bestand.nachlass DESC";
        } else { */
        if ($this->sorting) {
            $sql .= " ORDER BY " . $this->sorting . ' ' . $this->sortDirection;
        } else {
            $sql .= " ORDER BY name asc";
        }
        /*}*/
        $sql .= " LIMIT " . $this->limitResults;
        $sql .= " OFFSET " . $this->offset;

        $result = mysqli_query($sqli, $sql);
        $count_result = mysqli_query($sqli, "Select FOUND_ROWS() as total_count");
        $this->results['institution_results'] = array();
        while ($assoc = mysqli_fetch_assoc($result)) {
            $assoc['name'] = clean_entry(clangcont($assoc, 'name'));
            array_push($this->results['institution_results'], $assoc);
        }
        $this->results['institution_count'] = count($this->results['institution_results']);
        $this->results['institution_total_count'] = mysqli_fetch_assoc($count_result)['total_count'];
    }

    private function stock($level = 0)
    {
        global $sqli;
        $sql = '';
        $sql .= "SELECT SQL_CALC_FOUND_ROWS DISTINCT " . implode(", ", $this->stockFields()) . " FROM bestand";
        $sql .= " INNER JOIN institution ON bestand.inst_id = institution.id";
        if ($level >= 1) {
            $sql .= " LEFT JOIN bestand_fotograf ON bestand.id = bestand_fotograf.bestand_id";
            $sql .= " LEFT JOIN namen ON bestand_fotograf.fotografen_id = namen.fotografen_id";
        }
        $q = explode(" ", $this->query);
        $first = true;
        foreach ($q as $term) {
            $term = mysqli_real_escape_string($sqli, $term);
            if ($first) {
                $first = false;
                $sql .= " WHERE ";
            } else {
                $sql .= " AND ";
            }
            $sql .= '(';
            if ($level >= 0) {
                $sql .= "bestand.name LIKE '%" . $term . "%'";
                $sql .= " OR bestand.zeitraum LIKE '%" . $term . "%'";
            }
            if ($level >= 1) {
                $sql .= " OR namen.nachname LIKE '%" . $term . "%'";
                $sql .= " OR namen.vorname LIKE '%" . $term . "%'";
            }
            $sql .= ')';
        }
        $sql .= " AND bestand.gesperrt = 0 AND institution.gesperrt = 0";

        $sql .= $this->appendDirectQuery();

        if ($this->sorting) {
            $sql .= " ORDER BY " . $this->sorting . ' ' . $this->sortDirection;
        } else {
            $sql .= " ORDER BY name ASC";
        }

        $sql .= " LIMIT " . $this->limitResults;
        $sql .= " OFFSET " . $this->offset;
        /*
                echo '<br>' . $sql . '<br>';
                echo '<br>' . $sql_count . '<br>';*/

        //$count = mysqli_fetch_assoc(mysqli_query($sql_count))['total_count'];
        $result = mysqli_query($sqli, $sql);
        $count_result = mysqli_query($sqli, "Select FOUND_ROWS() as total_count");

        $this->results['stock_results'] = array();
        while ($assoc = mysqli_fetch_assoc($result)) {
            array_push($this->results['stock_results'], $assoc);
        }
        $this->results['stock_count'] = count($this->results['stock_results']);
        $this->results['stock_total_count'] = mysqli_fetch_assoc($count_result)['total_count'];

    }

    private function photographer($level = 0)
    {
        global $sqli;
        $sql = '';
        $sql .= "SELECT SQL_CALC_FOUND_ROWS DISTINCT " . implode(", ", $this->photographerFields()) . " FROM namen";
        $sql .= " RIGHT JOIN fotografen on namen.fotografen_id = fotografen.id";
        /* removed due to performance...
        if($level >= 1) {
            $sql.= " LEFT JOIN arbeitsperioden on arbeitsperioden.fotografen_id = fotografen.id";
        }*/
        $q = explode(" ", $this->query);
        $first = true;
        foreach ($q as $term) {
            $term = mysqli_real_escape_string($sqli, $term);
            if ($first) {
                $first = false;
                $sql .= " WHERE ";
            } else {
                $sql .= " AND ";
            }
            if ($level >= 0) {
                $sql .= "(namen.nachname LIKE '%" . $term . "%'";
                $sql .= " OR namen.vorname LIKE '%" . $term . "%'";
                $sql .= " OR namen.namenszusatz LIKE '%" . $term . "%'";
            }
            /* removed due to performance...
            if($level >= 1) {
                $sql.= " OR arbeitsperioden.arbeitsort LIKE '%".$term."%'";
            }*/
            if ($level >= 1) {
                $sql .= " OR fotografen.werdegang" . $this->lang . " LIKE '%" . $term . "%'";
                $sql .= " OR fotografen.schaffensbeschrieb" . $this->lang . " LIKE '%" . $term . "%'";
            }
            $sql .= ")";
        }
        $sql .= " AND fotografen.unpubliziert = 0";

        $sql .= $this->appendDirectQuery();

        if ($this->sorting) {
            $sql .= " ORDER BY " . $this->sorting . ' ' . $this->sortDirection;
        } else {
            //$sql .= " ORDER BY nachname, vorname asc";
            $sql .= " ORDER BY ( CASE ";
            $sql .= "  WHEN namen.nachname LIKE '" . $q[0] . "%' THEN 100 ";
            $sql .= "  WHEN namen.vorname LIKE '" . $q[0] . "%' THEN 80 ";
            $sql .= " ELSE 0 END) DESC";
        }
        $sql .= " LIMIT " . $this->limitResults;
        $sql .= " OFFSET " . $this->offset;

        $result = mysqli_query($sqli, $sql);
        $count_result = mysqli_query($sqli, "Select FOUND_ROWS() as total_count");
        $this->results['photographer_results'] = array();
        while ($assoc = mysqli_fetch_assoc($result)) {
            if ($_GET['lang'] != 'de') {
                $assoc['fotografengattungen_set'] = setuebersetzungen('fotografengattungen_uebersetzungen', $assoc['fotografengattungen_set']);
                $assoc['bildgattungen_set'] = setuebersetzungen('bildgattungen_uebersetzungen', $assoc['bildgattungen_set']);
            }
            $assoc['dateofbirth'] = formdatesimp($assoc ['geburtsdatum'], $assoc ['gen_geburtsdatum']);
            $assoc['dateofdeath'] = formdatesimp($assoc ['todesdatum'], $assoc ['gen_todesdatum']);
            array_push($this->results['photographer_results'], $assoc);
        }
        $this->results['photographer_count'] = count($this->results['photographer_results']);
        $this->results['photographer_total_count'] = mysqli_fetch_assoc($count_result)['total_count'];
    }

    private function appendDirectQuery()
    {
        $returnable = '';
        foreach ($this->direct as $direct) {
	    if(is_numeric($direct['value'])) {
                $returnable .= " AND " . $direct['field'] . " = " . $direct['value'];
	    } else {
                $returnable .= " AND " . $direct['field'] . " LIKE '%" . $direct['value'] . "%' ";
            }
        }
        return $returnable;
    }

    private function enoughOfType($type, $level)
    {
        // level is higher then accepted for this type...
        if ($level >= $this->levels[$type]) {
            return true;
        }
        // not one result is set yet...
        if (!is_array($this->results) || !array_key_exists($type . '_results', $this->results)) {
            return false;
        }
        // not enough results yet for the limit...
        if (count($this->results[$type . '_results']) <= $this->limitResults) {
            /*echo $level . '&' . $type . ': <br>';
            var_dump(count($this->results[$type . '_results']));
            var_dump($this->results);*/
            return false;
        }
        return true;
    }


    public function activate()
    {
        if (array_key_exists('query', $_GET) && strlen($_GET['query']) >= 2) {
            $this->query = $_GET['query'];
            return true;
        }
        return true;
    }

    public function setQuery($query)
    {
        $this->query = $query;
    }

    public function setLang($lang)
    {
        if ($lang != 'de') {
            $this->lang = '_' . $lang;
        }
    }

    public function response()
    {
        //jsonout($this->results, true);
    }

    public function setLimit($limit)
    {
        $this->limitResults = $limit;
    }

    public function setPhotoLimit($limit)
    {
        $this->limitPhotoResults = $limit;
    }

    public function setType($type)
    {
        $this->type = $type;
    }

    public function setSorting($sort)
    {
        $this->sorting = $sort;
    }

    public function setOffset($offset)
    {
        if (is_numeric($offset)) {
            $this->offset = $offset;
        }
    }

    public function setDirectQuery($query)
    {
        $q = explode(",", $query);
        foreach ($q as $where) {
            $where = explode(":", $where);
            array_push($this->direct, array(
                'field' => $where[0],
                'value' => $where[1]
            ));
        }
    }

    public function setGeoRef($value)
    {
        $this->onlyGeoRefImages = boolval($value);
    }

    public function setSortDirection($direction)
    {
        if ($direction == 'asc' || $direction == 'desc') {
            $this->sortDirection = $direction;
        }
    }

}

?>
