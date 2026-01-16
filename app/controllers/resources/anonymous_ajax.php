<?php


require_once(__DIR__ . '/BookingPlanDataHelper.php');


class Resources_AnonymousAjaxController extends StudipController
{
    use BookingPlanDataHelper;

    protected $with_session = true;

    protected $allow_nobody = true;


    public function get_booking_plan_action($resource_id)
    {
        $this->renderBookingPlanData($resource_id);
    }

    public function semester_week_action($timestamp)
    {
        $semester = \Semester::findByTimestamp($timestamp);
        if (!$semester) {
            $this->notFound('No semester found for given timestamp');
            return;
        }

        $timestamp = strtotime('today', $timestamp);
        $week_begin_timestamp = strtotime('monday this week', $semester->vorles_beginn);
        $end_date = $semester->vorles_ende;

        $i = 0;
        $result = [
            'semester_name' => (string)$semester->name,
            'week_number' => sprintf(_('KW %u'), date('W', $timestamp)),
            'current_day' => strftime('%x', $timestamp)
        ];
        while ($week_begin_timestamp < $end_date) {
            $next_week_timestamp = strtotime('+1 week', $week_begin_timestamp);
            if ($week_begin_timestamp <= $timestamp && $timestamp < $next_week_timestamp) {
                $result['sem_week'] = sprintf(
                    _('%u. Vorlesungswoche (ab %s)'),
                    $i + 1,
                    strftime('%x', $week_begin_timestamp));
                break;
            }
            $i += 1;

            $week_begin_timestamp = $next_week_timestamp;
        }

        $this->render_json($result);
    }
}
