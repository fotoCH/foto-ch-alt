<?php

$filters = new Filters();
$filters->setLang($glob['LANG']);
if (array_key_exists('type', $_GET)) {
  $filters->output($_GET['type']);
} else {
  $filters->output('No type set.');
}


class Filters
{
  private $filters = array();
  private $type = false;
  private $language = '';
  private $cachePrefix = 'filter_';

  public function setLang($lang) {
    if($lang != 'de') {
      $this->language = '_'.$lang;
    }
  }

  private function unknown()
  {
    $this->filters['type'] = 'Unknown Type: ' . $this->type;
  }

  private function fotografengattungen()
  {
    $this->distinctFieldValues("fotografen.fotografengattungen_set", "fotografen");
    $filters = implode(',', $this->filters['possible_values']);
    $this->filters['translations'] = explode(',', setuebersetzungen('fotografengattungen_uebersetzungen', $filters));
  }

  private function bildgattungen()
  {
    $this->distinctFieldValues("fotografen.bildgattungen_set", "fotografen");
    $filters = implode(',', $this->filters['possible_values']);
    $this->filters['translations'] = explode(',', setuebersetzungen('bildgattungen_uebersetzungen', $filters));
  }

  private function fotografenKanton()
  {
    $this->distinctFieldValues("fotografen.kanton", "fotografen");
  }

  private function institutionKanton()
  {
    $this->distinctFieldValues("institution.kanton", "institution", " WHERE institution.gesperrt = 0");
  }

  private function institution()
  {
    $this->distinctFieldValues("institution.name", "institution", " WHERE institution.gesperrt = 0");
  }

  private function exhibitionInstitution()
  {
    $this->distinctFieldValues("institution", "ausstellung");
  }

  private function stockInstitution()
  {
    $this->distinctFieldValues("institution.name", "institution, bestand", " WHERE institution.gesperrt = 0 AND institution.id=bestand.inst_id");
  }

  private function photoInstitution()
  {
    $this->distinctFieldValues("institution.name", "institution", " JOIN bestand on bestand.inst_id = institution.id JOIN fotos on fotos.dcterms_ispart_of = bestand.id  WHERE institution.gesperrt = 0");
  }

  private function institutionOrt()
  {
    $this->distinctFieldValues("institution.ort", "institution", " WHERE institution.gesperrt = 0");
  }

  private function ausstellungJahr()
  {
    $this->distinctFieldValues("ausstellung.jahr", "ausstellung", "", true);
  }

  private function ausstellungTyp()
  {
    $this->distinctFieldValues("ausstellung.typ", "ausstellung");
  }

  private function ausstellungOrt()
  {
    $this->distinctFieldValues("ausstellung.ort", "ausstellung");
  }

  private function literaturJahr()
  {
    $this->distinctFieldValues("literatur.jahr", "literatur");
  }

  private function literaturOrt()
  {
    $this->distinctFieldValues("literatur.ort", "literatur");
  }

  private function photoStickworte()
  {
    $this->distinctFieldValues("dcterms_subject", "fotos");
  }

  private function photoStocks()
  {
    global $sqli;
    $this->filters['possible_values'] = array();
    $sql = "SELECT DISTINCT dcterms_ispart_of as 'value' FROM fotos";
    $set = mysqli_query($sqli, $sql);
    while ($row = mysqli_fetch_assoc($set)) {
      $possible = $row['value'];
      if (!array_key_exists($possible, $this->filters['possible_values'])) {
        $stockQuery = "SELECT name FROM bestand WHERE id=" . $possible;
        $result = mysqli_query($sqli, $stockQuery);
        while ($stock = mysqli_fetch_assoc($result)) {
          array_push($this->filters['possible_values'], array(
            "id" => $possible,
            "value" => $stock['name']
          ));
          $sortValues[] = $stock{'name'};
        }
      }
    }
    if ($desc) {
      array_multisort($values, SORT_DESC, $this->filters['possible_values']);
    } else {
      array_multisort($sortValues, SORT_ASC, $this->filters['possible_values']);
    }
  }

  private function arbeitsorte()
  {
    $where = " WHERE name LIKE '".$_GET['search']."%'";
    $this->distinctFieldValues("name", "arbeitsorte", $where);
  }

  private function verlag()
  {
    $this->distinctFieldValues("verlag", "literatur");
  }

  private function zeitraum()
  {
    $this->distinctFieldValues("zeitraum", "fotos");
  }

  private function spatial()
  {
    $this->distinctFieldValues("dcterms_spatial", "fotos");
  }

  private function distinctFieldValues($field, $table, $where = '', $desc = false)
  {
    global $sqli;
    $this->filters['possible_values'] = array();
    $sql = "SELECT DISTINCT " . $field . " as 'value' FROM " . $table . $where;
    $set = mysqli_query($sqli, $sql);
    while ($row = mysqli_fetch_assoc($set)) {
      $possibles = explode(",", $row['value']);
      foreach ($possibles as $poss) {
        $poss = trim($poss);
        if ($poss == '') {
          continue;
        }
        if (!in_array($poss, $this->filters['possible_values'])) {
          array_push($this->filters['possible_values'], $poss);
        }
      }
    }
    if ($desc) {
      rsort($this->filters['possible_values']);
    } else {
      sort($this->filters['possible_values']);
    }
  }

  public function output($type)
  {
    $this->type = $type;
    $cacheObj = new fotoCache($this->cachePrefix . '_' . $this->language);
    if($cacheObj->isCached($this->type) && !isset($_GET['cache']) ){
      $this->filters = $cacheObj->getCache($this->type);
    }else {
      switch ($this->type) {
        case 'fotografengattungen':
          $this->fotografengattungen();
          break;
        case 'bildgattungen':
          $this->bildgattungen();
          break;
        case 'fotografen_kanton':
          $this->fotografenKanton();
          break;
        case 'institution':
          $this->institution();
          break;
        case 'ausstellung_institution':
          $this->exhibitionInstitution();
          break;
        case 'bestand_institution':
          $this->stockInstitution();
          break;
        case 'photo_institution':
          $this->photoInstitution();
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
        case 'arbeitsorte':
          $this->arbeitsorte();
          break;
        case 'verlag':
          $this->verlag();
          break;
        case 'zeitraum':
          $this->zeitraum();
          break;
        case 'photo_ort':
          $this->spatial();
          break;
        default :
          $this->unknown();
          break;
      }
      $cacheObj->cache($this->type, $this->filters);
    }
    jsonout($this->filters);
  }

}

?>
