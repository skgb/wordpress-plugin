SKGB Wordpress Plug-in
======================

Diese Software wird seit April 2021 nicht mehr durch den Autor
unterstützt. Sie ist aber unter einer [freien Lizenz][] verfügbar,
so dass du sie forken, verändern und weiterverwenden kannst.

Das Plug-in implementierte solche Aspekte des Verhaltens der
[SKGB][]-Wordpress-Site, die selbst dann beibehalten werden sollten,
wenn sich Grafikdesign etc. durch Austausch des „Theme“ verändern
*(separation of behaviour from both content and visualisation).*

[freien Lizenz]: https://github.com/skgb/wordpress-plugin/blob/master/LICENSE
[SKGB]: https://www.skgb.de/


Features
--------

- Hervorhebung des Suchbegriffs auf Ergebnis-Seiten nach
  Benutzung der Volltext-Suche
- Unterstützung von [Markdown][]:
  - Deaktivierung des veralteten „visuellen Editors“ von Wordpress < 5
    (notwendig, weil dieser nicht stabil arbeitet und
    nicht im Wechsel mit Markdown funktioniert)
  - Dashboard-Box mit Link zu Markdown-Handbuch
- Umsetzung des URL-Designs:
  - keine Protokoll-absoluten Links zur eigenen Site in die Datenbank schreiben
  - `/category/` entfernen
  - `/category/allgemein` ersetzen durch `/`
- Förderung eines einheitlichen Erscheinungsbilds durch automatische
  Korrektur einzelner besonders häufiger Rechtschreibfehler etc.
- Wordpress-Werkzeug zur Einsichtnahme in bestimmte Teile der
  Server-Konfiguration (read-only)
- von Wordpress generierte HTML-Ausgabe aufräumen;
  insb. Umwandlung von XHTML-Syntax in HTML-Syntax
  (geschrieben für Wordpress 2.8, wäre heute dank HTML5-Parsern entbehrlich)
- Debugging-Hilfe: Stacktrace für deprecated Funktionen

[Markdown]: https://de.wikipedia.org/wiki/Markdown


Installation
------------

```sh
cd wp-content/plugins
git clone https://github.com/skgb/wordpress-plugin.git skgb-web

# Um Änderungen über die Wordpress-GUI zu ermöglichen,
# muss www-data Schreibrechte haben – zum Beispiel:
chgrp -R www-data skgb-web
chmod -R g+w skgb-web
```

Ein GitHub `clone` ermöglicht einfaches Aktualisieren durch `git pull`
(nach `git reset --hard`, falls nötig). Manuelle Installation ist
natürlich ebenfalls möglich, Installation über das
Wordpress–Plug-in–Repository jedoch nicht.

Dieses Plug-in sollte gemeinsam mit folgenden Plug-ins verwendet werden:

- [Markdown Extra](https://michelf.ca/projects/php-markdown/classic/) 1.2.8
- [Classic Editor](https://wordpress.org/plugins/classic-editor/) 1.6+
- [Classic Editor Addon](https://wordpress.org/plugins/classic-editor-addon/) 2.6.0+

Soll auf „Markdown Extra“ verzichtet werden, so müssen entweder *alle* Inhalte
durch HTML ersetzt werden oder ein anderes Markdown–Plug-in muss eingesetzt
werden ([Suche auf wp.org](https://wordpress.org/plugins/search/Markdown/)).
Siehe auch [Theme-Issue #8](https://github.com/skgb/wordpress-theme/issues/8).


Lizenz
------

Diese Software darf verändert und weiterverwendet werden zu den
Bedingungen der GNU General Public License – [GPL Version 2][]
oder (wahlweise) jeder neueren Version.

Copyright (c) 2009 Segel- und Kanugemeinschaft Brucher Talsperre e. V. [SKGB][]

Autor: Arne Johannessen, SKGB

[GPL version 2]: https://github.com/skgb/wordpress-plugin/blob/master/LICENSE


To Do
-----

- GUI für `teflon_post`
- Aufräumen der HTML-Ausgabe aktualisieren, insbesondere hinsichtlich des
  Headers (Emoji – Datenschutz?, Kommentare-Feed, …)
- Überarbeitung im Rahmen der Anpassung an Gutenberg, siehe
  [Theme-Issue #8](https://github.com/skgb/wordpress-theme/issues/8);
  insb. müssen alle Markdown-Inhalte ersetzt werden durch Gutenberg-HTML


Changelog
---------

0.5

* Offer read access to server config files

0.4.1

* No autocorrect for absolute links after `:` or `=`

0.4

* Autocorrect absolute links to own site to site-relative links on save
* Autocorrect `Ij` to `IJ`
* SKGB-intern link no longer points to legacy digest directory

0.3.2

* Stacktrace für deprecated function calls in Modulen Dritter

0.3.1

0.3

0.2.2

* moved plugin file to directory and added installation scripts in bin
