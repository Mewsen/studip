<?php
class ActivateSemesterRoutes extends Migration
{
    public function description()
    {
        return "Activates all semester routes";
    }

    public function up()
    {
        // Deactivated since the restapi was removed in Stud.IP 6.0

        # require_once 'app/routes/Semester.php';
        # RESTAPI\ConsumerPermissions::get()->activateRouteMap(new RESTAPI\Routes\Semester());
    }
}
