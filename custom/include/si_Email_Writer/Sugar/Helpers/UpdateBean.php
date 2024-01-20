<?php

namespace si_Email_Writer\Sugar\Helpers;

/**
 * This class is responsible for updating the Bean
 */
class UpdateBean
{
    /**
     * This function updates Sugar bean
     * @param string  $beanName name of the bean to be updated
     * @param array $fieldsToUpdate fields to be updated in bean
     * @param array $update_date_modified Whether to update the date modified while saving the beans
     * @access public
     */
    public static function update($beanName, $fieldsToUpdate, $update_date_modified = true)
    {
        if (empty($fieldsToUpdate)) {
            return;
        }

        if (!empty($fieldsToUpdate && !is_array(array_values($fieldsToUpdate)[0])))
            //The array is not nested, for the sake of uniformity, make it nested
            $fieldsToUpdate = [$fieldsToUpdate];

        foreach ($fieldsToUpdate as $data) {
            if (isset($data['id']) && !empty($data['id'])) {
                $bean = \BeanFactory::getBean(ucfirst($beanName), $data['id'], array('disable_row_level_security' => true));
                unset($data['id']);
                foreach ($data as $k => $v) {
                    $bean->$k = $v;
                    if ($k == 'date_modified' || !$update_date_modified)
                        $bean->update_date_modified = false;
                }
                if (method_exists($bean, 'save')) {
                    $bean->save();
                } else {
                    $GLOBALS['log']->fatal("SI Email Writer Exception: Failed to save " . $bean->name . __FILE__ . ":" . __LINE__);
                }
            }
        }
    }
}
