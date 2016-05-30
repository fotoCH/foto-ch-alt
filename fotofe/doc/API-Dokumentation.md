# API Dokumentation
Dieses Dokument beschreibt die API, welche für fotoCH entwickelt wurde und öffentlich
genutzt werden kann.

Diese API wird vom neuen AngularJS basierten Frontend benutzt:
 [https://de.foto-ch.ch/](https://de.foto-ch.ch/)

Zu inhaltlichen Aspekten verweisen wir auf das [foto-ch-Handbuch](http://foto-ch.ch/?a=hilfe&lang=de).

Die API wird ständig weiterentwickelt. Künftige Änderungungen sollten mit dieser Version kompatibel sein.
Nötige Anpassungen und Fehlerbehebungen können aber vorkommen. 

## Allgemeine Infos
Alle API Aufrufe in diesem Dokument sind relativ zu **API-URL: [https://de.foto-ch.ch/api/](https://de.foto-ch.ch/api/)**. Die Daten werden im JSON Format zurückgegeben.

## Sprache 
Mit dem Parameter lang kann angegeben werden, in welcher Sprache der Content gewünscht wird

## Entitäten
Folgende Objekttypen können als Liste oder Einzelsatz abgerufen werden:
* Fotografen: photographer
dies ist die default-Entität und muss nicht angegeben werden.

* Institutionen: institution
* Bestände: inventory
* Fotos: foto
* Ausstellungen: exhibition
* Literatur: literature
* Arbeitsorte: orte
* Arbeitsperioden: perioden

- **Aufruf einer Liste:**   (api/?a=entity[parameters])

- **Aufruf eines Einzelsatzes:**   (api/?a=entity&id=123[parameters])

## Attribute

- Die Attributte sind aus den Beispielen erkennbar. Sie sind zum Teil Arrays. Gewisse Attribute liegen in verschieden formatierten Varianten vor. Einige bezeichnen css-Klassen, die zur Formatierung verwendet werden können.

### Liste aller Fotografen
- **Beschreibung:** Gibt eine Liste aller Fotografen zurück.
- **Aufruf:** [/?a=photographer](https://de.foto-ch.ch/api/?a=photographer)

**Beispiel-Resultat (erste 2 Einträge)**
```json
{
	"res": [
      {  
         "bioclass":"subtitle3",
         "fgeburtsdatum":"",
         "fldatum":"*",
         "fotografengattungen":"",
         "bildgattungen":"",
         "kanton":"FR,VD",
         "nachname":"A. Barras Photo-Cin\u00e9-Projection",
         "vorname":"",
         "namenszusatz":"",
         "id":"24019",
         "bearbeitungsdatum":"26.8.2011",
         "arbeitsperioden":"Lausanne VD"
      },
      {  
         "bioclass":"subtitle3bio",
         "fgeburtsdatum":"26.03.1910",
         "fldatum":"26.03.1910 - 07.04.1971",
         "fotografengattungen":"Fotograf,Fotoreporter,Agentur",
         "bildgattungen":"Personen,Reportage,Presse,Portr\u00e4t,Sport,Alltag",
         "kanton":"ZH",
         "nachname":"A. T. P. Bilderdienst",
         "vorname":"",
         "namenszusatz":"",
         "id":"21310",
         "bearbeitungsdatum":"7.3.2016",
         "arbeitsperioden":"Z\u00fcrich ZH"
      }
	]
}
```

#### Parameter
- **Nach Anfangsbuchstaben E filtern:** [/?a=photographer&anf=E](https://de.foto-ch.ch/api/?a=photographer&anf=E)

### Detaildaten eines Fotografen
- **Beschreibung:** Gibt ein Liste mit Detailinfos eines Fotografen zurück
- **Aufruf:** [/?a=photographer&id=1068](https://de.foto-ch.ch/api/?a=photographer&id=1068)

**Beispiel-Resultat für Fotografen-ID 1068**
```json
{  
   "umfeld_s":{  
      "de":"Vorg\u00e4nger von <a href=\"#\/photographer\/detail?id=20628\">August  Monbaron<\/a>.<br \/>Gegr\u00fcndet von \r\n<a href=\"#\/photographer\/detail?id=1067\">Charles Joseph  Bruder<\/a>.<br \/>Zusammenarbeit mit <a href=\"#\/photographer\/detail?id=1429\">William  Moritz<\/a>.",
      "fr":"Pr\u00e9d\u00e9cesseur de <a href=\"#\/photographer\/detail?id=20628\">August  Monbaron<\/a>.<br \/>\r\nFond\u00e9 par <a href=\"#\/photographer\/detail?id=1067\">Charles Joseph  Bruder<\/a>.<br \/>\r\nCollaboration avec <a href=\"#\/photographer\/detail?id=1429\">William  Moritz<\/a>."
   },
   "werdegang_s":{  
      "de":"Die aus Neuenburg stammenden Gebr\u00fcder oder Fr\u00e8res \r\nBruder waren f\u00fcr die Jahre nach 1850 ein \r\nausserordentlich aktives Fotografengespann. Ihre \r\nT\u00e4tigkeit ist f\u00fcr verschiedene Orte im Kanton Bern, aber \r\nauch ausserhalb belegt. 1852 ging ihre Reise von Solothurn (Juni) \u00fcber\r\nBurgdorf (Juli) und Langnau (August) nach Bern, wo sie \r\nsich mehrere Monate bis in den M\u00e4rz 1853 hinein \r\naufhielten. Im Juli 1853 hielten sie sich f\u00fcr zwei Wochen in Biel auf, Ende September bis Mitte Oktober waren sie erneut in Solothurn t\u00e4tig und kehrten dann nach Bern zur\u00fcck, wo sie bis Ende Jahr blieben. Ihre Aktivit\u00e4t in Aarau ist auf \r\nGrund einer einzelnen erhaltenen Ortsbildaufnahme von \r\n1852 belegt. Gleichzeitig mit dem Aufkommen der Cartes \r\nde visite und der Konsolidierung des Fotografenberufs \r\nbeschr\u00e4nkten die Gebr\u00fcder Bruder ihr Arbeitsfeld auf \r\nNeuenburg. "
   },
   "schaffensbeschrieb_s":{  
      "de":"Die Gebr\u00fcder Bruder waren Fotografen von hohem \r\nhandwerklichem und gestalterischem K\u00f6nnen. Sie boten \r\ns\u00e4mtliche zu ihrer Zeit verf\u00fcgbaren Techniken an und \r\narbeiteten f\u00fcr die Kolorierung mit dem Neuenburger Maler \r\nund Stecher William Moritz zusammen. Wie ihren \r\nInseraten zu entnehmen ist, hatte dieser wesentlichen \r\nAnteil an der Qualit\u00e4t der Aufnahmen. Die von ihm \r\nveredelten Daguerreotypien, Papierabz\u00fcge, Ambrotypien \r\nund  Pannotypien w\u00fcrden den sch\u00f6nsten Aquarell- und \r\nMiniaturbildern auf Elfenbein gleichen. Die erhaltenen \r\nFotografien der Gebr\u00fcder Bruder best\u00e4tigen diese \r\nAussage. Die farbig oder als Grisaillen veredelten Bl\u00e4tter \r\nsind von hervorragender Qualit\u00e4t und oft schwierig von \r\naquarellierten Darstellungen zu unterscheiden."
   },
   "beruf_s":null,
   "namen":[  
      {  
         "titel":"",
         "nachname":"Bruder",
         "vorname":"Gebr\u00fcder",
         "namenszusatz":"",
         "idf":null
      },
      {  
         "titel":"",
         "nachname":"Bruder",
         "vorname":"Fr\u00e8res",
         "namenszusatz":"",
         "idf":null
      }
   ],
   "originalsprache":"de",
   "sprachanzeige":"Originalsprache: de&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;Artikel vorhanden in: <a href=\".\/?a=fotograph&amp;id=1068&amp;lang=de&amp;clang=fr\">fr<\/a>&nbsp;",
   "id":"",
   "pnd":null,
   "pnd_status":"99",
   "fbearbeitungsdatum":"15.2.2016",
   "fldatum":"",
   "fumfeld":"Vorg\u00e4nger von <a href=\"#\/photographer\/detail?id=20628\">August  Monbaron<\/a>.<br \/>Gegr\u00fcndet von \r\n<a href=\"#\/photographer\/detail?id=1067\">Charles Joseph  Bruder<\/a>.<br \/>Zusammenarbeit mit <a href=\"#\/photographer\/detail?id=1429\">William  Moritz<\/a>.",
   "fotografengattungen_set":"Atelierfotograf, Wanderfotograf, Firma",
   "bildgattungen_set":"Personen, Portr\u00e4t, Ortsbild, Ethnologie",
   "heimatort":"",
   "beruf":"",
   "fotografengattungen":"Atelierfotograf, Wanderfotograf, Firma",
   "bildgattungen":"Personen, Portr\u00e4t, Ortsbild, Ethnologie",
   "arbeitsperioden":[  
      {  
         "arbeitsort":"Aarau AG",
         "um_vonf":"",
         "von":"1852",
         "um_bisf":"",
         "bis":""
      },
      {  
         "arbeitsort":"Solothurn SO",
         "um_vonf":"",
         "von":"1852",
         "um_bisf":"",
         "bis":""
      },
      {  
         "arbeitsort":"Burgdorf BE",
         "um_vonf":"",
         "von":"1852",
         "um_bisf":"",
         "bis":""
      },
      {  
         "arbeitsort":"Langnau BE",
         "um_vonf":"",
         "von":"1852",
         "um_bisf":"",
         "bis":""
      },
      {  
         "arbeitsort":"Bern BE",
         "um_vonf":"",
         "von":"1852",
         "um_bisf":"",
         "bis":"1853"
      },
      {  
         "arbeitsort":"Luzern LU",
         "um_vonf":"",
         "von":"1852",
         "um_bisf":"",
         "bis":""
      },
      {  
         "arbeitsort":"Biel BE",
         "um_vonf":"",
         "von":"1853",
         "um_bisf":"",
         "bis":""
      },
      {  
         "arbeitsort":"Solothurn SO",
         "um_vonf":"",
         "von":"1853",
         "um_bisf":"",
         "bis":""
      },
      {  
         "arbeitsort":"La Neuveville BE",
         "um_vonf":"",
         "von":"1862",
         "um_bisf":"",
         "bis":""
      },
      {  
         "arbeitsort":"Neuch\u00e2tel NE",
         "um_vonf":"um ",
         "von":"1865",
         "um_bisf":"",
         "bis":""
      },
      {  
         "arbeitsort":"La Chaux-de-Fonds NE",
         "um_vonf":"",
         "von":"",
         "um_bisf":"",
         "bis":""
      }
   ],
   "umfeld":"Vorg\u00e4nger von <a href=\"#\/photographer\/detail?id=20628\">August  Monbaron<\/a>.<br \/>Gegr\u00fcndet von \r\n<a href=\"#\/photographer\/detail?id=1067\">Charles Joseph  Bruder<\/a>.<br \/>Zusammenarbeit mit <a href=\"#\/photographer\/detail?id=1429\">William  Moritz<\/a>.",
   "werdegang":"Die aus Neuenburg stammenden Gebr\u00fcder oder Fr\u00e8res \r\nBruder waren f\u00fcr die Jahre nach 1850 ein \r\nausserordentlich aktives Fotografengespann. Ihre \r\nT\u00e4tigkeit ist f\u00fcr verschiedene Orte im Kanton Bern, aber \r\nauch ausserhalb belegt. 1852 ging ihre Reise von Solothurn (Juni) \u00fcber\r\nBurgdorf (Juli) und Langnau (August) nach Bern, wo sie \r\nsich mehrere Monate bis in den M\u00e4rz 1853 hinein \r\naufhielten. Im Juli 1853 hielten sie sich f\u00fcr zwei Wochen in Biel auf, Ende September bis Mitte Oktober waren sie erneut in Solothurn t\u00e4tig und kehrten dann nach Bern zur\u00fcck, wo sie bis Ende Jahr blieben. Ihre Aktivit\u00e4t in Aarau ist auf \r\nGrund einer einzelnen erhaltenen Ortsbildaufnahme von \r\n1852 belegt. Gleichzeitig mit dem Aufkommen der Cartes \r\nde visite und der Konsolidierung des Fotografenberufs \r\nbeschr\u00e4nkten die Gebr\u00fcder Bruder ihr Arbeitsfeld auf \r\nNeuenburg.",
   "schaffensbeschrieb":"Die Gebr\u00fcder Bruder waren Fotografen von hohem \r\nhandwerklichem und gestalterischem K\u00f6nnen. Sie boten \r\ns\u00e4mtliche zu ihrer Zeit verf\u00fcgbaren Techniken an und \r\narbeiteten f\u00fcr die Kolorierung mit dem Neuenburger Maler \r\nund Stecher William Moritz zusammen. Wie ihren \r\nInseraten zu entnehmen ist, hatte dieser wesentlichen \r\nAnteil an der Qualit\u00e4t der Aufnahmen. Die von ihm \r\nveredelten Daguerreotypien, Papierabz\u00fcge, Ambrotypien \r\nund  Pannotypien w\u00fcrden den sch\u00f6nsten Aquarell- und \r\nMiniaturbildern auf Elfenbein gleichen. Die erhaltenen \r\nFotografien der Gebr\u00fcder Bruder best\u00e4tigen diese \r\nAussage. Die farbig oder als Grisaillen veredelten Bl\u00e4tter \r\nsind von hervorragender Qualit\u00e4t und oft schwierig von \r\naquarellierten Darstellungen zu unterscheiden.",
   "auszeichnungen_und_stipendien":"",
   "bestaende":[  
      {  
         "id":"1531",
         "inst_id":"101",
         "gi":null,
         "name":"Sammlung Rittersaalverein Burgdorf im Schlossmuseum",
         "inst_name":"Schlossmuseum Burgdorf",
         "zeitraum":"um 1840 - 2012",
         "Bestand":"Best\u00e4nde (Name, Zeitraum)"
      },
      {  
         "id":"1031",
         "inst_id":"145",
         "gi":null,
         "name":"Collection Georges Montandon",
         "inst_name":"Ch\u00e2teau et mus\u00e9e de Valangin",
         "zeitraum":"1900 - 1940",
         "Bestand":""
      },
      {  
         "id":"931",
         "inst_id":"140",
         "gi":null,
         "name":"Fonds courant de photographies",
         "inst_name":"Biblioth\u00e8que de la Ville de La Chaux-de-Fonds, D\u00e9partement audiovisuel",
         "zeitraum":"D\u00e8s 1870",
         "Bestand":""
      },
      {  
         "id":"1042",
         "inst_id":"190",
         "gi":null,
         "name":"Fotos: Portraits, Pers\u00f6nlichkeiten, Gruppenfotos",
         "inst_name":"Staatsarchiv des Kantons Nidwalden",
         "zeitraum":"ca. 1850 \u2013 2007",
         "Bestand":""
      },
      {  
         "id":"820",
         "inst_id":"25",
         "gi":null,
         "name":"Historische Fotografie",
         "inst_name":"Schweizerisches Nationalmuseum",
         "zeitraum":"1839 bis heute",
         "Bestand":""
      },
      {  
         "id":"1038",
         "inst_id":"227",
         "gi":null,
         "name":"Iconographie locale Neuch\u00e2tel",
         "inst_name":"Mus\u00e9e d'art et d'histoire de la Ville de Neuch\u00e2tel",
         "zeitraum":"1865 - 1994",
         "Bestand":""
      },
      {  
         "id":"1039",
         "inst_id":"227",
         "gi":null,
         "name":"Photographies de la Collection Bickel",
         "inst_name":"Mus\u00e9e d'art et d'histoire de la Ville de Neuch\u00e2tel",
         "zeitraum":"1860 environ - 1940, 1970",
         "Bestand":""
      },
      {  
         "id":"715",
         "inst_id":"145",
         "gi":null,
         "name":"Photographies provenant de divers fonds",
         "inst_name":"Ch\u00e2teau et mus\u00e9e de Valangin",
         "zeitraum":"1840 - 1990",
         "Bestand":""
      },
      {  
         "id":"144",
         "inst_id":"25",
         "gi":null,
         "name":"Sammlung Herzog (Allg. Sammlung)",
         "inst_name":"Schweizerisches Nationalmuseum",
         "zeitraum":"19. und 20. Jahrhundert",
         "Bestand":""
      }
   ],
   "primaerliteratur":null,
   "sekundaerliteratur":[  
      {  
         "id":"1757",
         "text":"100 ans de photographie. J. Girod, Bruder Fr\u00e8res, J. Deppeler, E. Chiffelle, J. Rossi, V. Berstecher, A. Acquadro, in: Intervalles, Revue culturelle du Jura bernois et Bienne, 2008, Nr. 80, printemps."
      },
      {  
         "id":"47",
         "text":"Bourquin, Werner: 50 Jahre Perrot AG Biel, Biel 1955."
      },
      {  
         "id":"46",
         "text":"Bourquin, Marcus u. Werner: Biel. Stadtgeschichtliches Lexikon, Biel 1999."
      },
      {  
         "id":"406",
         "text":"Fasnacht, Peter: \u00abZeiten vergehen, das Bild bleibt bestehen...\u00bb. Auf Spurensuche in der Bieler Fotografie-Geschichte: Portr\u00e4tfotografinnen und -fotografen und ihre Ateliers, in: Bieler Jahrbuch 2005, Biel 2006, S. 20-46."
      },
      {  
         "id":"692",
         "text":"Keckeis, Peter (Hg.): Damals in der Schweiz. Kultur, Geschichte, Volksleben der Schweiz im Spiegel der fr\u00fchen Photographie, Frauenfeld, Huber 1981."
      },
      {  
         "id":"2385",
         "text":"M\u00fcller, A.: Biedermeierisches Luzern. Die ersten Photographen, in: Heimatland, illustrierte Monatsbeilage des Vaterland, 1948 , Nr. 8, S. 58 - 60."
      },
      {  
         "id":"126",
         "text":"Perret, Ren\u00e9: Frappante \u00c4hnlichkeit. Pioniere der Schweizer Photographie. Bilder der Anf\u00e4nge, Brugg 1991."
      },
      {  
         "id":"199",
         "text":"Sch\u00fcrpf, Markus: Fotografie im Emmental. Idyll und Realit\u00e4t, Bern, Kunstmuseum Bern 2000."
      },
      {  
         "id":"201",
         "text":"Sch\u00fcrpf, Markus: Fr\u00fche Fotografie in Burgdorf, 1839-1875, Burgdorf, H.-U. Haldemann 2001."
      },
      {  
         "id":"214",
         "text":"Schweizerische Ethnologische Gesellschaft (Hg.): L'objectif subjectif. Collections de photographies ethno-historiques en Suisse. Das subjektive Objektiv. Sammlungen historisch-ethnographischer Photographien in der Schweiz, Bern, SEG 1997."
      },
      {  
         "id":"222",
         "text":"Schweizerisches Landesmuseum (Hg.): Im Licht der Dunkelkammer. Die Schweiz in Photographien des 19. Jahrhunderts aus der Sammlung Herzog, Basel 1994."
      }
   ],
   "einzelausstellungen":null,
   "gruppenausstellungen":[  
      {  
         "id":"371",
         "text":"1994, Z\u00fcrich, Schweizerisches Landesmuseum, Im Licht der Dunkelkammer. Die Schweiz in Photographien des 19. Jahrhunderts aus der Sammlung Herzog",
         "titel":"Im Licht der Dunkelkammer. Die Schweiz in Photographien des 19. Jahrhunderts aus der Sammlung Herzog",
         "ort":"Z\u00fcrich",
         "jahr":"1994",
         "institution":"Schweizerisches Landesmuseum"
      },
      {  
         "id":"255",
         "text":"2000, Bern, Kunstmuseum, Fotografie im Emmental. Idyll und Realit\u00e4t",
         "titel":"Fotografie im Emmental. Idyll und Realit\u00e4t",
         "ort":"Bern",
         "jahr":"2000",
         "institution":"Kunstmuseum"
      },
      {  
         "id":"274",
         "text":"2001, Burgdorf, Rathaus, Fr\u00fche Fotografie in Burgdorf. 1839-1875. Carl Daut, Carl Durheim, Hugo Kopp, Arnold Meyer, Franz Xaver R\u00fchl u.a",
         "titel":"Fr\u00fche Fotografie in Burgdorf. 1839-1875. Carl Daut, Carl Durheim, Hugo Kopp, Arnold Meyer, Franz Xaver R\u00fchl u.a",
         "ort":"Burgdorf",
         "jahr":"2001",
         "institution":"Rathaus"
      },
      {  
         "id":"650",
         "text":"2008, La Neuveville, Mus\u00e9e d'Art et d'Histoire, 100 ans de photographie. A. Acquadro, J. Girod, Bruder Fr\u00e9res, Deppeler, E. Chiffelle, Jean Rossi, Victor Beerstecher",
         "titel":"100 ans de photographie. A. Acquadro, J. Girod, Bruder Fr\u00e9res, Deppeler, E. Chiffelle, Jean Rossi, Victor Beerstecher",
         "ort":"La Neuveville",
         "jahr":"2008",
         "institution":"Mus\u00e9e d'Art et d'Histoire"
      }
   ],
   "autorIn":"Markus Sch\u00fcrpf",
   "bearbeitungsdatum":"15.2.2016",
   "photos":null
}
```



### Aktualisierte Fotografen
- **Beschreibung:** Gibt eine Liste von Fotografen zurück, welche zuletzt bearbeitet wurde.
- **Aufruf:** [/?recent=10](https://de.foto-ch.ch/api/?recent=10)

**Beispiel-Resultat**
```json
{
	"res": [
		{
			"bioclass": "subtitle3",
			"fgeburtsdatum": "1926",
			"fldatum": "1926",
			"nachname": "Wolf",
			"vorname": "Kurt",
			"namenszusatz": "",
			"id": "21429"
		},
		{
			"bioclass": "subtitle3bio",
			"fgeburtsdatum": "14.05.1892",
			"fldatum": "14.05.1892 - ",
			"nachname": "Photographische Gesellschaft Bern",
			"vorname": "",
			"namenszusatz": "",
			"id": "1724"
		}
	]
}
```

## Institutionen
### Liste aller Institutionen
- **Beschreibung:** Gibt eine Liste aller Institutionen zurück.
- **Aufruf:** [/?a=institution](https://de.foto-ch.ch/api/?a=institution)

**Beispiel-Resultat (erste 2 Einträge)**
```json
{
	"res": [
		{
			"nameclass": null,
			"name": "Abbaye de Saint-Maurice, Monastère",
			"ort": "Saint-Maurice",
			"abkuerzung": "",
			"id": "129"
		},
		{
			"nameclass": null,
			"name": "Abegg-Stiftung",
			"ort": "Riggisberg",
			"abkuerzung": "",
			"id": "87"
		}
	]
}
```

#### Parameter
- **Nach Anfangsbuchstaben E filtern:** [/?a=institution&anf=E](https://de.foto-ch.ch/api/?a=institution&anf=E)

### Detaildaten einer Institution
- **Beschreibung:** Gibt eine Liste mit Detailinfos einer Institution zurück
- **Aufruf:** [/?a=institution&id=300](https://de.foto-ch.ch/api/?a=institution&id=300)

**Beispiel-Resultat für Institutions-ID 2**
```json
{  
   "name":"Staatsarchiv des Kantons Bern (StAB)",
   "adresse":"Falkenplatz 4",
   "ort":"3012 Bern",
   "isil":"",
   "art":"\u00f6ffentliches Archiv",
   "art_id":"1",
   "kanton":"BE",
   "homepage":"<a href=\"http:\/\/www.be.ch\/staatsarchiv\" target=\"_new\">www.be.ch\/staatsarchiv<\/a>",
   "bildgattungen_set":"Personen, Reportage, Presse, Portr\u00e4t, Ortsbild, Architektur, Landschaft, Natur, Bergfotografie, Flugaufnahme, Gewerbe, Industrie, Tourismus, Landwirtschaft, Tiere, Sachaufnahme, Reproduktion, Werbung, Mode, Firmenportr\u00e4t, Sport, Unfall\/Katastrophe, Verkehr, Milit\u00e4r, Ethnologie, Medizin, Kriminologie, Musik, Theater, Kunst, Alltag, Akt, Film, Dokumentation, Multivision, private Fotografie, wissenschaftliche Fotografie, Reise",
   "zugang_zur_sammlung":"Voranmeldung erforderlich",
   "sammlungszeit":"1860 - heute",
   "sammlungsbeschreibung":"",
   "sammlungsgeschichte":"",
   "bearbeitungsdatum":"8.12.2014",
   "gesperrt":"0",
   "bestaende":[  
      {  
         "id":"46",
         "inst_id":"2",
         "gi":null,
         "name":"Archiv des Schweizerischen Berufsfotografenverbandes",
         "inst_name":null,
         "zeitraum":"1889-1999",
         "Bestand":null
      },
      {  
         "id":"873",
         "inst_id":"2",
         "gi":null,
         "name":"Bachmann (Fotonachlass)",
         "inst_name":null,
         "zeitraum":"um 1910 bis um 1920",
         "Bestand":""
      },
      {  
         "id":"3100",
         "inst_id":"2",
         "gi":null,
         "name":"Baumann (Fotonachlass)",
         "inst_name":null,
         "zeitraum":"ca. 1950 - 2002",
         "Bestand":""
      },
      {  
         "id":"1357",
         "inst_id":"2",
         "gi":null,
         "name":"Bernhardt Franz; Bernhardt Walter; Bernhardt Otto (Fotonachlass)",
         "inst_name":null,
         "zeitraum":"1900 bis 1980er Jahre",
         "Bestand":""
      }
   ],
   "literatur":[  
      {  
         "id":"569",
         "text":"Mathys, Nora: Welche Fotografien sind erhaltenswert? Ein Diskussionsbeitrag zur Bewertung von Fotografennachl\u00e4ssen, in: Der Archivar. Mitteilungsblatt f\u00fcr deutsches Archivwesen, 2007, 1, S. 34-40."
      },
      {  
         "id":"13788",
         "text":"Netzwerk Pressebildarchive (Hg.): Pressefotografie! Photographie de presse! Fotografia giornalistica!, Aarau, Netzwerk Pressebildarchive 2014."
      },
      {  
         "id":"285",
         "text":"T\u00fcrler, Heinrich: Inventar des Staatsarchivs des Kantons Bern, in: Inventare schweizerischer Archive, hrsg. von der Allgemeinen geschichtsforschenden Gesellschaft der Schweiz [ = Beiheft zum Anzeiger f\u00fcr schweiz. Geschichte], Bern 1895, I. Teil, S. 38-64."
      }
   ],
   "einzelausstellungen":[  
      {  
         "id":"6907",
         "text":"2009, Bern, Kornhausforum, Walter Nydegger",
         "titel":"Walter Nydegger",
         "ort":"Bern",
         "jahr":"2009",
         "institution":"Kornhausforum"
      },
      {  
         "id":"5956",
         "text":"2011, Bern, Kornhausforum, Albert Winkler. Fotografien ",
         "titel":"Albert Winkler. Fotografien ",
         "ort":"Bern",
         "jahr":"2011",
         "institution":"Kornhausforum"
      },
      {  
         "id":"6908",
         "text":"2010, Bern, Kornhausforum, Margrit und Ernst Baumann. Fotoreportagen 1950 \u2013 2000",
         "titel":"Margrit und Ernst Baumann. Fotoreportagen 1950 \u2013 2000",
         "ort":"Bern",
         "jahr":"2010",
         "institution":"Kornhausforum"
      }
   ],
   "gruppenausstellungen":null,
   "photos":null,
   "userlevel":null
}

```

## Ausstellungen
### Liste mit allen Ausstellungen
- **Beschreibung:** Gibt eine Liste mit allen Ausstellungen zurück.
- **Aufruf:** [/?a=exhibition](https://de.foto-ch.ch/api/?a=exhibition)

**Beispiel-Resultat (erste 2 Einträge)**
```json
{  
   "res":[  
      {  
         "titel":"",
         "jahr":"1904",
         "ort":"Wien",
         "typ":"G",
         "institution":null,
         "inst_id":null,
         "nameclass":"subtitle3",
         "id":"26",
         "gesperrt":null,
         "photographer":[  
            {  
               "name":"Rodolphe Archibald  Reiss",
               "id":"2009",
               "nachname":"Reiss",
               "vorname":"Rodolphe Archibald",
               "namenszusatz":"",
               "gesperrt":"0",
               "bildgattungen":[  
                  "Personen",
                  "Ortsbild",
                  "Architektur",
                  "Landschaft",
                  "Medizin",
                  "Kriminologie",
                  "Theater",
                  "wissenschaftliche Fotografie"
               ]
            }
         ]
      },
      {  
         "titel":"",
         "jahr":"1956",
         "ort":"Paris",
         "typ":"G",
         "institution":"Grand Palais",
         "inst_id":null,
         "nameclass":"subtitle3",
         "id":"11334",
         "gesperrt":null,
         "photographer":[  
            {  
               "name":"Willi  Eidenbenz",
               "id":"21122",
               "nachname":"Eidenbenz",
               "vorname":"Willi",
               "namenszusatz":"",
               "gesperrt":"0",
               "bildgattungen":[  
                  ""
               ]
            }
         ]
      },
      {  
         "titel":"",
         "jahr":"1978",
         "ort":"New York",
         "typ":"G",
         "institution":"Nikon Gallery",
         "inst_id":null,
         "nameclass":"subtitle3",
         "id":"12225",
         "gesperrt":null,
         "photographer":[  
            {  
               "name":"G\u00e9rard  P\u00e9tremand",
               "id":"22685",
               "nachname":"P\u00e9tremand",
               "vorname":"G\u00e9rard",
               "namenszusatz":"",
               "gesperrt":"0",
               "bildgattungen":[  
                  "Werbung",
                  "Kunst mit Fotografie",
                  "Film"
               ]
            }
         ]
      },
      {  
         "titel":"",
         "jahr":"1981",
         "ort":"Gen\u00e8ve",
         "typ":"G",
         "institution":"Galerie Le Tr\u00e9pied",
         "inst_id":null,
         "nameclass":"subtitle3",
         "id":"111",
         "gesperrt":null,
         "photographer":[  
            {  
               "name":"George  Basas",
               "id":"1019",
               "nachname":"Basas",
               "vorname":"George",
               "namenszusatz":"",
               "gesperrt":"0",
               "bildgattungen":[  
                  "Sachaufnahme",
                  "Mode",
                  "Kunst mit Fotografie"
               ]
            }
         ]
      }
   ]
}
```

#### Parameter
- **Nach Anfangsbuchstaben E filtern:** [/?a=exhibition&anf=E](https://de.foto-ch.ch/api/?a=exhibition&anf=E)

### Detaildaten einer Ausstellung
- **Beschreibung:** Gibt ein Liste mit Detailinfos einer Ausstellung zurück
- **Aufruf:** [/?a=exhibition&id=6711](https://de.foto-ch.ch/api/?a=exhibition&id=6711)

**Beispiel-Resultat für Ausstellung mit ID 6711**
```json
{  
   "text":"2003, Paris, Fnac Montparnasse, East of a New Eden",
   "titel":"East of a New Eden",
   "jahr":"2003",
   "ort":"Paris",
   "institution":"Fnac Montparnasse",
   "bearbeitungsdatum":"2.2.2012",
   "photographer":[  
      {  
         "name":"Alban  Kakulya",
         "nachname":"Kakulya",
         "vorname":"Alban",
         "namenszusatz":"",
         "id":"29790",
         "gesperrt":"0"
      }
   ]
}
```

## Bestände
### Liste aller Bestände
- **Beschreibung:** Gibt eine Liste aller Bestände zurück.
- **Aufruf:** [/?a=inventory](https://de.foto-ch.ch/api/?a=inventory)

**Beispiel-Resultat (erste 4 Einträge)**
```json
{  
   "res":[  
      {  
         "name":" Archivio fotografico Luigi Gisep",
         "institution":null,
         "inst_id":"694",
         "nameclass":"subtitle3x",
         "id":"4050",
         "gesperrt":"1",
         "photographer":[  
            {  
               "name":"Guglielmo  Fanconi",
               "id":"24605",
               "nachname":"Fanconi",
               "vorname":"Guglielmo",
               "namenszusatz":"",
               "gesperrt":"0",
               "bildgattungen":[  
                  ""
               ]
            },
            {  
               "name":"Mario  Fanconi",
               "id":"35001",
               "nachname":"Fanconi",
               "vorname":"Mario",
               "namenszusatz":"",
               "gesperrt":"0",
               "bildgattungen":[  
                  ""
               ]
            },
            {  
               "name":"Riccardo  Fanconi",
               "id":"34614",
               "nachname":"Fanconi",
               "vorname":"Riccardo",
               "namenszusatz":"",
               "gesperrt":"0",
               "bildgattungen":[  
                  ""
               ]
            },
            {  
               "name":"Iginia  Fanconi-Bindsch\u00e4dler",
               "id":"35002",
               "nachname":"Fanconi-Bindsch\u00e4dler",
               "vorname":"Iginia",
               "namenszusatz":"",
               "gesperrt":"0",
               "bildgattungen":[  
                  ""
               ]
            },
            {  
               "name":"Giulio  Lanfranchini",
               "id":"34613",
               "nachname":"Lanfranchini",
               "vorname":"Giulio",
               "namenszusatz":"",
               "gesperrt":"0",
               "bildgattungen":[  
                  ""
               ]
            },
            {  
               "name":"Francesco  Olgiati",
               "id":"34612",
               "nachname":"Olgiati",
               "vorname":"Francesco",
               "namenszusatz":"",
               "gesperrt":"0",
               "bildgattungen":[  
                  "Personen",
                  "Portr\u00e4t",
                  "Ortsbild",
                  "Architektur",
                  "Landschaft",
                  "Bergfotografie",
                  "Gewerbe",
                  "Tourismus",
                  "Verkehr"
               ]
            }
         ]
      },
      {  
         "name":" Henze, Hans Werner",
         "institution":null,
         "inst_id":"522",
         "nameclass":"subtitle3",
         "id":"2820",
         "gesperrt":"0",
         "photographer":[  

         ]
      },
      {  
         "name":" Kagel, Mauricio",
         "institution":null,
         "inst_id":"522",
         "nameclass":"subtitle3",
         "id":"2821",
         "gesperrt":"0",
         "photographer":[  

         ]
      },
      {  
         "name":" Kantonale Denkmalpflege (KDP) des Kantons Bern",
         "institution":null,
         "inst_id":"26",
         "nameclass":"subtitle3",
         "id":"220",
         "gesperrt":"0",
         "photographer":[  
            {  
               "name":"  ",
               "id":"20765",
               "nachname":null,
               "vorname":null,
               "namenszusatz":null,
               "gesperrt":null,
               "bildgattungen":[  
                  ""
               ]
            },
            {  
               "name":"  Alpar Bern",
               "id":"20016",
               "nachname":"Alpar Bern",
               "vorname":"",
               "namenszusatz":"",
               "gesperrt":"0",
               "bildgattungen":[  
                  ""
               ]
            },
            {  
               "name":"  Comet Photo AG",
               "id":"20181",
               "nachname":"Comet Photo AG",
               "vorname":"",
               "namenszusatz":"",
               "gesperrt":"0",
               "bildgattungen":[  
                  ""
               ]
            },
            {  
               "name":"Hans A.  Fischer",
               "id":"20273",
               "nachname":"Fischer",
               "vorname":"Hans A.",
               "namenszusatz":"",
               "gesperrt":"0",
               "bildgattungen":[  
                  ""
               ]
            },
            {  
               "name":"Hans Alfred  Heiniger",
               "id":"1265",
               "nachname":"Heiniger",
               "vorname":"Hans Alfred",
               "namenszusatz":"",
               "gesperrt":"0",
               "bildgattungen":[  
                  ""
               ]
            },
            {  
               "name":"Martin  Hesse",
               "id":"1272",
               "nachname":"Hesse",
               "vorname":"Martin",
               "namenszusatz":"",
               "gesperrt":"0",
               "bildgattungen":[  
                  ""
               ]
            },
            {  
               "name":"Gerhard  Howald",
               "id":"1276",
               "nachname":"Howald",
               "vorname":"Gerhard",
               "namenszusatz":"",
               "gesperrt":"0",
               "bildgattungen":[  
                  ""
               ]
            },
            {  
               "name":"Theodor von Lerber",
               "id":"20547",
               "nachname":"Lerber",
               "vorname":"Theodor",
               "namenszusatz":"von",
               "gesperrt":"0",
               "bildgattungen":[  
                  ""
               ]
            },
            {  
               "name":"Robert  Marti-Wehren",
               "id":"1400",
               "nachname":"Marti-Wehren",
               "vorname":"Robert",
               "namenszusatz":"",
               "gesperrt":"0",
               "bildgattungen":[  
                  ""
               ]
            },
            {  
               "name":"Fernand Sepp  Rausser",
               "id":"1465",
               "nachname":"Rausser",
               "vorname":"Fernand Sepp",
               "namenszusatz":"",
               "gesperrt":"0",
               "bildgattungen":[  
                  ""
               ]
            },
            {  
               "name":"Christian  Rubi",
               "id":"1488",
               "nachname":"Rubi",
               "vorname":"Christian",
               "namenszusatz":"",
               "gesperrt":"0",
               "bildgattungen":[  
                  ""
               ]
            },
            {  
               "name":"Alfred  Sch\u00e4tzle",
               "id":"20795",
               "nachname":"Sch\u00e4tzle",
               "vorname":"Alfred",
               "namenszusatz":"",
               "gesperrt":"0",
               "bildgattungen":[  
                  ""
               ]
            },
            {  
               "name":"Roland von Siebenthal",
               "id":"20844",
               "nachname":"Siebenthal",
               "vorname":"Roland",
               "namenszusatz":"von",
               "gesperrt":"0",
               "bildgattungen":[  
                  ""
               ]
            }
         ]
      }
   ]
}
```

#### Parameter
- **Nach Anfangsbuchstaben E filtern:** [/?a=inventory&anf=E](https://de.foto-ch.ch/api/?a=inventory&anf=E)

### Detaildaten eines Bestandes
- **Beschreibung:** Gibt ein Liste mit Detailinfos eines Bestandes zurück
- **Aufruf:** [/?a=inventory&id=128](https://de.foto-ch.ch/api/?a=inventory&id=129)

**Beispiel-Resultat für Bestand mit ID 129 (gekürzt)**
```json
{  
   "id":"129",
   "name":"Schweizerisches Rotes Kreuz (CH-BAR#J2.15-02*, Schweizerisches Rotes Kreuz: Zentrale Ablage)",
   "bearbeitungsdatum":"23.3.2016",
   "zeitraum":"1901\u20131990",
   "bestandsbeschreibung":"",
   "link_extern":"",
   "signatur":"CH-BAR#J2.15-02#1969\/7*",
   "copyright":"",
   "bildgattungen":"Personen, Reportage, Portr\u00e4t, Ortsbild, Landschaft, Flugaufnahme, Landwirtschaft, Tiere, Sachaufnahme, Unfall\/Katastrophe, Milit\u00e4r, Ethnologie, Medizin, Dokumentation",
   "umfang":"10'000 Einheiten",
   "weiteres":"",
   "erschliessungsgrad":"Auf Dossier-Ebene erschlossen",
   "inst_id":"8",
   "inst_name":"Schweizerisches Bundesarchiv",
   "photographer":[  
      {  
         "name":"Ch.  Alonso",
         "id":"20015",
         "nachname":"Alonso",
         "vorname":"Ch.",
         "namenszusatz":"",
         "gesperrt":"1"
      },
      {  
         "name":"  Armeefilmdienst",
         "id":"20031",
         "nachname":"Armeefilmdienst",
         "vorname":"",
         "namenszusatz":"",
         "gesperrt":"1"
      },
      {  
         "name":"  Arni",
         "id":"20033",
         "nachname":"Arni",
         "vorname":"",
         "namenszusatz":"",
         "gesperrt":"1"
      }

   ],
   "photos":null
}
```

## Fotoportal
### Liste aller Fotos
- **Beschreibung:** Gibt eine Liste aller Fotos zurück.
- **Aufruf:** [/?a=photo](https://de.foto-ch.ch/api/?a=photo)

**Beispiel-Resultat (erster Eintrag)**
```json
{  
   "result_count":6485,
   "res":[  
      {  
         "id":"15673",
         "dc_created":"1944-01-01",
         "created":"1944",
         "title":"Knabe, Heim Sonnenberg, Luzern",
         "description":null,
         "dcterms_ispart_of":"234",
         "image_path":"psimage\/PS_X999_001NEND001R.jpg",
         "name":"Paul Senn",
         "photographer_id":"1541",
         "institution":"Bernische Stiftung f\u00fcr Foto, Film und Video im Kunstmuseum Bern",
         "institution_id":"35",
         "stock":"Senn, Paul (Nachlass)",
         "stock_id":"234"
      }
   ]
}
```

#### Parameter
- **Fotos eines bestimmten Fotografen:** [/?a=photo&photographer=1541](https://de.foto-ch.ch/api/?a=photo&photographer=1541)
- **Fotos eines bestimmten Bestandes:** [/?a=photo&inventory=234](https://de.foto-ch.ch/api/?a=photo&inventory=234)
- **Fotos eines bestimmten Bestandes:** [/?a=photo&institution=35](https://de.foto-ch.ch/api/?a=photo&institution=35)

### Detaildaten eines Bildes
- **Beschreibung:** Gibt die Detaildaten eines Bildes zurück.
- **Aufruf:** [/?a=photo&id=9969](https://de.foto-ch.ch/api/?a=photo&id=9969)

**Beispiel-Resultat für Bild mit der ID 15673**
```json
{  
   "id":"15673",
   "dc_created":"1944-01-01",
   "created":"1944",
   "title":"Knabe, Heim Sonnenberg, Luzern",
   "description":null,
   "dc_creator":"1541",
   "image_path":"psimage\/PS_X999_001NEND001R.jpg",
   "copy":"Gottfried Keller-Stiftung, Bern.",
   "medium":"",
   "img_url":"http:\/\/www.paulsenn.ch\/Bilder\/Onlinearchiv\/Larges\/PS_X999_001NEND001R.jpg",
   "dct_spatial":"Luzern",
   "subject":"",
   "dc_coverage":null,
   "name":"Paul Senn",
   "institution":"Bernische Stiftung f\u00fcr Foto, Film und Video im Kunstmuseum Bern",
   "inst_id":"35",
   "stock":"Senn, Paul (Nachlass)",
   "stock_id":"234",
   "supp_id":"PS_X999_001NEND001R"
}
```

## Georeferenzen 
dieser Bereich ist noch nicht definitiv!
Test im Frontend:
[https://de.foto-ch.ch/#/test] https://de.foto-ch.ch/#/test

### Liste aller Arbeitsorte und Aufnahmeorte von Fotos
- **Beschreibung:** Gibt eine Liste aller Fotos zurück.
- **Aufruf:** [/?a=photo](https://de.foto-ch.ch/api/?a=orte)
- **Parameter:**
photo=0 : keine Aufnahmeorte
kanton=BE, land=deu : Eingrenzen der Arbeitsorte
Aufnahmeorte sind provisorisch am Attribut "swissname":"fotoquery" erkennbar

**Beispiel-Resultat (drei Einträge, 2 Arbeitsorte 1 Aufnahmeort)**
```json
[  
   {  
      "id":"4",
      "name":"Aachen",
      "lat":"50.7753455",
      "lon":"6.0838868",
      "swissname":"",
      "lat_o":"0",
      "lon_o":"0",
      "sn_o":"",
      "status":"0",
      "stat":"1",
      "kanton":"",
      "land":"DEU",
      "dc_right":"",
      "image_path":""
   },
   {  
      "id":"5",
      "name":"Aarau",
      "lat":"47.393207550049",
      "lon":"8.0549411773682",
      "swissname":"<b>Aarau<\/b> (AG) - Aarau",
      "lat_o":"47.391036987305",
      "lon_o":"8.0460901260376",
      "sn_o":"<b>Aarau (AG)<\/b>",
      "status":"0",
      "stat":"3",
      "kanton":"AG",
      "land":"",
      "dc_right":"",
      "image_path":""
   },
   {
   		"id":"22808",
   		"dc_creator":"20265",
   		"dc_created":"0000-00-00",
   		"dc_title":"Kurhaus Tarasp, Parkanlagen mit Aj\u00fcz",
   		"dc_description":"",
   		"dc_identifier":"",
   		"dc_right":"\u00a9 Fundaziun 100 ons dinastia da fotografs Feuerstein",
   		"dc_coverage":"",
   		"dcterms_ispart_of":"4095",
   		"dcterms_medium":"Negativ Glas, 13 x 18 cm",
   		"dcterms_spatial":"Tarasp",
   		"dcterms_subject":"",
   		"edm_dataprovider":"701",
   		"image_path":"fn\/FF_L_3472_NG.jpg",
   		"all":"",
   		"dc_created_bis":"0000-00-00",
   		"supplier_id":"FF_L_3472_NG",
   		"zeitraum":"",
   		"lon":"10.259723",
   		"lat":"46.776662",
   		"google_revcode":"",
   		"name":"Tarasp: Kurhaus Tarasp, Parkanlagen mit Aj\u00fcz",
   		"swissname":"fotoquery"
   	}
]
```
### Detaildaten eines Arbeitsortes
- **Beschreibung:** Gibt die Fotografen, die an diesem Ort gearbeitet haben wieder
- **Aufruf:** [/?a=perioden&id=4](https://de.foto-ch.ch/api/?a=perioden&id=4)

**Beispiel-Resultat für Arbeitsort mit der ID 4**
```json
[  
   {  
      "id":"25331",
      "name":"Jacob Woodtly",
      "periode":"1849 - 1873"
   },
   {  
      "id":"25331",
      "name":"Jacob Wothly",
      "periode":"1849 - 1873"
   },
   {  
      "id":"1133",
      "name":"Carl Durheim",
      "periode":"1853 - "
   },
   {  
      "id":"1133",
      "name":"Charles Durheim",
      "periode":"1853 - "
   },
   {  
      "id":"24970",
      "name":"Eduard Siegwart",
      "periode":"um 1870 - "
   }
]
```

## Suchfunktion ##
dieser Bereich ist noch nicht definitiv!

### Sucht nach Stichwort in vier Entitätstypen
- **Beschreibung:** Sucht nach Stichwort in vier Entitätstypen
- **Aufruf:** [/?a=search&query=Stichwort](https://de.foto-ch.ch/api/?a=search&query=Stichwort)

**Beispiel-Resultat Such nach Bern (gekürzt)**
```json
{  
   "photographer_results":[  
      {  
         "bioclass":"subtitle3",
         "fgeburtsdatum":"",
         "fldatum":"",
         "nachname":"Abernathy",
         "vorname":"J. F.",
         "namenszusatz":"",
         "id":"33069",
         "bearbeitungsdatum":"29.5.2013"
      },
      {  
         "bioclass":"subtitle3",
         "fgeburtsdatum":"",
         "fldatum":"",
         "nachname":"Abgottspon",
         "vorname":"Bernarda",
         "namenszusatz":"",
         "id":"24157",
         "bearbeitungsdatum":"15.8.2008"
      },
      {  
         "bioclass":"subtitle3",
         "fgeburtsdatum":"",
         "fldatum":"*",
         "nachname":"Aeby",
         "vorname":"Bernard",
         "namenszusatz":"",
         "id":"24000",
         "bearbeitungsdatum":"26.3.2013"
      }
   ],
   "photographer_result_count":126,
   "literature_results":[  
      {  
         "name":" Kantonale Denkmalpflege (KDP) des Kantons Bern",
         "institution":null,
         "inst_id":"26",
         "nameclass":"subtitle3",
         "id":"220",
         "gesperrt":"0"
      },
      {  
         "name":"Anderes, Bernhard: Archiv Bernhard Anderes",
         "institution":null,
         "inst_id":"679",
         "nameclass":"subtitle3",
         "id":"3764",
         "gesperrt":"0"
      },
      {  
         "name":"Archiv der Schweizerischen Landesausstellung Bern 1914",
         "institution":null,
         "inst_id":"2",
         "nameclass":"subtitle3",
         "id":"63",
         "gesperrt":"0"
      }
   ],
   "literature_result_count":58,
   "institution_results":[  
      {  
         "nameclass":null,
         "name":"Bernische Stiftung f\u00fcr Foto, Film und Video im Kunstmuseum Bern",
         "abkuerzung":"",
         "id":"35"
      },
      {  
         "nameclass":null,
         "name":"Burgerbibliothek Bern",
         "abkuerzung":"(BBB)",
         "id":"13"
      },
      {  
         "nameclass":null,
         "name":"B\u00fcro f\u00fcr Fotografiegeschichte Bern",
         "abkuerzung":"",
         "id":"144"
      }
   ],
   "institution_result_count":12,
   "inventory_results":[  
      {  
         "name":" Kantonale Denkmalpflege (KDP) des Kantons Bern",
         "institution":"Denkmalpflege des Kantons Bern",
         "inst_id":"26",
         "nameclass":"subtitle3",
         "id":"220",
         "gesperrt":"0"
      },
      {  
         "name":"Anderes, Bernhard: Archiv Bernhard Anderes",
         "institution":"Schweizerische Nationalbibliothek - Graphische Sammlung \/ Eidgen\u00f6ssisches Archiv f\u00fcr Denkmalpflege",
         "inst_id":"679",
         "nameclass":"subtitle3",
         "id":"3764",
         "gesperrt":"0"
      },
      {  
         "name":"Archiv der Schweizerischen Landesausstellung Bern 1914",
         "institution":"Staatsarchiv des Kantons Bern",
         "inst_id":"2",
         "nameclass":"subtitle3",
         "id":"63",
         "gesperrt":"0"
      }
   ],
   "inventory_result_count":58,
   "exhibition_results":[  
      {  
         "titel":"1+1+1=3: Hermann Pitz, Michael Snow, Bernard Vo\u00efta",
         "jahr":"2011",
         "ort":"Lissabon",
         "typ":"E",
         "institution":"Culturgest",
         "inst_id":null,
         "nameclass":"subtitle3",
         "id":"14843",
         "gesperrt":null
      },
      {  
         "titel":"1. Berner Biennale",
         "jahr":"1989",
         "ort":"Kunstmuseum Bern",
         "typ":"G",
         "institution":null,
         "inst_id":null,
         "nameclass":"subtitle3",
         "id":"174",
         "gesperrt":null
      },
      {  
         "titel":"10 Berner K\u00fcnstler. Ueli Berger, Herbert Distel, Bendicht Fivian, usw",
         "jahr":"1970",
         "ort":"Olten",
         "typ":"G",
         "institution":"Stadthaus Olten",
         "inst_id":null,
         "nameclass":"subtitle3",
         "id":"8305",
         "gesperrt":null
      }
   ],
   "exhibition_result_count":111,
   "photo_results":[  
      {  
         "id":"15680",
         "dc_created":"1935-01-01",
         "created":"1935",
         "title":"Hornussen im Bernbiet",
         "description":null,
         "dcterms_ispart_of":"234",
         "image_path":"psimage\/PS_X999_008ASD001R.jpg",
         "name":"Paul Senn",
         "institution":"Bernische Stiftung f\u00fcr Foto, Film und Video im Kunstmuseum Bern",
         "stock":"Senn, Paul (Nachlass)"
      },
      {  
         "id":"15683",
         "dc_created":"1935-01-01",
         "created":"1935",
         "title":"Knechte-Dinget, Bern",
         "description":null,
         "dcterms_ispart_of":"234",
         "image_path":"psimage\/PS_X999_011NSND001R.jpg",
         "name":"Paul Senn",
         "institution":"Bernische Stiftung f\u00fcr Foto, Film und Video im Kunstmuseum Bern",
         "stock":"Senn, Paul (Nachlass)"
      },
      {  
         "id":"15684",
         "dc_created":"1938-01-01",
         "created":"1938",
         "title":"Holzer, Berner Oberland",
         "description":null,
         "dcterms_ispart_of":"234",
         "image_path":"psimage\/PS_X999_012NEND004R.jpg",
         "name":"Paul Senn",
         "institution":"Bernische Stiftung f\u00fcr Foto, Film und Video im Kunstmuseum Bern",
         "stock":"Senn, Paul (Nachlass)"
      }
   ],
   "photo_result_count":81
}
```



## Versionsübersicht
- 30.05.2016 - 1.0.0: Schlusskorrekturen, Markus Schürpf
- 19.05.2016 - 0.9.1: Ergänzungen und Erweiterungen (Suche, Georeferenzen), Christian Schweingruber
- 18.05.2016 - 0.9.0: Vorbereitung zur Publikation, Christian Schweingruber
- 24.04.2015 - 0.0.5: Ausstellung und Fotografen aktualisiert, Stefan Pfister.
- 23.03.2015 - 0.0.4: Ausstellung hinzugefügt, Stefan Pfister.
- 18.03.2015 - 0.0.3: API-Aufrufe verlinken; API für statische Inhalte und Partner hinzugefügt, Stefan Pfister.
- 16.03.2015 - 0.0.2: Bestandsfelder aktualisiert und Sprach-API hinzugefügt, Stefan Pfister.
- 13.03.2015 - 0.0.1: Initiale Version, Stefan Pfister.
