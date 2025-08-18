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

    /**
     * Creates a "standard" calendar with typical functions and
     * behavior for Stud.IP.
     *
     * @return \Studip\Fullcalendar The created calendar instance.
     */
    public static function createSimpleCalendar(
        string $data_url,
        array $views = ['timeGridWeek'],
        string $initial_view = 'timeGridWeek',
        int $start_hour = 8,
        int $end_hour = 20,
        ?\DateTime $initial_date = null,
        bool $show_weekends = false,
        bool $show_all_day_slot = false,
        bool $write_permissions = false,
        string $add_entry_url = '',
        array $resources = []
    )
    {
        $fullcalendar_views = [];
        foreach ($views as $view) {
            if ($view === self::VIEW_MONTH) {
                $fullcalendar_views['dayGridMonth'] = [
                    'eventTimeFormat' => ['hour' => 'numeric', 'minute' => '2-digit'],
                    'titleFormat'     => ['year' => 'numeric', 'month' => 'long'],
                    'displayEventEnd' => true
                ];
            } elseif ($view === self::VIEW_WEEK) {
                $fullcalendar_views['timeGridWeek'] = [
                    'columnHeaderFormat' => ['weekday' => 'short', 'year' => 'numeric', 'month' => '2-digit', 'day' => '2-digit', 'omitCommas' => true],
                    'weekends'           => $show_weekends,
                    'titleFormat'        => ['year' => 'numeric', 'month' => '2-digit', 'day' => '2-digit'],
                    'slotDuration'       => $slot_durations['week']
                ];
            } elseif ($view === self::VIEW_DAY) {
                $fullcalendar_views['timeGridDay'] = [
                    'columnHeaderFormat' => ['weekday' => 'long', 'year' => 'numeric', 'month' => '2-digit', 'day' => '2-digit', 'omitCommas' => true],
                    'titleFormat'        => ['year' => 'numeric', 'month' => '2-digit', 'day' => '2-digit'],
                    'slotDuration'       => $slot_durations['day']
                ];
            } elseif ($view === self::GROUP_WEEK) {
                $fullcalendar_views['resourceTimelineWeek'] = [
                    'columnHeaderFormat' => ['weekday' => 'long', 'year' => 'numeric', 'month' => '2-digit', 'day' => '2-digit', 'omitCommas' => true],
                    'titleFormat'        => ['year' => 'numeric', 'month' => '2-digit', 'day' => '2-digit'],
                    'weekends'           => $show_weekends,
                    'slotDuration'       => $slot_durations['week_group']
                ];
            } elseif ($view === self::GROUP_DAY) {
                $fullcalendar_views['resourceTimelineDay'] = [
                    'columnHeaderFormat' => ['weekday' => 'long', 'year' => 'numeric', 'month' => '2-digit', 'day' => '2-digit', 'omitCommas' => true],
                    'titleFormat'        => ['year' => 'numeric', 'month' => '2-digit', 'day' => '2-digit'],
                    'slotDuration'       => $slot_durations['day_group']
                ];
            }
        }
        return new \Studip\Fullcalendar(
            '',
            [
                'eventSources' => [$data_url],
                'views'        => $fullcalendar_views,
                'initialView'  => $initial_view,
                'weekNumbers'  => true,
                'initialDate'  => $initial_date ? $initial_date->format('Y-m-d') : date('Y-m-d'),
                'header'      => [
                    'start'   => array_keys($fullcalendar_views),
                    'center'  => 'title',
                    'end'     => 'prev,today,next'
                ],
                'allDaySlot'  => $show_all_day_slot,
                'addDayText'  => '',
                'slotMinTime' => sprintf('%02u:00', $start_hour),
                'slotMaxTime' => sprintf('%02u:00', $end_hour),
                'selectable'  => $write_permissions && !empty($add_entry_url),
                'editable'    => $write_permissions,
                'dialog_size' => 'auto',
                'timeGridEventMinHeight' => 20
                //TODO: resources, resource titles
            ]
        );
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
        return $template->render(
            [
                'title' => $this->title,
                'config' => $this->config,
                'attributes' => array_merge(
                    $this->attributes,
                    [$real_data_name => '1']
                )
            ]
        );
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
