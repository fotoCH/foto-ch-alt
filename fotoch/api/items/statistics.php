<?php

$statistics = new Statistics();
$statistics->output();


class Statistics {
    private $stats = array();

    public function __construct() {
        $this->photographer();
        $this->institution();
        $this->photos();
        $this->stocks();
        $this->literature();
        $this->exhibition();
    }

    private function photographer() {
        $this->stats['photographer'] = array();
        $this->stats['photographer']['count'] = $this->count('fotografen', 'WHERE unpubliziert = 0');
        $this->stats['photographer']['biographies'] = $this->count('fotografen', 'WHERE werdegang <> ""');
    }

    private function exhibition() {
        $this->stats['exhibition'] = array();
        $this->stats['exhibition']['count'] = $this->count('ausstellung');
    }


    private function literature() {
        $this->stats['literature'] = array();
        $this->stats['literature']['count'] = $this->count('literatur');
    }

    private function institution() {
        $this->stats['institution'] = array();
        $this->stats['institution']['count'] = $this->count('institution', 'WHERE gesperrt = 0');
    }

    private function stocks() {
        $this->stats['stocks'] = array();
        $this->stats['stocks']['count'] = $this->count('bestand', 'JOIN institution ON bestand.inst_id = institution.id WHERE institution.gesperrt=0 AND bestand.`gesperrt`=0', 'bestand.id');
    }

    private function photos() {
        $this->stats['photos'] = array();
        $this->stats['photos']['count'] = $this->count('fotos');
    }

    private function count($table, $where='', $id_field='id') {
        $sql = "SELECT COUNT($id_field) as num FROM $table $where";
        $result = mysql_query($sql);
        while($row = mysql_fetch_assoc($result)) {
            return $row['num'];
        }
    }

    public function output() {
        jsonout($this->stats);
    }

}

?>