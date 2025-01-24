<?php

final class Timer
{
    private static ?Timer $instance = null;
    private array $timers = [];

    /**
     * gets the instance via lazy initialization (created on first usage)
     */
    public static function getInstance(): Timer
    {
        if (self::$instance === null) {
            self::$instance = new self();
        }

        return self::$instance;
    }

    public function start_timer(string $timer_name, $instance): void
    {
        $this->timers[$timer_name][$instance]['start'] = microtime(true);
    }

    public function stop_timer(string $timer_name, $instance): void
    {
        if (isset($this->timers[$timer_name][$instance])) {
            $this->timers[$timer_name][$instance]['end'] = microtime(true);
        } else {
            throw new InvalidArgumentException('Timer not found: ' . $timer_name);
        }
    }

    public function get_time_deltas(): array
    {
        $deltas = [];
        foreach ($this->timers as $timer_name => $timer) {
            foreach ($timer as $instance) {
                $delta = $this->compute_delta($instance);
                if (is_float($delta)) {
                    $deltas[$timer_name][] = $delta * 1000;
                }
            }
        }
        $res = [];
        foreach ($deltas as $timer_name => $data) {
            $res[$timer_name]['avg'] = array_sum($data)/count($data);
            $res[$timer_name]['std_dev'] = $this->standard_deviation($data);
            $res[$timer_name]['data'] = $data;
        }
        return $res;
    }

    //src: https://www.geeksforgeeks.org/php-program-find-standard-deviation-array/
    private function standard_deviation($array): float
    {
        $num_of_elements = count($array);

        $variance = 0.0;

                // calculating mean using array_sum() method
        $average = array_sum($array)/$num_of_elements;

        foreach($array as $i)
        {
            // sum of squares of differences between
                        // all numbers and means.
            $variance += pow(($i - $average), 2);
        }

        return (float)sqrt($variance/$num_of_elements);
    }

    private function compute_delta($data)
    {
        if (isset($data['start']) && isset($data['end'])) {
            return $data['end'] - $data['start'];
        }
        return false;
    }

    /**
     * is not allowed to call from outside to prevent from creating multiple instances,
     * to use the singleton, you have to obtain the instance from Singleton::getInstance() instead
     */
    private function __construct()
    {
    }

    /**
     * prevent the instance from being cloned (which would create a second instance of it)
     */
    private function __clone()
    {
    }

    /**
     * prevent from being unserialized (which would create a second instance of it)
     */
    public function __wakeup()
    {
        throw new Exception("Cannot unserialize singleton");
    }
}
