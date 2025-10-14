<?php

class RemoveWikiQuicklinks extends Migration
{
    public function description(): string
    {
        return 'Removes quicklink steps from wiki helptours.';
    }

    public function up(): void
    {
        DBManager::get()->exec(
            "DELETE FROM `help_tour_steps`
            WHERE (`tour_id` = '4d41c9760a3248313236af202275107b' OR `tour_id`= '5d41c9760a3248313236af202275107b')
            AND (`step` = 7 OR `step` = 8)"
        );
    }

    public function down(): void
    {
        DBManager::get()->exec(
            "INSERT INTO `help_tour_steps` VALUES
            ('4d41c9760a3248313236af202275107b',7,'QuickLinks','Dieser Bildschirmbereich zeigt eine Liste von QuickLinks (Verweisen) auf Wiki-Seiten. Ein Klick auf einen QuickLink öffnet die korrelierende Wiki-Seite. Deren Inhalt lässt sich mit Hilfe der Schaltflächen \"Bearbeiten\" und \"Löschen\" gestalten.','R',0,'#layout-sidebar SECTION:eq(0)  DIV:eq(6)  DIV:eq(0)','wiki.php','','','',1441276241,0),
            ('4d41c9760a3248313236af202275107b',8,'QuickLinks bearbeiten','Über das Icon zum Bearbeiten von QuickLinks öffnet sich ein Editor.\r\n\r\nNeue QuickLinks lassen sich mit doppelten eckigen Klammern erstellen: [[Name]]. Das Löschen eines QuickLinks entfernt die korrelierende Seite aus der Liste.','R',0,'#layout-sidebar SECTION:eq(0)  DIV:eq(6)  DIV:eq(0)','wiki.php','','','root@localhost',1441276241,0),
            ('5d41c9760a3248313236af202275107b',7,'QuickLinks','This box displays links, leading to other Wiki pages. Selecting a link will forward to the related page.','R',0,'#layout-sidebar SECTION:eq(0)  DIV:eq(6)  DIV:eq(0)','wiki.php','','','',1441276241,0),
            ('5d41c9760a3248313236af202275107b',8,'Editing QuickLinks','A click on this icon will open an editor to edit the QuickLinks.\r\n\r\nEntering a name within double square brackets like [[name]] in the editor will create a new QuickLink leading to a correlating page. Deleting a QuickLink will cause its deletion in the QuickLink box.','R',0,'#layout-sidebar SECTION:eq(0)  DIV:eq(6)  DIV:eq(0)','wiki.php','','','root@localhost',1441276241,0)"
        );
    }
}
