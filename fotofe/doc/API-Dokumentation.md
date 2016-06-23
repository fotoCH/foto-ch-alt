# API Dokumentation
Dieses Dokument beschreibt die API, welche für fotoCH entwickelt wurde und öffentlich
genutzt werden kann.
Diese API wird vom neuen AngularJS basierten Frontend benutzt:
 [https://de.foto-ch.ch/](https://de.foto-ch.ch/)
Zu inhaltlichen Aspekten verweisen wir auf das [foto-ch-Handbuch](http://foto-ch.ch/?a=hilfe&lang=de).
Die API wird ständig weiterentwickelt. Künftige Änderungungen sollten mit dieser Version kompatibel sein.
Nötige Anpassungen und Fehlerbehebungen können aber vorkommen. 

### Allgemeine Infos
Alle API Aufrufe in diesem Dokument sind relativ zu **API-URL: [https://de.foto-ch.ch/api/](https://de.foto-ch.ch/api/)**. Die Daten werden im JSON Format zurückgegeben.

### Sprache 
Mit dem Parameter lang kann angegeben werden, in welcher Sprache der Content gewünscht wird

### Entitäten
Folgende Objekttypen können als Liste oder Einzelsatz abgerufen werden:
- Fotografen: photographer _dies ist die default-Entität und muss nicht angegeben werden._
- Suche: streamsearch
- Institutionen: institution
- Bestände: inventory
- Fotos: foto
- Ausstellungen: exhibition
- Literatur: literature
- Arbeitsorte: orte
- Arbeitsperioden: perioden
- **Aufruf einer Liste:** (api/?a=entity[parameters])
- **Aufruf eines Einzelsatzes:** (api/?a=entity&id=123[parameters])

### Streamsearch
 Gibt mehrere JSON Objekte als gestreamte Anworten zurück.Wird für Listenansichten und Suchen verwendet
 Erlaubte Parameter:
- `query` Suchparameter
- `limit` Limitiert die Anzahl Resultate
- `photolimit` (Reduziert bei einer kompletten Suche die Anzahl Fotos speziell)
- `offset`Definiert ein Offset für die Anfrage
- `type` sucht nur nach einem spezifischen Typen. Erlaubte Werte:
    - `'photographer', 'stock', 'institution', 'exhibition', 'literature', 'photos'`
- `sort` Feld, nachdem sortiert werden soll
- `sortdirection` ASC oder DESC
- `direct` Direkte Suchanfrage auf ein spezifisches Feld, Beispiel:
    -  `?...&direct=institution.ort:Aarau,institution.kanton:AG`

##### Beispiele
- [?a=streamsearch&query=test&limit=8&photolimit=20](https://de.foto-ch.ch/api/?a=streamsearch&query=test&limit=8&photolimit=20)
- [?a=streamsearch&type=institution&limit=30&offset=0&direct=institution.kanton:AI](https://de.foto-ch.ch/api/?a=streamsearch&type=institution&limit=30&offset=0&direct=institution.kanton:AI)
- [?a=streamsearch&type=stock&limit=30&offset=0&direct=institution.ort:Krauchthal&query=delacour](https://de.foto-ch.ch/api/?a=streamsearch&type=stock&limit=30&offset=0&direct=institution.ort:Krauchthal&query=delacour)

##### Beispiel um die gestreamte Antwort zu verarbeiten (Beispiel Angular $http):
```js
$http({
    method: "GET",
    url: "https://de.foto-ch.ch/api/?a=streamsearch&query=apfel",
    headers: {
       'Content-Type': "text/plain"
    },
    transformResponse: [function (data) {return data;}],
    onProgress: function(event) {
        try {
            var response = event.currentTarget.responseText;
            response = response.replace(/}{/g, "},{"); // Fehlende Komma's zwischen den Resultaten
            response = "[" + response + "]"; // Zu Array "umwandeln"
            var newresult = JSON.parse(response); // Nun gültiges JSON parsen
            newresult = newresult[newresult.length-1]; // Letzte Antwort extrahieren
            console.log(newresult);
        } catch (e) {
            console.log(e);
        }
    }
}).then(function(e) {
    // done
});
```

### Filters
Kann verwendet werden um von gewissen Feldern die "DISTINCT" Werte zu erhalten, um entsprechende Filterungen auf Inhalte und "direct" Queries für die Streamsearch durchzuführen.

Teilweise werden nur Texte, Teilweise ein Assoziative Array mit ID und Value zurückgegeben, Wird ID & Value zurückgegeben, muss die direct Search in der Stream Query mit der ID durchgeführt werden.

Query Beispiel: [/?a=filters&type=bildgattungen](https://de.foto-ch.ch/api/?a=filters&type=bildgattungen)

##### Paremeter
- `type` Auswahl des gewünschten Typen. Erlaubte Werte:
  - `fotografengattungen`
  - `bildgattungen`
  - `fotografen_kanton`
  - `institution_kanton`
  - `institution_ort`
  - `ausstellung_jahr`
  - `ausstellung_typ`
  - `ausstellung_ort`
  - `literatur_jahr`
  - `literatur_ort`
  - `photo_stichworte`
  - `photo_stocks`

### Statistiken
- **Beschreibung:** Gibt gewisse statistische Werte zurück. Zur Zeit noch sehr limitierte Antwort.
- **Aufruf:** [/?a=statistics](https://de.foto-ch.ch/api/?a=statistics)

### Liste aller Fotografen
- **Beschreibung:** Gibt eine Liste aller Fotografen zurück.
- **Aufruf:** [/?a=photographer](https://de.foto-ch.ch/api/?a=photographer)

#### Parameter
- **Nach Anfangsbuchstaben E filtern:** [/?a=photographer&anf=E](https://de.foto-ch.ch/api/?a=photographer&anf=E)

### Detaildaten eines Fotografen
- **Beschreibung:** Gibt ein Liste mit Detailinfos eines Fotografen zurück
- **Aufruf:** [/?a=photographer&id=1068](https://de.foto-ch.ch/api/?a=photographer&id=1068)

### Aktualisierte Fotografen
- **Beschreibung:** Gibt eine Liste von Fotografen zurück, welche zuletzt bearbeitet wurde.
- **Aufruf:** [/?recent=10](https://de.foto-ch.ch/api/?recent=10)

## Institutionen
### Liste aller Institutionen
- **Beschreibung:** Gibt eine Liste aller Institutionen zurück.
- **Aufruf:** [/?a=institution](https://de.foto-ch.ch/api/?a=institution)

#### Parameter
- **Nach Anfangsbuchstaben E filtern:** [/?a=institution&anf=E](https://de.foto-ch.ch/api/?a=institution&anf=E)

### Detaildaten einer Institution
- **Beschreibung:** Gibt eine Liste mit Detailinfos einer Institution zurück
- **Aufruf:** [/?a=institution&id=300](https://de.foto-ch.ch/api/?a=institution&id=300)

## Ausstellungen
### Liste mit allen Ausstellungen
- **Beschreibung:** Gibt eine Liste mit allen Ausstellungen zurück.
- **Aufruf:** [/?a=exhibition](https://de.foto-ch.ch/api/?a=exhibition)

#### Parameter
- **Nach Anfangsbuchstaben E filtern:** [/?a=exhibition&anf=E](https://de.foto-ch.ch/api/?a=exhibition&anf=E)

### Detaildaten einer Ausstellung
- **Beschreibung:** Gibt ein Liste mit Detailinfos einer Ausstellung zurück
- **Aufruf:** [/?a=exhibition&id=6711](https://de.foto-ch.ch/api/?a=exhibition&id=6711)

## Bestände
### Liste aller Bestände
- **Beschreibung:** Gibt eine Liste aller Bestände zurück.
- **Aufruf:** [/?a=inventory](https://de.foto-ch.ch/api/?a=inventory)

#### Parameter
- **Nach Anfangsbuchstaben E filtern:** [/?a=inventory&anf=E](https://de.foto-ch.ch/api/?a=inventory&anf=E)

### Detaildaten eines Bestandes
- **Beschreibung:** Gibt ein Liste mit Detailinfos eines Bestandes zurück
- **Aufruf:** [/?a=inventory&id=128](https://de.foto-ch.ch/api/?a=inventory&id=129)

## Fotoportal
### Liste aller Fotos
- **Beschreibung:** Gibt eine Liste aller Fotos zurück.
- **Aufruf:** [/?a=photo](https://de.foto-ch.ch/api/?a=photo)

#### Parameter
- **Fotos eines bestimmten Fotografen:** [/?a=photo&photographer=1541](https://de.foto-ch.ch/api/?a=photo&photographer=1541)
- **Fotos eines bestimmten Bestandes:** [/?a=photo&inventory=234](https://de.foto-ch.ch/api/?a=photo&inventory=234)
- **Fotos eines bestimmten Bestandes:** [/?a=photo&institution=35](https://de.foto-ch.ch/api/?a=photo&institution=35)

### Detaildaten eines Bildes
- **Beschreibung:** Gibt die Detaildaten eines Bildes zurück.
- **Aufruf:** [/?a=photo&id=9969](https://de.foto-ch.ch/api/?a=photo&id=9969)

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

### Detaildaten eines Arbeitsortes
- **Beschreibung:** Gibt die Fotografen, die an diesem Ort gearbeitet haben wieder
- **Aufruf:** [/?a=perioden&id=4](https://de.foto-ch.ch/api/?a=perioden&id=4)

## Versionsübersicht
- 23.06.2016 - 1.1.0: Ergänzungen Filters, Streamsearch & Statistics, Silas Mächler
- 30.05.2016 - 1.0.0: Schlusskorrekturen, Markus Schürpf
- 19.05.2016 - 0.9.1: Ergänzungen und Erweiterungen (Suche, Georeferenzen), Christian Schweingruber
- 18.05.2016 - 0.9.0: Vorbereitung zur Publikation, Christian Schweingruber
- 24.04.2015 - 0.0.5: Ausstellung und Fotografen aktualisiert, Stefan Pfister.
- 23.03.2015 - 0.0.4: Ausstellung hinzugefügt, Stefan Pfister.
- 18.03.2015 - 0.0.3: API-Aufrufe verlinken; API für statische Inhalte und Partner hinzugefügt, Stefan Pfister.
- 16.03.2015 - 0.0.2: Bestandsfelder aktualisiert und Sprach-API hinzugefügt, Stefan Pfister.
- 13.03.2015 - 0.0.1: Initiale Version, Stefan Pfister.
