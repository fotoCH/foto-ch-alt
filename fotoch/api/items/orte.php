<?php


$venue = new Venue($_GET);

$photographers = $venue->getPhotographers();
$photos = $venue->getPhotos();

jsonout(array_merge($photographers, $photos));

class Venue{

    private $term;
    private $searchPhotographers;
    private $searchPhotos;

    function __construct($get_array)
    {
        $this->term = isset($get_array['query']) ? mysql_escape_string($get_array['query']) : false;
        $this->searchPhotographers = isset($get_array['photographer']) ? filter_var($get_array['photographer'], FILTER_VALIDATE_BOOLEAN) : true;
        $this->searchPhotos = isset($get_array['photos']) ? filter_var($get_array['photos'], FILTER_VALIDATE_BOOLEAN) : true;
    }

    /**
     * @return array
     */
    public function getPhotographers(){
        if($this->searchPhotographers){
            $query = "SELECT *, '' as dc_right, '' as image_path FROM arbeitsorte WHERE lat<>0";

            if($this->term){
               $query .= " AND name LIKE '%" . $this->term . "%'";
            }

            return $this->getResultasArray($query);

        }else{
            return array();
        }
    }

    /**
     * @return array
     */
    public function getPhotos(){
        if($this->searchPhotos){
            $query = "SELECT *, CONCAT(dcterms_spatial,': ', dc_title) as name, 'fotoquery' as swissname FROM fotos WHERE lat<>0";

            if($this->term){
                $query .= " AND dcterms_spatial LIKE '%" . $this->term . "%'";
            }
            return $this->getResultasArray($query);
        }else{
            return array();
        }
    }

    /**
     * @param $query
     * @return array
     */
    private function getResultasArray($query){
        $result = getfromselect($query);
        return $result ? $result : array();
    }

}
?>
