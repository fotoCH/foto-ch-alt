<?php 

class DokufichenFotografFormBuilder extends DokuficheFormBuilder {
	
	protected $type = 'fotograf';
	static function bearbeitungstiefen() {
		return array(0=>'0: L&auml;rm',1=>'1: K U Be Bi A',2=>'2: K U Be Bi A W',3=>'3: K U Be Bi A W S I',4=>'4: K U Be Bi A W S I Ue (I/F/D)',5=>'5: K U Be Bi A W S I Ue (I/F/D/E)');
	}
	
	function formName() { global $spr;
		$spr['fotografennamen'];
	}
	function generate() {
		$this->g_head();
		$this->g_info();
		$this->g_history();
		$this->g_vita();
		$this->g_transl();
		$this->g_doku();	
	}
}

class DokufichenInstitutionFormBuilder extends DokuficheFormBuilder {
	protected $type = 'institution';
	static function bearbeitungstiefen() {
		return array(0=>'0:',1=>'1: Lokal',2=>'2: Regional',3=>'3: Kantonal',4=>'4: National (&Uuml;bersetzungen D/F/I)',5=>'5: International (&Uuml;bersetzungen D/F/I/E)');
	}
	
	function formName() { global $spr;
		$spr['institution'];
	}
	function generate() {
		$this->g_head();
		$this->g_basic_info();
		$this->g_history();
		$this->g_todo();
		$this->g_update();
		$this->g_transl();
		$this->g_doku();
	}
}

class DokuficheFormBuilder {
	public $edit;
	protected $def;
	protected $id;
	public $formData;
	
	function __construct( $def ) {
		$this->def = $def;
		$this->edit = new Edit( $def, $this->type );
	}
	
	function loadformData() {
		$sql = "SELECT * FROM {$this->edit->type_plur} WHERE id ='{$this->id}'";
		$result = mysql_query($sql);
		$this->formData = mysql_fetch_array($result);
	}
	
	function init($id) {
		$this->id = $id;
		$this->loadformData();
	}
	
	function endPara() {
		$this->def->parse("bearbeiten.form.tend");
		$this->def->parse("bearbeiten.form");
		$this->_endPara();
		mabstand($this->def);
	}
	
	function _endPara() {
		$this->def->parse("bearbeiten.form.fieldset_end");
		$this->def->parse("bearbeiten.form");
	}
	
	function g_head() {
		$this->_title($this->formName());
		$this->edit->namendf($this->id, $this->formData["name"]);
		$this->_endPara();
		$this->def->parse("bearbeiten.bearbeiten_head_dokufiche_".$this->edit->type_plur);
		mabstand($this->def);
	}
	
	function _title($name) {
		$this->def->assign("LEGEND", "<b>".$name."</b>");
		$this->def->parse("bearbeiten.form.fieldset_start");
		$this->def->parse("bearbeiten.form");
	}
	function title($name) {
		$this->_title($name);
		$this->def->parse("bearbeiten.form.start");
		$this->def->parse("bearbeiten.form");
	}
	
	function g_basic_info() {
		global $spr;
		$array_eintrag = $this->formData;
		$this->title( $spr['allgemeine_informationen'] );
		genformitem($this->def,'textfield',$spr['projektname'],$array_eintrag['projektname'],'projektname');

		$arr_territoriumszugegoerigkeit=array('de'=>'de','fr'=>'fr','it'=>'it','rm'=>'rm','en'=>'en'); //Array füllen für Select
		genselectitem($this->def, $spr['territoriumszugegoerigkeit'], $array_eintrag['territoriumszugegoerigkeit'], "territoriumszugegoerigkeit", $arr_territoriumszugegoerigkeit, "", "", "");

		gendatnoedit($this->def,$spr['erstellungsdatum'],$array_eintrag['erstellungsdatum']);
		gendatnoedit($this->def,$spr['letzte_aktualisierung'],$array_eintrag['bearbeitungsdatum']);

		$arr_bearbeitungstiefe=$this->bearbeitungstiefen();
		genselectitem($this->def, $spr['bearbeitungstiefe'], $array_eintrag['bearbeitungstiefe'], "bearbeitungstiefe", $arr_bearbeitungstiefe, "", "", "");
	}
	function g_info() {
		global $spr;
		$array_eintrag = $this->formData;
		$this->g_basic_info();
		genstempel1($this->def, $spr['bibliografie'].': '.$spr['fertig_gestellt'],'biografie',$array_eintrag);
		genstempel1($this->def, $spr['ausstellungen'].': '.$spr['fertig_gestellt'],'ausstellungen',$array_eintrag);
		genstempel1($this->def, $spr['auszeichnungen_stipendien'].': ','auszeichnungen_stipendien',$array_eintrag);
		genstempel1($this->def, $spr['bestaende2'].': '.$spr['fertig_gestellt'],'bestaende',$array_eintrag);
		genstempel1b($this->def, $spr['interview_vorgesehen'],'interview_vorgesehen',$array_eintrag);
		genstempel1($this->def, $spr['interview_fertiggestellt'],'interview_fertiggestellt',$array_eintrag);
	}
	
	function g_history() {
		genformitem($this->def,'edittext','Aenderungsverfolgung',$this->formData['history'],'history_b');
		$this->endPara();
	}
	
	function g_vita() {
		global $spr;
		$this->title($spr['vita']);
		$array_eintrag = $this->formData;
		
		
		
		genstempel1b($this->def, $spr['pnd_vorgesehen'],'pnd_vorgesehen',$array_eintrag);
		genstempel1($this->def, $spr['pnd_erstellt'],'pnd_erstellt',$array_eintrag);
		gennoedit($this->def, $spr['originalsprache'],$array_eintrag['originalsprache']);
		genstempel2($this->def, $spr['werdegang'],'werdegang',$array_eintrag,-1);
		genstempel2($this->def, $spr['schaffensbeschrieb'],'schaffensbeschrieb',$array_eintrag,-1);
		$this->endPara();		
	}
	
	function g_doku() {
		global $spr;
		$this->title($spr['dokumentation']);
		$array_eintrag = $this->formData;

		$arr_dokumentation=array('Haengemappen'=>'H&auml;ngemappen','Archivschachteln'=>'Archivschachteln','Elektronisch'=>'Elektronisch'); //Array füllen für Select
		$set= $array_eintrag['dokumentation'];
		$array_set = explode (",", $set);
		gencheckarrayitemKv($this->def, $spr['dokumentation'], $arr_dokumentation, "dokumentation[]",$array_set);

		genformitem($this->def,'textfield','Dokumentation_Beschreibung',$array_eintrag['dokumentation_text'],'dokumentation_text');
		genstempel1($this->def, 'dokumentation_erfasst','dokumentation',$array_eintrag);

		genformitem($this->def,'edittext',$spr['notiz'],$array_eintrag['notiz_fiche'],'notiz_fiche');
		
		$this->endPara();
	}
	
	function out() {
		$this->def->parse("bearbeiten.speichern");
		$this->def->parse("bearbeiten");
		return $this->def->text("bearbeiten");
	}
	
	function g_todo() { global $spr;
		$this->title( $spr['vorgeseheneArbeiten'] );
		$this->endPara();
	}
	
	function g_update() { global $spr;
		$this->title( $spr['aktualisierung'].' '.$spr['bestaende2'] );
		$array_eintrag = $this->formData;
		genstempel1b($this->def, $spr['vorgesehen'],'bestaende_vorgesehen',$array_eintrag);
		genstempel1($this->def, $spr['versand'],'bestaende_versand',$array_eintrag);
		genstempel1($this->def, $spr['antwortinstitution'],'bestaende_antwortinstitution',$array_eintrag);
		genstempel1($this->def, $spr['aktualisierungerstellt'],'bestaende_aktualisierungerstellt',$array_eintrag);
		genstempel1($this->def, $spr['okinstitution'],'bestaende_okinstitution',$array_eintrag);
		genstempel1($this->def, $spr['publizieren'],'bestaende_publizieren',$array_eintrag);
		//genstempel_multi($this->def, $spr['aktualisierung'],'aktualisierung',$this->formData);
		$this->endPara();
		$this->title( $spr['aktualisierung']);
		genstempel1b($this->def, $spr['interview'] .' '.$spr['vorgesehen'],'interview_vorgesehen',$array_eintrag);
		genstempel1($this->def, $spr['interview'] .' '.$spr['erstellt'],'interview_erstellt',$array_eintrag);

		genstempel2($this->def, $spr['sammlungsgeschichte'],'sammlungsgeschichte',$array_eintrag,-1);
		genstempel2($this->def, $spr['sammlungsbeschreibung'],'sammlungsbeschreibung',$array_eintrag,-1);
		
		genstempel1b($this->def, $spr['bibliografie'].' '.$spr['vorgesehen'],'bibliografie_vorgesehen',$array_eintrag);
		genstempel1($this->def, $spr['bibliografie'].' '.$spr['erstellt'],'bibliografie_erstellt',$array_eintrag);

		genstempel1b($this->def, $spr['ausstellungen'].' '.$spr['vorgesehen'],'ausstellungen_vorgesehen',$array_eintrag);
		genstempel1($this->def, $spr['ausstellungen'].' '.$spr['erstellt'],'ausstellungen_erstellt',$array_eintrag);
		
		
		$this->endPara();
	}
	
	function g_transl() { global $spr;
		$this->title( $spr['uebersetzung'] );
		$array_eintrag = $this->formData;
		genstempel2($this->def, $spr['uebersetzung'].' de','uebersetzung_de',$array_eintrag,-1);
		genstempel2($this->def, $spr['uebersetzung'].' fr','uebersetzung_fr',$array_eintrag,-1);
		genstempel2($this->def, $spr['uebersetzung'].' it','uebersetzung_it',$array_eintrag,-1);
		genstempel2($this->def, $spr['uebersetzung'].' rm','uebersetzung_rm',$array_eintrag,-1);
		genstempel2($this->def, $spr['uebersetzung'].' en','uebersetzung_en',$array_eintrag,-1);
		$this->endPara();
	}
	
}
?>