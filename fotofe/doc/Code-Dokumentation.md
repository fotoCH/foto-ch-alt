# Code Dokumentation
Dieses Dokument beschreibt die eingesetzten Technologien, sowie die Struktur der Applikation.

## Verwendete Technologien
Die Applikation besteht im Wesentlichen aus einem Backend, welches die Daten aus der Datenbank via [API](API-Dokumentation.html) dem Frontend zur Verfügung stellt. Die Daten werden anschliessend mit Javascript in HTML umgewandelt und im Browser dargestellt.

**Frontend**
- [AngularJS](https://angularjs.org/)
- [jQuery](http://jquery.com/)
- [Less pre-processor](http://lesscss.org/)

**Backend**
- PHP (Symfony? Silex?)
- Die API liefert die Daten im JSON Format aus

**Datenbank**
- MySQL
- Verwaltung via [phpMyAdmin](https://filos.catatec.ch/phpMyAdmin/index.php)

## Code-Verwaltung
Der Code wird in einem [SVN-Repository](svn://svn.catatec.ch/foto-ch) verwaltet.

## Frontend
Der Frontend-Code (HTML/CSS) stützt sich auf dem Styleguide von [Code Guide](http://codeguide.co/).

### Browser-Unterstützung
Es werden alle modernen Browser unterstützt, der Internet Explorer ab Version 9. Generell wird die Methode der [progressiven Verbesserung](http://de.wikipedia.org/wiki/Progressive_Verbesserung) angewendet.  
Crossbrowser-Testing lässt sich einfach durchführen mit [Browserstack](http://www.browserstack.com/screenshots).

### Ordnerstruktur
Die Ordnerstruktur orientiert sich an den Best-Practices von diesem [Artikel](https://scotch.io/tutorials/angularjs-best-practices-directory-structure).
```
app/
----- components/   // each component is treated as a mini Angular app
---------- home/
--------------- homeController.js
--------------- homeService.js
--------------- homeView.html
---------- blog/
--------------- blogController.js
--------------- blogService.js
--------------- blogView.html
----- shared/   // Beinhaltet 
---------- sidebar/
--------------- sidebarDirective.js
--------------- sidebarView.html
---------- article/
--------------- articleDirective.js
--------------- articleView.html
----- app.controllers.js
----- app.directives.js
----- app.module.js
----- app.routes.js
assets/
----- css/      // All styles and style related files (SCSS or LESS files)
--------------- less/      // Less Dateien
----- font/     // Webschriften
----- js/       // JavaScript files written for your app that are not for angular
----- img/      // Bilder, Logos, Icons, etc.
----- libs/     // Third-party libraries such as jQuery, Moment, Underscore, etc.
index.html
```

### AngularJS
Verwendete Module:
- angular-ui-router
- angucomplete
- TODO

Directiven:
- TODO

### CSS
- normalize.css

TODO
- LESS Hat (http://lesshat.madebysource.com/)
- Less-Struktur erklären
- Mobile First approach for Media Queries
- Layout-Definitions are prefixed with „l-"
- Typography: using rem
- normalize.css
- Code style
  - CodeGuide (http://codeguide.co/) and Sass Styleguide (http://www.sitepoint.com/css-sass-styleguide/)
  - BEM CSS Structure (http://csswizardry.com/2013/01/mindbemding-getting-your-head-round-bem-syntax/)
  - Less Structure:
```css
.element {
  $scoped-variable: whatever;
  @extend .other-element;
  @include mixin($argument);
  property: value;
 
  &:pseudo {
    /* styles here */
  }
 
  .nested {
    /* styles here */
  }
 
  @include breakpoint($size) {
    /* styles here */
  }
}
```

The different items in a Sass rule set go in the following order:
Scoped variables
Selector extensions with @extend
Mixin inclusions with @include with the exception of media-query stuff
Regular property: value pairs
Pseudo-class/element nesting with & after an empty line
Regular selector nesting after an empty line
Media query stuff (with or without a mixin)




## Versionsübersicht
- 19.03.2015 - 0.0.1: Initiale Version, Stefan Pfister