<?php
# Lifter002: DONE
# Lifter007: TEST

/**
 * @author  Jan-Hendrik Willms <tleilax+studip@gmail.com>
 * @author  Marcus Lunzenauer <mlunzena@uos.de>
 * @author  Martin Gieseking  <mgieseki@uos.de>
 * @license GPL2 or any later version
 */
class DataFieldTimeEntry extends DataFieldEntry
{
    protected $template = 'time.php';

    /**
     * Returns whether the datafield contents are valid
     *
     * @return boolean indicating whether the datafield contents are valid
     */
    public function isValid()
    {
        $value = trim($this->value);

        if (!$value) {
            return parent::isValid();
        }

        $parts = explode(':', $value);

        return parent::isValid()
            && $parts[0] >= 0 && $parts[0] <= 24
            && $parts[1] >= 0 && $parts[1] <= 59;
    }
}
