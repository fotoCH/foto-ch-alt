<?php

/* 
Exit if script can't find the yaz module
*/
if (!extension_loaded('yaz')) {
	throw( new JsonRpcError( 1, "no yzlib" ) );
       print "Sorry, 'yaz.so' isn't loaded....";
       exit;
}


function queryIsbn( $isbn )
{
$config_settings = array(

/* marc: titel, untertitel, anmerkungen, autorin, jahr, seitenzahl, isbn, verlag */

array(  'title' => 'gbv Ã¶vk & gvk',
	'yaz_connect_string' => 'z3950.gbv.de:20012/goevk',
	'yaz_connect_options' => array('user'=>'999','password'=>'abc'),
	'yaz_record_syntax' => 'MARC21',
	'marc' => array( array( '245', '440' ), array( '/^(.+)([\/|:]{1}.*$)/', '/^(.+):/' ),
			 array( '245b', '245', '440' ), array( '/(.+)\/$/', '/[\/|:]{1}(.+)$/', '/:[ ]*([^\/]+)/' ),
			 array(  '650'), array(  '' ),
			 array( '100', '245c', '245c', '700', '710'), array( '', '/(.+)\.$/', '/(.+)\.$/', '', '', '' ),
			 '260c', '/([0-9]+)/', 
			 '300', '/(^[0-9]+)|[^\[]{1}([0-9]+)[^\]]{1}/',
			 array( '003', '020' ), array( '/([0-9\-X]+)/', '/([0-9\-X]+)/' ),
	                 '260b', '', ),
),	
/*array('title' => 'SFU Library Catalogue',
	'yaz_connect_string' => 'troy.lib.sfu.ca:210/innopac',
	'yaz_connect_options' => '',
	'yaz_record_syntax' => 'opac',
),
array(  'title' => 'U of T Library Catalogue',
	'yaz_connect_string' => 'sirsi.library.utoronto.ca:2200/UNICORN',
	'yaz_connect_options' => '',
	'yaz_record_syntax' => 'marc21',
),
array(  'title' => 'U Vic  Library Catalogue',
	'yaz_connect_string' => 'voyager.library.uvic.ca:7090/voyager',
	'yaz_connect_options' => '',
	'yaz_record_syntax' => 'marc21',
),
array(  'title' => 'US Library of Congress',
	'yaz_connect_string' => 'z3950.loc.gov:7090/voyager',
	'yaz_connect_options' => '',
	'yaz_record_syntax' => 'marc21',
),*/
array(  'title' => 'swb pollux',
	'yaz_connect_string' => '193.197.31.30:210/swblite',
	'yaz_connect_options' => '',
	'yaz_record_syntax' => 'UNIMARC',
),	
);


$isbn = mb_ereg_replace('-', '', $isbn);

$res;
foreach( $config_settings as $config ) {
	//print "suche auf ".$config['title']."<br>";
	$id = yaz_connect($config['yaz_connect_string'],$config['yaz_connect_options']);
	yaz_syntax($id, $config['yaz_record_syntax']);

	$res = opac_find($id, $isbn);
	if( $res )
	{
		//print "found :<br>";
		//foreach( $res as $t => $v )
		//	print "$t (".$config['marc'][$t]."): $v<br>";
		yaz_close( $id );
		$ret = array();
		$record = 'Gefunden in: '.$config['title'].'<br>Ganzer Record:<br>';
		foreach( $res as $tag => $cont )
			$record .= "$tag: $cont <br>";
		$ret[] = $record;
//                $ret[0] = bin2hex( $res[245] );
		if( $config['marc'] ) for( $i = 0; $i+1 < count( $config['marc'] ); $i += 2  )
		{ 
			$tag = $config['marc'][$i];
			$preg = $config['marc'][$i+1];
			if( is_array( $tag ) ) {
				$found = false;
				for( $j = 0; $j < count( $tag ); $j++ )
				{
					if( $tag[$j] != '' && isset( $res[$tag[$j]] ) ) {
                                		if( $preg[$j] != '' ) {
                                        		$hits = array();
                                        		preg_match($preg[$j], $res[$tag[$j]], $hits);
                                            if( !isset($hits[1]) && !isset($hits[2]) )
                                                         continue;
                                            $ret[] = isset($hits[1]) ?  $hits[1] :  $hits[2];
							$found = true;
							break;
						} else {
							$ret[] = $res[$tag[$j]];
							$found = true;
                                                        break;
						}
					}
				}
				if( !$found )
					$ret[] =  '';
			}
			else {
				if( $tag != '' && isset( $res[$tag] ) ) {
					if( $preg != '' ) {
						$hits = array();
						preg_match($preg, $res[$tag], $hits);
						$ret[] = $hits[1]!='' ?  $hits[1] :  $hits[2];
					} else
						$ret[] =  $res[$tag];
				} else
					$ret[] = '';
			}
		}
		return $ret;
		//break;
	}
	yaz_close( $id );
	return array();
}

}

function opac_find ($id, $keywords) {
   /*
   Use
   1=4 : title
   1=21 : subject
   1=1003 : author
   1=1007 : identifier  

   Relation
   2=3 : equal

   Position
   3=1 : first in field
   3=3 : any position

   Structure
   4=1 : phrase
   4=2 : word
   4=101 : normalized

   Trunction
   5=1 : right truncate
   5=100 : do not truncate
   */
 
   // Queries look like '@and @attr 1=4 putting @attr 1=4 content ';
   $query = '@attr 4=1 @attr 1=7 "' . $keywords . '*"'; 

   // $sort_criteria = '1=' . $field . ' ia';
   // yaz_sort($id, $sort_criteria);
   yaz_search($id, 'rpn', $query);
   yaz_wait();
   $error = yaz_error($id);
   if (!empty($error)) {
     die( "<p>Error: $error</p><br>" );
     //return false;
   } else {
     
     $hits = yaz_hits($id);
     if ($hits == '0') {
	return false;
     }
   }
   //yaz_present($id);
   
   $recs = '<?xml version="1.0"?>' . "\n" ."<root>\n";
   $rec = yaz_record($id, 1, "xml"); 
   $rec = preg_replace("/xmlns=\"http:.*\"/", '', $rec);
   $recs .= $rec."\n</root>\n";

   $dom = new DomDocument;
   $dom->preserveWhiteSpace = FALSE;
   $dom->loadXML($recs);
   //print mb_ereg_replace("\n",'<br>',htmlentities($dom->saveXML()));

   $letters = array( '', 'b', 'c', 'd', 'f', 'd', 'e' );
   $bad = array( "U\xcc\x88", "O\xcc\x88", "A\xcc\x88", "u\xcc\x88", "o\xcc\x88", "a\xcc\x88", );
   $replace = array( "Ãœ", "Ã–", "Ã„", "Ã¼", "Ã¶", "Ã¤", );

   $ret = array();
   $elements = $dom->getElementsByTagName('datafield');
   foreach( $elements as $it )
   {
	$value = array();
	$fields = $it->getElementsByTagName('subfield');
	for( $i = 0; $i < $fields->length; $i++ )
        {
                $val = $fields->item($i)->nodeValue;
		$val = str_replace( $bad, $replace, $val);
		$ret[$it->getAttribute( 'tag' ).$letters[$i]] = $val;
        }
   }
   return $ret;
}

?> 