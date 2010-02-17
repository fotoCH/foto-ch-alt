<?php

	if(empty($_SESSION)){
		$_SESSION['lastactions'] = array();
		$_SESSION['url'] = array();		
	}

	if($_GET['id']==''){			
		$_SESSION['lastactions'] = array();
		$_SESSION['url'] = array();		
		
		//echo "<h1>id leer</h1>";
		if($_GET['anf']!="" || $_GET['submitbutton']!=""){ //-> suche und trefferliste von institution bzw. fotograph			
			$_SESSION['lastactions'][0] = "suche";
			$_SESSION['lastactions'][1] = "trefferliste";
			$_SESSION['url'][0][0] = "suche"; //value
			$_SESSION['url'][0][1] = "a=$action"; //href
			$_SESSION['url'][1][0] = "trefferliste"; //value
			$_SESSION['url'][1][1] = GETasString(); //href
			//store the actions
		}
		//-> nur suche
		else {
			$_SESSION['lastactions'][0] = "suche";
			$_SESSION['url'][0][0] = "suche"; //value
			$_SESSION['url'][0][1] = "a=$action"; //href
		}
	}	
	else { //id is set -> detailansicht von fotograph, institution, bestand
		//-> fotograph, bestand, institution
		if(!in_array($action,$_SESSION['lastactions'])){
			$_SESSION['url'][sizeof($_SESSION['lastactions'])][0] = $action; //value
			$_SESSION['url'][sizeof($_SESSION['lastactions'])][1] = GETasString(); //href
			$_SESSION['lastactions'][sizeof($_SESSION['lastactions'])] = $action;
		}
		//in array
		else {
			//top position in array?
			if($action == $_SESSION['lastactions'][sizeof($_SESSION['lastactions'])-1]){			
				$_SESSION['url'][sizeof($_SESSION['lastactions'])-1][0] = $action; //value
				$_SESSION['url'][sizeof($_SESSION['lastactions'])-1][1] = GETasString(); //href
				$_SESSION['lastactions'][sizeof($_SESSION['lastactions'])-1] = $action;
			}
			//somewhere else in the array
			else {
				$key = array_keys($_SESSION['lastactions'] ,$action);
				$templastactions = array();
				$templasturlactions = array();
				//$templasturlactions = array();
				for($i = 0 ; $i <= $key[0]; $i++){
					$templastactions[$i] = $_SESSION['lastactions'][$i];
					$templasturlactions[$i][0] = $_SESSION['url'][$i][0];
					$templasturlactions[$i][1] = $_SESSION['url'][$i][1];
				}
				$_SESSION['lastactions']  = array();
				$_SESSION['lastactions']  = $templastactions;
				$_SESSION['url'] = array();
				$_SESSION['url'] = $templasturlactions;
			}
		}
	}
	//assign actions to template
	$navhist = new XTemplate("./templates/navhistory.xtpl");
	$navhist->assign("LANG",$language);
		
	//parse the $_SESSION['lastactions'][$i] actions...
	for($i = 0 ; $i < sizeof($_SESSION['lastactions']); $i++){		
		$value = $spr[$_SESSION['url'][$i][0]];
		$navhist->assign("VALUE",$value);
		$navhist->assign("HREF", $_SESSION['url'][$i][1]);
		if($i != 0) $navhist->assign("RARR", "&nbsp; &rarr; &nbsp; ");
		$navhist->parse("navhistory.".$_SESSION['url'][$i][0]);
		$res.= $navhist->text("navhistory.".$_SESSION['url'][$i][0]);
	}
	//$navhist->parse("navhistory");
	$navigationhistory.=$res;	
?>