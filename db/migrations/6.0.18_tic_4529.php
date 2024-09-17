<?php

final class Tic4529 extends Migration
{
    public function description()
    {
        return 'Delete MAIL_USE_SUBJECT_PREFIX and add MAIL_SUBJECT_PREFIX';
    }

    public function up()
    {
        Config::get()->delete("MAIL_USE_SUBJECT_PREFIX");

        Config::get()->create("MAIL_SUBJECT_PREFIX", array(
            'value' => "[Stud.IP]",
            'type' => "string",
            'range' => "global",
            'section' => "global",
            'description' => "Stellt dem Titel von per Mail versandten Nachrichten, wenn UNI_NAME_CLEAN leer ist."
        ));
    }

    public function down()
    {
        Config::get()->delete("MAIL_SUBJECT_PREFIX");

        Config::get()->create("MAIL_USE_SUBJECT_PREFIX", array(
            'value' => "1",
            'type' => "boolean",
            'range' => "global",
            'section' => "global",
            'description' => "Stellt dem Titel von per Mail versandten Nachrichten den Wert von UNI_NAME_CLEAN voran."
        ));
    }
}
