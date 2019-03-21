# API Dokumentation interne Teile
Dieses Dokument beschreibt die API, welche für fotoCH entwickelt wurde.

## Allgemeine Infos
Alle API Aufrufe in diesem Dokument sind relativ zu **API-URL: [https://www2.foto-ch.ch/api/](https://www2.foto-ch.ch/api/)**. Die Daten werden im JSON Format zurückgegeben.

## Sprache 
Mit dem Parameter lang kann angegeben werden, in welcher Sprache der Content gewünscht wird

## UI-Strings / Label / Sprache
Mit diesem Teil der API können sämtliche UI-Strings aus der Datenbank abgerufen werden.
- **[/?a=sprache&lang=de](https://www2.foto-ch.ch/api/?a=sprache&lang=de)** (Deutsch)

## Statische Inhalte
Lieferte Inhalte für statischen Seiten, z.B. Über uns.
- **[/?a=pages&lang=de](https://www2.foto-ch.ch/api/?a=pages&lang=de)** (Deutsch)

## Partner
Liefert Infos zu den Partner-Organisationen sowie den Dateinamen der Logos. Die Logos sind lokal auf dem Server gespeichert.
- **[/?a=partner&lang=de](https://www2.foto-ch.ch/api/?a=partner&lang=de)** (Deutsch)



## Versionsübersicht
- 18.05.2016 - 0.9.0: Vorbereitung zur Publikation
- 24.04.2015 - 0.0.5: Ausstellung und Fotografen aktualisiert, Stefan Pfister.
- 23.03.2015 - 0.0.4: Ausstellung hinzugefügt, Stefan Pfister.
- 18.03.2015 - 0.0.3: API-Aufrufe verlinken; API für statische Inhalte und Partner hinzugefügt, Stefan Pfister.
- 16.03.2015 - 0.0.2: Bestandsfelder aktualisiert und Sprach-API hinzugefügt, Stefan Pfister.
- 13.03.2015 - 0.0.1: Initiale Version, Stefan Pfister.
