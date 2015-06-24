# API Dokumentation
Dieses Dokument beschreibt die API, welche für fotoCH entwickelt wurde.

## Allgemeine Infos
Alle API Aufrufe in diesem Dokument sind relativ zu **API-URL: [https://www2.foto-ch.ch/api/](https://www2.foto-ch.ch/api/)**. Die Daten werden im JSON Format zurückgegeben.

## UI-Strings / Label / Sprache
Mit diesem Teil der API können sämtliche UI-Strings aus der Datenbank abgerufen werden.
- **[/?a=sprache&lang=de](https://www2.foto-ch.ch/api/?a=sprache&lang=de)** (Deutsch)
- **[/?a=sprache&lang=fr](https://www2.foto-ch.ch/api/?a=sprache&lang=fr)** (Französisch)
- **[/?a=sprache&lang=it](https://www2.foto-ch.ch/api/?a=sprache&lang=it)** (Italienisch)

## Statische Inhalte
Lieferte Inhalte für statischen Seiten, z.B. Über uns.
- **[/?a=pages&lang=de](https://www2.foto-ch.ch/api/?a=pages&lang=de)** (Deutsch)
- **[/?a=pages&lang=fr](https://www2.foto-ch.ch/api/?a=pages&lang=fr)** (Französisch)
- **[/?a=pages&lang=it](https://www2.foto-ch.ch/api/?a=pages&lang=it)** (Italienisch)

## Partner
Liefert Infos zu den Partner-Organisationen sowie den Dateinamen der Logos. Die Logos sind lokal auf dem Server gespeichert.
- **[/?a=partner&lang=de](https://www2.foto-ch.ch/api/?a=partner&lang=de)** (Deutsch)
- **[/?a=partner&lang=fr](https://www2.foto-ch.ch/api/?a=partner&lang=fr)** (Französisch)
- **[/?a=partner&lang=it](https://www2.foto-ch.ch/api/?a=partner&lang=it)** (Italienisch)

## Fotografen
### Liste aller Fotografen
- **Beschreibung:** Gibt eine Liste aller Fotografen zurück.
- **Aufruf:** [/?a=photographer](https://www2.foto-ch.ch/api/?a=photographer)

**Beispiel-Resultat (erste 2 Einträge)**
```json
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

#### Parameter
- **Nach Anfangsbuchstaben E filtern:** [/?a=photographer&anf=E](https://www2.foto-ch.ch/api/?a=photographer&anf=E)

### Detaildaten eines Fotografen
- **Beschreibung:** Gibt ein Liste mit Detailinfos eines Fotografen zurück
- **Aufruf:** [/?a=photographer&id=1542](https://www2.foto-ch.ch/api/?a=photographer&id=1542)

**Beispiel-Resultat für Fotografen-ID 1542**
```json
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



### Aktualisierte Fotografen
- **Beschreibung:** Gibt eine Liste von Fotografen zurück, welche zuletzt bearbeitet wurde.
- **Aufruf:** [/?recent=10](https://www2.foto-ch.ch/api/?recent=10)

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
- **Aufruf:** [/?a=institution](https://www2.foto-ch.ch/api/?a=institution)

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
- **Nach Anfangsbuchstaben E filtern:** [/?a=institution&anf=E](https://www2.foto-ch.ch/api/?a=institution&anf=E)

### Detaildaten einer Institution
- **Beschreibung:** Gibt ein Liste mit Detailinfos einer Institution zurück
- **Aufruf:** [/?a=institution&id=300](https://www2.foto-ch.ch/api/?a=institution&id=300)

**Beispiel-Resultat für Institutions-ID 300**
```json
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

## Ausstellungen
### Liste mit allen Ausstellungen
- **Beschreibung:** Gibt eine Liste mit allen Beständen zurück.
- **Aufruf:** [/?a=exhibition](https://www2.foto-ch.ch/api/?a=exhibition)

**Beispiel-Resultat (erste 2 Einträge)**
```json
{
	"res": [
		{
			"titel": "A & P Schudel",
			"jahr": "1985",
			"ort": "Zürich",
			"typ": "G",
			"institution": "Nikon Foto Galerie",
			"inst_id": null,
			"nameclass": "subtitle3",
			"id": "6283",
			"gesperrt": null
		},
		{
			"titel": "A Broken Arm",
			"jahr": "2006",
			"ort": "New York",
			"typ": "G",
			"institution": "303 Gallery",
			"inst_id": null,
			"nameclass": "subtitle3",
			"id": "8709",
			"gesperrt": null
		}
	]
}
```

#### Parameter
- **Nach Anfangsbuchstaben E filtern:** [/?a=exhibition&anf=E](https://www2.foto-ch.ch/api/?a=exhibition&anf=E)

### Detaildaten einer Ausstellung
- **Beschreibung:** Gibt ein Liste mit Detailinfos einer Ausstellung zurück
- **Aufruf:** [/?a=exhibition&id=6711](https://www2.foto-ch.ch/api/?a=exhibition&id=6711)

**Beispiel-Resultat für Ausstellung mit ID 6711**
```json
{
	"text": "2003, Paris, Fnac Montparnasse, East of a New Eden",
	"bearbeitungsdatum": "2.2.2012",
	"photographer": [
		{
			"name": "Alban  Kakulya",
			"fotografen_id": "29790",
			"gesperrt": "0"
		}
	]
}
```

## Bestände
### Liste aller Bestände
- **Beschreibung:** Gibt eine Liste aller Bestände zurück.
- **Aufruf:** [/?a=inventory](https://www2.foto-ch.ch/api/?a=inventory)

**Beispiel-Resultat (erste 2 Einträge)**
```json
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

#### Parameter
- **Nach Anfangsbuchstaben E filtern:** [/?a=inventory&anf=E](https://www2.foto-ch.ch/api/?a=inventory&anf=E)

### Detaildaten eines Bestandes
- **Beschreibung:** Gibt ein Liste mit Detailinfos eines Bestandes zurück
- **Aufruf:** [/?a=inventory&id=128](https://www2.foto-ch.ch/api/?a=inventory&id=128)

**Beispiel-Resultat für Bestand mit ID 128**
```json
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

## Fotoportal
### Liste aller Fotos
- **Beschreibung:** Gibt eine Liste aller Fotos zurück.
- **Aufruf:** [/?a=photo](https://www2.foto-ch.ch/api/?a=photo)

**Beispiel-Resultat (erster Eintrag)**
```json
{
    "result_count": 5964,
    "res": [
        {
            "id": "9946",
            "dc_created": "1890-01-01",
            "created": "[Entre 1890 et 1900]",
            "title": "Albeuve, village et église",
            "description": "",
            "dcterms_ispart_of": "1074",
            "image_path": "bcu/lwgg/lwgg00001.jpg",
            "name": " Léon de Weck - Georges de Gottrau",
            "institution": "Kantons- und Universitätsbibliothek Freiburg",
            "stock": "Weck, Léon de - Gottrau, Georges de "
        }
    ]
}
```

#### Parameter
- **Fotos eines bestimmten Fotografen:** [/?a=photo&photographer=1541](https://www2.foto-ch.ch/api/?a=photo&photographer=1541)
- **Fotos eines bestimmten Bestandes:** [/?a=photo&inventory=234](https://www2.foto-ch.ch/api/?a=photo&inventory=234)
- **Fotos eines bestimmten Bestandes:** [/?a=photo&institution=35](https://www2.foto-ch.ch/api/?a=photo&institution=35)

### Detaildaten eines Bildes
- **Beschreibung:** Gibt die Detaildaten eines Bildes zurück.
- **Aufruf:** [/?a=photo&id=9969](https://www2.foto-ch.ch/api/?a=photo&id=9969)

**Beispiel-Resultat für Bild mit der ID 9969**
```json
{
    "0": "9969",
    "1": "1886-01-01",
    "2": "[Entre 1886 et 1896]",
    "3": "[Jaun], Bellegarde, village",
    "4": "Porte le titre original: Bellegarde, rochers, ruines + village",
    "5": "bcu/lwgg/lwgg00025.jpg",
    "6": " Léon de Weck - Georges de Gottrau",
    "7": "Kantons- und Universitätsbibliothek Freiburg",
    "8": "Weck, Léon de - Gottrau, Georges de ",
    "id": "9969",
    "dc_created": "1886-01-01",
    "created": "[Entre 1886 et 1896]",
    "title": "[Jaun], Bellegarde, village",
    "description": "Porte le titre original: Bellegarde, rochers, ruines + village",
    "image_path": "bcu/lwgg/lwgg00025.jpg",
    "name": " Léon de Weck - Georges de Gottrau",
    "institution": "Kantons- und Universitätsbibliothek Freiburg",
    "stock": "Weck, Léon de - Gottrau, Georges de "
}
```

## Versionsübersicht
- 24.04.2015 - 0.0.5: Ausstellung und Fotografen aktualisiert, Stefan Pfister.
- 23.03.2015 - 0.0.4: Ausstellung hinzugefügt, Stefan Pfister.
- 18.03.2015 - 0.0.3: API-Aufrufe verlinken; API für statische Inhalte und Partner hinzugefügt, Stefan Pfister.
- 16.03.2015 - 0.0.2: Bestandsfelder aktualisiert und Sprach-API hinzugefügt, Stefan Pfister.
- 13.03.2015 - 0.0.1: Initiale Version, Stefan Pfister.
