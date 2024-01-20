<?php

namespace si_Email_Writer\Sugar\Helpers;

/**
 * Updates a scheduler job's interval.
 */
class UpdateJob
{
    public static function setInterval($job, $job_interval)
    {
        try {
            $scheduler = \BeanFactory::getBean('Schedulers');
            $scheduler->retrieve_by_string_fields(array('job' => $job, 'deleted' => '0'));
            $scheduler->job_interval = $job_interval;

            if (method_exists($scheduler, 'save')) {
                $scheduler->save();
                return true;
            } else {
                $GLOBALS['log']->fatal("SIEmailWriter Exception: Failed to save " . $scheduler->name . __FILE__ . ":" . __LINE__);
                return false;
            }
        } catch (\Exception $e) {
            $GLOBALS['log']->fatal("si_Email_Writer Error in " . __FILE__ . ":" . __LINE__ . ": " . $e->getMessage());
            return false;
        }
    }
}
