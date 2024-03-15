# Stud.IP v6.0

**15.03.2024**

## Neue Features

- 

## Breaking changes

- Im Rahmen von [Issue #3788](https://gitlab.studip.de/studip/studip/-/issues/3788) wurden die Zusätze an allen Icons entfernt. Dadurch kann es sein, dass manche Plugins nicht mehr erscheinen. Diese müssen dann auf eine Variante ohne Zusätze umgestellt werden.
- Die Funktion `get_config()` wurde entfernt. Stattdessen muss die Methode `Config::get()->getValue('CONFIG_KEY')` bzw. der Shortcut `Config::get()->CONFIG_KEY` verwendet werden. ([Issue #2797](https://gitlab.studip.de/studip/studip/-/issues/2797))
- Die Funktion `smile()` wurde entfernt. Sie kann ersatzlos entfernt werden. ([Issue #3158](https://gitlab.studip.de/studip/studip/-/issues/3158))
- Die Funktion `transformBeforeSave()` wurde entfernt. Sie kann ersatzlos entfernt werden. ([Issue #3159](https://gitlab.studip.de/studip/studip/-/issues/3159))
- Die schon lange nicht mehr genutzten Methoden zum Setzen, Auslesen und Enfernen von Schmuckgrafiken von Bildern für die Sidebar wurde entfernt. Die Methoden `Sidebar::setImage()`, `Sidebar::getImage()` sowie `Sidebar::removeImage()` müssen ersatzlos entfernt werden. ([Issue #3157](https://gitlab.studip.de/studip/studip/-/issues/3157))
- Der zweite Parameter für die Methode `Navigation::setImage()` wurde entfernt. Der Parameter schien sich auf das Bild zu beziehen, hat aber Attribute an dem Link gesetzt. Stattdessen muss die Methode `Navigation::setLinkAttributes()` verwendet werden. ([Issue #3578](https://gitlab.studip.de/studip/studip/-/issues/3578))
- Die Unterstützung für LESS-Stylsheets in Plugins wurde entfernt. Als Alternative wird SCSS unterstützt. ([Issue #2720](https://gitlab.studip.de/studip/studip/-/issues/2720)) 

## Security related issues

-

## Deprecated Features

- 

## Known Issues

- 
