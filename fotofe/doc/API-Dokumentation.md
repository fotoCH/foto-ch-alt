# API Dokumentation
Dieses Dokument beschreibt die API, welche für fotoCH entwickelt wurde.

## Allgemeine Infos
Alle API Aufrufe in diesem Dokument sind relativ zu **API-URL: https://www2.foto-ch.ch/api/**. Die Daten werden im JSON Format zurückgegeben.

## Dokumentation-History
- 16.03.2015 - 0.0.2: Bestandsfelder aktualisiert und Sprach-API hinzugefügt
- 13.03.2015 - 0.0.1: Initiale Version, Stefan Pfister

## Sprach-API
Mit der Sprach-API können sämtliche UI-Strings aus der Datenbank abgerufen werden.

- **https://www2.foto-ch.ch/api/?a=sprache&lang=de** (Deutsch)
- **https://www2.foto-ch.ch/api/?a=sprache&lang=fr** (Französisch)
- **https://www2.foto-ch.ch/api/?a=sprache&lang=it** (Italienisch)

## Fotografen
### Liste aller Fotografen
- **Beschreibung:** Gibt eine Liste aller Fotografen zurück.
- **Call:** /?a=fotographer

**Beispiel-Resultat (erste 2 Einträge)**
```
{
	"res": [
	{
		"bioclass": "subtitle3",
		"fgeburtsdatum": "",
		"fldatum": "*",
		"nachname": "A. Barras Photo-Ciné-Projection",
		"vorname": "",
		"namenszusatz": "",
		"id": "24019"
		},
		{
			"bioclass": "subtitle3bio",
			"fgeburtsdatum": "26.03.1910",
			"fldatum": "26.03.1910 - 07.04.1971",
			"nachname": "A. T. P. Bilderdienst",
			"vorname": "",
			"namenszusatz": "",
			"id": "21310"
		}
	]
}
```

####Parameter
- **Nach Anfangsbuchstaben E filtern:** /?a=fotographer&anf=E

### Detaildaten eines Fotografen
- **Beschreibung:** Gibt ein Liste mit Detailinfos eines Fotografen zurück
- **Call:** /?a=fotographer&id=1542

**Beispiel-Resultat für Fotografen-ID 1542**
```
{
	"namen": [
	{
		"titel": "",
		"nachname": "Siegold",
		"vorname": "E.",
		"namenszusatz": "",
		"idf": null
	}
	],
	"originalsprache": "de",
	"sprachanzeige": "",
	"id": "",
	"pnd": null,
	"pnd_status": "99",
	"fbearbeitungsdatum": "12.02.2007",
	"fldatum": "",
	"fumfeld": "",
	"fotografengattungen_set": "Wanderfotograf",
	"bildgattungen_set": "Personen",
	"heimatort": "",
	"beruf": "",
	"fotografengattungen": "Wanderfotograf",
	"bildgattungen": "Personen",
	"arbeitsperioden": [
	{
		"arbeitsort": "Langenthal BE",
		"um_vonf": "um ",
		"von": "1867",
		"um_bisf": "",
		"bis": ""
	}
	],
	"umfeld": "",
	"werdegang": "",
	"schaffensbeschrieb": "",
	"auszeichnungen_und_stipendien": "",
	"bestaende": null,
	"primaerliteratur": null,
	"sekundaerliteratur": [
	{
		"id": "200",
		"text": "Schürpf, Markus: Fotografie in Langenthal. 1857-1998, Langenthal, Merkur 1998."
	}
	],
	"einzelausstellungen": null,
	"gruppenausstellungen": null,
	"autorIn": "",
	"bearbeitungsdatum": "12.02.2007"
}
```

## Institutionen
### Liste aller Institutionen
- **Beschreibung:** Gibt eine Liste aller Institutionen zurück.
- **Call:** /?a=institution

**Beispiel-Resultat (erste 2 Einträge)**
```
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

####Parameter
- **Nach Anfangsbuchstaben E filtern:** /?a=institution&anf=E

### Detaildaten einer Institution
- **Beschreibung:** Gibt ein Liste mit Detailinfos einer Institution zurück
- **Call:** /?a=institution&id=300

**Beispiel-Resultat für Institutions-ID 300**
```
{
	"name": "Archäologie und Museum Baselland",
	"adresse": "Amtshausgasse 7",
	"ort": "4410 Liestal",
	"isil": "",
	"art": "Dokumentationszentrum / Museum",
	"homepage": " <a href=\"http://www.archaeologie.bl.ch\" target=\"_new\">www.archaeologie.bl.ch</a>",
	"bildgattungen_set": "Personen, Porträt, Ortsbild, Architektur, Landschaft, Natur, Flugaufnahme, Industrie, Landwirtschaft, Sachaufnahme, Mode, Unfall/Katastrophe, Militär, Ethnologie, Archäologie, Kunst, Alltag, Dokumentation, private Fotografie, wissenschaftliche Fotografie",
	"zugang_zur_sammlung": "nach Voranmeldung",
	"sammlungszeit": "Ende 19. Jahrhundert - ",
	"sammlungsbeschreibung": "",
	"sammlungsgeschichte": "Ab den 1930er Jahren wurde auf archäologischen Fundstellen systematisch fotografiert und das Archiv wird laufend erweitert.<br>\r\nAb den späten 1960er Jahren wurden erste Ausstellungen des Kantonsmuseums (heute Museum.BL) mit Fotos illustriert und dokumentiert.<br>\r\nAb den 1980er Jahren wurden erste historische Fotos und Ansichtskarten eher zufallsmässig gesammelt.\r\n",
	"bearbeitungsdatum": "2013-01-29",
	"gesperrt": "0",
	"bestaende": [
		{
			"id": "3231",
			"inst_id": "300",
			"gi": null,
			"name": "Fotoatelier Seiler, Liestal",
			"inst_name": null,
			"zeitraum": "1880-1960",
			"Bestand": null
		},
		{
			"id": "1625",
			"inst_id": "300",
			"gi": null,
			"name": "Nachlass Strübin, Theodor",
			"inst_name": null,
			"zeitraum": "1935-1970",
			"Bestand": ""
		},
		{
			"id": "1624",
			"inst_id": "300",
			"gi": null,
			"name": "Fotodokumentation der Archäologie Baselland",
			"inst_name": null,
			"zeitraum": "seit 1910",
			"Bestand": ""
		},
		{
			"id": "3232",
			"inst_id": "300",
			"gi": null,
			"name": "Modefotos Seidenbandfabrik Seiler & Co., Gelterkinden",
			"inst_name": null,
			"zeitraum": "",
			"Bestand": ""
		}
	]
}
```

## Bestände
### Liste aller Bestände
- **Beschreibung:** Gibt eine Liste aller Bestände zurück.
- **Call:** /?a=inventory

**Beispiel-Resultat (erste 2 Einträge)**
```
{
	"res": [
		{
			"name": " Archivio fotografico Luigi Gisep",
			"institution": null,
			"inst_id": "694",
			"nameclass": "subtitle3x",
			"id": "4050",
			"gesperrt": "1"
		},
		{
			"name": " Henze, Hans Werner",
			"institution": null,
			"inst_id": "522",
			"nameclass": "subtitle3",
			"id": "2820",
			"gesperrt": "0"
		}
	]
}
```

####Parameter
- **Nach Anfangsbuchstaben E filtern:** /?a=inventory&anf=E

### Detaildaten eines Bestandes
- **Beschreibung:** Gibt ein Liste mit Detailinfos eines Bestandes zurück
- **Call:** /?a=inventory&id=128

**Beispiel-Resultat für Bestand mit ID 128**
```
{
	"id": "128",
	"name": "Heimatlosenporträts (CH-BAR#E21*, Polizeiwesen)",
	"bearbeitungsdatum": "2013-09-26",
	"zeitraum": "1852–1853",
	"bestandsbeschreibung": "",
	"link_extern": "",
	"signatur": "CH-BAR#E21#1000/131*",
	"copyright": "",
	"bildgattungen": "Personen, Porträt, Kriminologie",
	"umfang": "220 Einheiten",
	"weiteres": "",
	"erschliessungsgrad": "Auf Dossier-Ebene erschlossen",
	"inst_id": "8",
	"inst_name": "Schweizerisches Bundesarchiv",
	"fotographer": [
	{
		"name": "Carl  Durheim",
		"fotografen_id": "1133",
		"gesperrt": 0
	}
	]
}
```