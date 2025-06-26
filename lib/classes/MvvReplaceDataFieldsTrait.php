<?php
trait MvvReplaceDataFieldsTrait
{
    /**
     * The id of the section (Studiengangteil-Abschnitt) with replaced or
     * added fields (defined as data fields) in all related objects.
     *
     * @var string|null $replace_df_abschnitt_id
     */
    private string|null $replace_df_abschnitt_id = null;

    /**
     * Set the section (Studiengangteil-Abschnitt) with replaced or
     * added fields (defined as data fields) for all related objects.
     *
     * @param Stgteilabschnitt $abschnitt The section.
     * @return int The number of objects related to the given section.
     */
    public function setReplaceDfAbschnitt(Stgteilabschnitt $abschnitt): int
    {
        $this->replace_df_abschnitt_id = $abschnitt->id;
        return 1;
    }

    /**
     * Returns the replaced value for the given field and section id.
     *
     * @param string $field
     * @param string|null $abschnitt_id
     * @return string
     */
    public function getReplacedValue(
        string $field,
        string $abschnitt_id = null
    ): string
    {
        $abschnitt_id = $abschnitt_id ?? $this->replace_df_abschnitt_id;
        if (is_null($abschnitt_id)) {
            return $this->getValue($field) ?? '';
        }

        if (!in_array($field, array_keys(static::db_fields()))) {
            throw new InvalidArgumentException(static::class . '::' . $field . ' not found.');
        }
        // find data field that replaces the given local field
        $data_field_id = static::getReplaceDataFieldsId($field, $this->abschnitt_assignments->getClassName());
        if ($data_field_id) {
            $abschnitt = $this->abschnitt_assignments->findOneBy('abschnitt_id', $abschnitt_id);
            if (!$abschnitt) {
                return $this->getValue($field) ?? '';
            }
            $data_field = $abschnitt->datafields->findOneBy('datafield_id', $data_field_id);
            if (mb_strlen($data_field->getValue('content'))) {
                $tdf = $data_field->getTypedDatafield();
                return $tdf->getDisplayValue();
            }
        }
        return $this->getValue($field) ?? '';
    }

    /**
     * Retrieves the id of the related data field.
     *
     * @param string $field The field to search for a related data field.
     * @param string $related_class The name of the related class.
     * @return string|null The id of the data field. Null if no related data field.
     */
    private static function getReplaceDataFieldsId(
        string $field,
        string $related_class
    ): string|null
    {
        static $data_fields = null;

        if (is_null($data_fields)) {
            DataField::findEachBySQL(
                function (DataField $f) use (&$data_fields) {
                    $data_fields[$f->object_class] = $f->datafield_id;
                },
                "`object_type` = ? AND `object_class` IN(?)",
                [
                    strtolower($related_class),
                    array_keys(static::db_fields())
                ]
            );
            $data_fields = $data_fields ?: [];
        }
        return $data_fields[$field] ?? null;
    }
}
