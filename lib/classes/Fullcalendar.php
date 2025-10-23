<?php
namespace Studip;

class Fullcalendar
{
    const VIEW_MONTH = 'timeGridMonth';

    const VIEW_WEEK = 'timeGridWeek';

    const VIEW_DAY = 'timeGridDay';

    const GROUP_WEEK = 'resourceTimelineWeek';

    const GROUP_DAY = 'resourceTimelineDay';


    protected $title;

    /**
     * Fullcalendar configuration options.
     * They are passed to the JavaScript fullcalendar class.
     */
    protected $config;

    /**
     * Additional HTML attributes that shall be attached to the
     * section element in which the fullcalendar instance is created.
     */
    protected $attributes;

    /**
     * The name of the fullcalendar for the data attribute. This is set
     * to "fullcalendar" by default, but custom fullcalendars may require
     * special data attributes to prevent the default Fullcalendar JS
     * initialiser to be executed.
     */
    protected $data_name;

    public static function create(
        $title = '',
        $config = [],
        $attributes = [],
        $data_name = 'fullcalendar'
    )
    {
        $instance = new \Studip\Fullcalendar(
            $title,
            $config,
            $attributes,
            $data_name
        );

        return $instance->render();
    }

    public function __construct(
        $title = '',
        $config = [],
        $attributes = [],
        $data_name = 'fullcalendar'
    )
    {
        $this->title = $title;
        $this->config = $config;
        $this->attributes = $attributes;
        $this->data_name = $data_name;
    }

    public function setDefaultView(?string $view): void
    {
        if ($view === null) {
            unset($this->config['defaultView']);
        } else {
            $this->config['defaultView'] = $view;
        }
    }

    public function setResponsiveDefaultView(?string $view): void
    {
        if ($view === null) {
            unset($this->config['responsiveDefaultView']);
        } else {
            $this->config['responsiveDefaultView'] = $view;
        }
    }

    public function render()
    {
        $factory = new \Flexi\Factory($GLOBALS['STUDIP_BASE_PATH'] . '/templates');
        $template = $factory->open('studip-fullcalendar.php');
        $real_data_name = sprintf('data-%s', $this->data_name);

        //Move the Stud.IP parts of the configuration out of the
        //fullcalendar configuration:
        $fullcalendar_config = $this->config;
        $template_params     = [
            'title'      => $this->title,
            'attributes' => array_merge(
                $this->attributes,
                [$real_data_name => '1']
            ),
            'dialog_size'       => 'auto',
            'action_urls'       => [],
            'display_holidays'  => true,
            'display_vacations' => true
        ];
        if (is_array($fullcalendar_config['studip_urls'])) {
            $template_params['action_urls'] = $fullcalendar_config['studip_urls'];
            unset($fullcalendar_config['studip_urls']);
        }
        if (!empty($fullcalendar_config['dialog_size'])) {
            $template_params['dialog_size'] = $fullcalendar_config['dialog_size'];
            unset($fullcalendar_config['dialog_size']);
        }
        if (!empty($fullcalendar_config['display_holidays'])) {
            $template_params['display_holidays'] = $fullcalendar_config['display_holidays'];
            unset($fullcalendar_config['display_holidays']);
        }
        if (!empty($fullcalendar_config['display_vacations'])) {
            $template_params['display_vacations'] = $fullcalendar_config['display_vacations'];
            unset($fullcalendar_config['display_vacations']);
        }
        $template_params['config'] = $fullcalendar_config;

        return $template->render($template_params);
    }

    /**
     * Creates an array with data for a Fullcalendar instance
     * from Stud.IP objects that implement the EventSource interface.
     */
    public static function createData($objects = [], $begin = null, $end = null)
    {
        if (!count($objects)) {
            //No data means there is nothing to do.
            return [];
        }

        $data = [];

        foreach ($objects as $object) {
            if ($object instanceof \Studip\Calendar\EventSource) {
                $events = $object->getFilteredEventData(
                    $GLOBALS['user']->id, null, null, $begin, $end
                );

                foreach ($events as $event) {
                    $data[] = $event->toFullcalendarEvent();
                }
            }
        }
        return $data;
    }
}
