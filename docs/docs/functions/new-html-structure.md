---
id: new-html-structure
title: HTML-Struktur
sidebar_label: HTML-Struktur
---
Über die Jahre ist die grundlegende HTML-Struktur einer Stud.IP-Seite quasi organisch gewachsen und ist an vielen Stellen nicht gut zur Unterstützung der Barrierefreiheit geeignet. Ab Stud.IP 5.3 bekommt Stud.IP eine neue Struktur seiner Seiten, die semantischer daran orientiert ist, wofür jeder Bereich gedacht ist.

# Grundlegende Struktur einer Stud.IP-Seite
````html
<html>
    <head>
    </head>
    <body>
        <div id="skip_link_navigation">Skiplinks</div>
        <header id="main-header">
            <div id="top-bar" role="banner">
                <div id="responsive-menu">Hamburgermenü</div>
                <div id="site-title">Globaler Titel der Installation ("Stud.IP")</div>
                <div id="header-links">Dynamische Links (z.B. zum Entwicklerchat), Schnellsuche, Notifications, Avatarmenü</div>
            </div>
            <nav id="navigation-level-1">Hauptnavigation</nav>
            <div id="current-page-structure">
                <nav id="navigation-level-2">Navigation der aktuellen Seite</nav>
                <div id="page-title-container">Titel der aktuellen Seite</div>
            </div>
        </header>
        <aside id="sidebar">
            Sidebar mit Navigation (#navigation-level-3) und sonstigen Widgets
        </aside>
        <main id="content-wrapper">
            Der eigentliche Seiteninhalt
        </main>
        <a id="scroll-to-top">
            Kurzlink zum Springen an den Seitenanfang
        </a>
        <footer id="main-footer">
            <div id="footer-info">
                Informativer Inhalt ("Angemeldet als...", ggf. Debuginfos)
            </div>
            <nav id="footer-navigation">
                Navigationspunkte im Footer (Impressum, Datenschutzerklärung etc.)
            </nav>
        </footer>
    <body>
</html>
````

# Layout
Zur Darstellung der Seitenelemente wird ein Gridlayout verwendet, das aktuell aus 2 Spalten und 3 Zeilen besteht.
- Der gesamte Header bildet Zeile 1, diese erstreckt sich über die ganze Seitenbreite.
- Die Sidebar liegt in Spalte 1, Zeile 2.
- Der Inhalt bildet Spalte 2, Zeile 2.
- Der Footer erstreckt sich ebenfalls über die gesamte Seitenbreite und beansprucht Zeile 3.

# Überführung der alten Struktur in die neue
Der alte Seitenaufbau wurde wie folgt in die neue Struktur überführt:

| Altes Element           | Neues Element            |
|-------------------------|--------------------------|
| #layout_wrapper         | *gibt es nicht mehr*     |
| #barBottomContainer     | #top-bar                 |
| #barBottomLeft          | #responsive-menu         |
| #barTopFont             | #site-title              |
| #barBottomright         | #header-links            |
| #barTopAvatar           | #avatar-menu-container   |
| #notification_container | #notification-container  |
| #header_avatar_menu     | #avatar-menu             |
| #flex-header            | #navigation-level-1      |
| #barTopMenu             | #navigation-level1-items |
| #barTopStudip           | #top-logo                |
| #layout_page            | #current-page-structure  |
| #layout_context_title   | #context-title           |
| .secondary-navigation   | #navigation-level-2      |
| #page_title_container   | #page-title-container    |
| #current_page_title     | #page-title              |
| #layout_container       | *gibt es nicht mehr*     |
| #layout-sidebar         | #sidebar                 |
| section.sidebar         | *gibt es nicht mehr*     |
| #layout_footer          | #main-footer             |
