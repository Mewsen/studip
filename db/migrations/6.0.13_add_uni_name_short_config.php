<?php

final class AddUniNameShortConfig extends Migration
{
    public function description()
    {
        return 'Adds the configuration UNI_NAME_SHORT.';
    }

    public function up()
    {
        Config::get()->create("UNI_NAME_SHORT", array(
            'value' => "",
            'type' => "string",
            'range' => "global",
            'section' => "global",
            'description' => "Kurze Name der Stud.IP-Installation bzw. Hochschule."
        ));
    }

    public function down()
    {
        Config::get()->delete("UNI_NAME_SHORT");
    }
}
