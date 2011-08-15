<?php
class EditFotograf extends Edit {

	function __construct($def) {
		parent::__construct($def,'fotograf');
	}

	function namen($id){ //nur fotograf
		global $spr;
		$def = $this->def;
		$sql = "SELECT * FROM namen WHERE fotografen_id=$id ORDER BY id"; // Weitere Formdaten aus Tabelle 'Namen' holen
		$result = mysql_query($sql);
		if(mysql_num_rows($result)>0){
			$num=1;
			while($array=mysql_fetch_array($result)){
				if ($num==1){
					$def->assign("DELETE", "");
					$def->assign("STANDARD", "(Standard)");				
				}else{
					$lang = $_GET['lang'];
					$def->assign("DELETE", "<a href=\"./?a=edit&amp;n=del&n_id=$array[id]&amp;id=$id&amp;lang=$lang\">[&nbsp;".$spr['loeschen']."&nbsp;]</a>");// does not work ??				
					$def->assign("STANDARD", "");	
					$def->assign("BR","<br />")	;		
				}
				$def->assign("NUM", $num);
				$def->assign("NAMEN", $array);			
				$def->parse("bearbeiten.form.namen");
				$def->parse("bearbeiten.form");
				$num++;
			}
			$def->parse("bearbeiten.form.new_namen");
			$def->parse("bearbeiten.form");
		}else{
			$def->parse("bearbeiten.form.new_namen");
			$def->parse("bearbeiten.form");
		}
	}
	
	function arbeitsperioden($id){//nur fotograf
		$def = $this->def;
		$sql = "SELECT * FROM `arbeitsperioden` WHERE fotografen_id = $id ORDER BY id asc"; // Weitere Formdaten aus Tabelle 'arbeitsperioden' holen
		$result = mysql_query($sql);
		if(mysql_num_rows($result)>0){
			$num=1;
			while($array=mysql_fetch_array($result)){
				if($array['um_von']=="1"){
					$def->assign("check_von", "checked=\"checked\"");
				}else{
					$def->assign("check_von", "");
				}
				if($array['um_bis']=="1"){
					$def->assign("check_bis", "checked=\"checked\"");
				}else{
					$def->assign("check_bis", "");
				}
				$def->assign("NUM", $num);
				$def->assign("ARBEITSORT", $array);
				$def->parse("bearbeiten.form.arbeitsort");
				$def->parse("bearbeiten.form");
				$num++;
			}
			$def->parse("bearbeiten.form.new_arbeitsort");
			$def->parse("bearbeiten.form");
			
		} else {
			$def->parse("bearbeiten.form.new_arbeitsort");
			$def->parse("bearbeiten.form");
		}
	}
}
	
class Edit {
	protected $def;
	protected $type;
	public $type_plur;
	public $type_suffix;
	function __construct($def,$type) {
		$this->def = $def;
		$this->type = $type;
		$this->type_plur = "{$type}";
		$this->type_suffix = "_{$type}";
		if( $type == 'fotograf' ) {
			$this->type_plur = "fotografen";
			$this->type_suffix = '';
		}
	}
	
	function bestand_qry($id) {
		if( $this->type == 'institution') {
			return "SELECT * FROM `bestand` WHERE inst_id = $id ORDER BY bestand.nachlass DESC, bestand.name ASC";
		} elseif ( $this->type == 'fotograf' ) {
			return "SELECT bestand_fotograf.fotografen_id, bestand_fotograf.id AS bf_id, institution.name AS inst_name, institution.id AS inst_id, bestand.*
				FROM bestand_fotograf INNER JOIN (bestand INNER JOIN institution ON bestand.inst_id = institution.id) ON bestand_fotograf.bestand_id = bestand.id
				WHERE bestand_fotograf.fotografen_id=$id ORDER BY bestand.nachlass DESC, bestand.name ASC"; // Weitere Formdaten aus Tabelle 'bestaende' holen
		}
	}
	
	function bestand($id){
       $sql = $this->bestand_qry($id);
       $def = $this->def;
       $result = mysql_query($sql);
       if(mysql_num_rows($result)>0){
               $num=1;
               while($array=mysql_fetch_array($result)){
                       $def->assign("NUM", $num);
                       $def->assign("BESTAND", $array);
                       $def->parse("bearbeiten.form.bestand{$this->type_suffix}");
                       $def->parse("bearbeiten.form");
                       $num++;
               }
       }
       $def->parse("bearbeiten.form.new_bestand{$this->type_suffix}");
       $def->parse("bearbeiten.form");
	}
	
	
	function namendf($id,$n=''){
		global $spr;
		$def = $this->def;
		if( $this->type == 'institution') {
			$def->assign("ID", $id);
			$def->assign("NAME", $n);			
			$def->parse("bearbeiten.form.namendfi");
			$def->parse("bearbeiten.form");
			return;
		}
		$sql = "SELECT * FROM namen WHERE fotografen_id=$id ORDER BY id"; // Weitere Formdaten aus Tabelle 'Namen' holen
		$result = mysql_query($sql);
		if(mysql_num_rows($result)>0){
			$num=1;
			while($array=mysql_fetch_array($result)){
				if ($num==1){
					$def->assign("STANDARD", "(Standard)");				
				}else{
					$lang = $_GET['lang'];
					$def->assign("BR","<br />")	;		
				}
				$def->assign("NUM", $num);
				$def->assign("NAMEN", $array);			
				$def->parse("bearbeiten.form.namendf");
				$def->parse("bearbeiten.form");
				$num++;
			}
		}
	}
	
	function literatur($id){
		$def = $this->def;
		$sql = "SELECT literatur_{$this->type}.{$this->type_plur}_id, literatur_{$this->type}.id AS if_id, ".
		($this->type=='fotograf'?"literatur_{$this->type}.typ AS if_typ, ":"").
		"literatur.* FROM literatur_{$this->type} INNER JOIN literatur ON literatur_{$this->type}.literatur_id = literatur.id
		WHERE literatur_{$this->type}.{$this->type_plur}_id=$id ORDER BY if_id"; // Weitere Formdaten aus Tabelle 'bestaende' holen
		$result = mysql_query($sql);
		if(mysql_num_rows($result)>0){
			$num=1;
			while($array=mysql_fetch_array($result)){	
				$def->assign("NUM", $num);
				$array=formlit($array);
				$def->assign("LITERATUR", $array);
				$def->parse("bearbeiten.form.literatur");
				$def->parse("bearbeiten.form");
				$num++;
			}
		}
		$def->parse("bearbeiten.form.new_literatur{$this->type_suffix}");
		$def->parse("bearbeiten.form");
	}
	function ausstellungen($id){
		$def = $this->def;
		// ausstellungen
		$sql = "SELECT ausstellung_{$this->type}.{$this->type}_id, ausstellung_{$this->type}.id AS af_id, ausstellung.*
		FROM ausstellung_{$this->type} INNER JOIN ausstellung ON ausstellung_{$this->type}.ausstellung_id = ausstellung.id
		WHERE ausstellung_{$this->type}.{$this->type}_id=$id ORDER BY ausstellung.typ, af_id"; // Weitere Formdaten aus Tabelle 'bestaende' holen
		$result = mysql_query($sql);
		if(mysql_num_rows($result)>0){
			$num=1;
			while($array=mysql_fetch_array($result)){
				$def->assign("NUM", $num);
				$def->assign("AUSSTELLUNG", $array);			
				$def->parse("bearbeiten.form.ausstellung");
				$def->parse("bearbeiten.form");
				$num++;
			}
		}
		$def->parse("bearbeiten.form.new_ausstellung{$this->type_suffix}");	
		$def->parse("bearbeiten.form");
	}
	
	function writeHistory($id,$line){
		$sql="UPDATE $this->type_plur SET history=CONCAT(history,'".mysql_real_escape_string($line."\r\n")."') WHERE id=$id LIMIT 1";
		//echo $sql;
		$result = mysql_query($sql);
		return;
	}	
}

function getHChanged($l,$n,$o){
	return $l.'=\''.$n.'\' old:\''.$o.'\'';
}

?>