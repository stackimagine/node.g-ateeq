<?php

namespace si_Email_Writer\Sugar\Helpers;

/**
 * This class provides methods for performing database operations that cannot be achieved through SugarCRM beans.
 */
class DBHelper
{
    /**
     * Executes a custom SQL query and returns the result.
     *
     * @param  string    $query SQL query to be executed
     * @return mixed     Query result or false if an exception occurs
     */
    public static function executeQuery($query)
    {
        try {
            $GLOBALS['log']->debug('si_Email_Writer Query: ' . $query);
            return $GLOBALS['db']->query($query);
        } catch (\Exception $ex) {
            $GLOBALS['log']->fatal("si_Email_Writer Exception in " . __FILE__ . ":" . __LINE__ . ": " . $ex->getMessage());
            return false;
        }
    }

    /**
     * Prepares a SELECT query string based on the given parameters.
     *
     * @param  string        $table   Name of the table
     * @param  string|array  $fields Fields to be selected (default is '*')
     * @param  array         $where   Associative array of fields to be matched in the WHERE clause
     * @param  string|null   $subquery Subquery to be used as a table (optional)
     * @return string        Prepared SELECT query string
     */
    public static function prepareSelect($table, $fields = '*', $where = [], $subquery = null, $orderBy = null)
    {
        $sql = "SELECT ";

        if (is_string($fields))
            $sql .= $fields;
        else if (is_array($fields) && !empty($fields))
            $sql .= implode(", ", $fields);
        else
            $sql .= '*';

        $sql .= " FROM ";

        if ($subquery)
            $sql .= "($subquery) AS SubqueryTable";
        else
            $sql .= "$table";

        $sql .= self::whereMaker($where);

        if ($orderBy) {
            $sql .= " ORDER BY $orderBy";
        }

        $sql = rtrim($sql);

        return $sql;
    }


    /**
     * Prepares and executes a SELECT query on the specified table with optional conditions.
     *
     * @param  string        $table   Name of the table
     * @param  string|array  $fields Fields to be selected (default is '*')
     * @param  array         $where   Associative array of fields to be matched in the WHERE clause
     * @return array|false   Array of results or false if an exception occurs
     */
    public static function select($table, $fields = '*', $where = [], $orderBy = null)
    {
        try {
            $sql = self::prepareSelect($table, $fields, $where, null, $orderBy);
            $res = self::executeQuery($sql);
            $res2 = [];
            while ($row = $GLOBALS['db']->fetchByAssoc($res)) {
                $res2[] = $row;
            }
            return $res2;
        } catch (\Exception $ex) {
            $GLOBALS['log']->fatal("si_Email_Writer Exception in " . __FILE__ . ":" . __LINE__ . ": " . $ex->getMessage());
            return false;
        }
    }

    /**
     * Prepares and executes a SELECT query with a subquery on the specified table with optional conditions.
     *
     * @param  string        $mainTable   Name of the main table
     * @param  string|array  $mainFields  Fields to be selected in the main query (default is '*')
     * @param  array         $mainWhere   Associative array of fields to be matched in the WHERE clause of the main query
     * @param  string        $subTable    Name of the subquery table
     * @param  string|array  $subFields   Fields to be selected in the subquery (default is '*')
     * @param  array         $subWhere    Associative array of fields to be matched in the WHERE clause of the subquery
     * @return string        Combined SELECT query string
     */
    public static function selectWithSubquery($mainTable, $mainFields = '*', $mainWhere = [], $subTable, $subFields = '*', $subWhere = [])
    {
        try {
            // Prepare the subquery
            $subquery = self::prepareSelect($subTable, $subFields, $subWhere);

            // Prepare the main query with the subquery
            $mainQuery = self::prepareSelect($mainTable, $mainFields, $mainWhere, $subquery);

            $res = self::executeQuery($mainQuery);
            $res2 = [];

            while ($row = $GLOBALS['db']->fetchByAssoc($res))
                $res2[] = $row;

            return $res2;
        } catch (\Exception $ex) {
            $GLOBALS['log']->fatal("si_Email_Writer Exception in " . __FILE__ . ":" . __LINE__ . ": " . $ex->getMessage());
            return [];
        }
    }

    /**
     * Executes a DELETE query on the specified table with optional conditions.
     *
     * @param  string $table   Name of the table
     * @param  array  $where   Associative array of fields to be matched in the WHERE clause
     * @return mixed  Query result or false if an exception occurs
     */
    public static function delete($table, $where = [])
    {
        try {
            $sql = "DELETE FROM $table";
            $sql .= self::whereMaker($where);
            $sql = rtrim($sql);
            return self::executeQuery($sql);
        } catch (\Exception $ex) {
            $GLOBALS['log']->fatal("si_Email_Writer Exception in " . __FILE__ . ":" . __LINE__ . ": " . $ex->getMessage());
            return false;
        }
    }

    /**
     * Executes an UPDATE query on the specified table with given fields and optional conditions.
     *
     * @param  string $table   Name of the table
     * @param  array  $fields  Associative array of fields to be updated
     * @param  array  $where   Associative array of fields to be matched in the WHERE clause
     * @return mixed  Query result or false if an exception occurs
     */
    public static function update($table, $fields, $where = [])
    {
        try {
            $sql = "UPDATE $table SET";
            foreach ($fields as $field => $value)
                $sql .= " " . $field . " = '" . $value . "',";
            $sql = rtrim($sql, ',');
            $sql .= self::whereMaker($where);
            $sql = rtrim($sql);
            return self::executeQuery($sql);
        } catch (\Exception $ex) {
            $GLOBALS['log']->fatal("si_Email_Writer Exception in " . __FILE__ . ":" . __LINE__ . ": " . $ex->getMessage());
            return false;
        }
    }

    /**
     * Prepares a WHERE clause string for an SQL query based on an associative array.
     *
     * @param array   $where Associative array of fields to be matched in the WHERE clause
     * @return string WHERE clause string for SQL Query
     */
    public static function whereMaker($where = [])
    {
        if (!$where)
            return '';

        while (!ltrim($rhs = self::rhsParser($where[key($where)][1]))) {
            unset($where[key($where)]);
        }
        if (ltrim($rhs)) {
            $sql = " WHERE " . key($where) . " " . $where[key($where)][0] . $rhs;
            unset($where[key($where)]);
        }
        foreach ($where as $field => $value) {
            $rhs = self::rhsParser($where[$field][1]);
            if (ltrim($rhs)) {
                $sql .= $value['operator'] ?? "AND";
                $sql .= " " . $field . " " . $where[$field][0] . $rhs;
            }
        }
        return $sql;
    }

    /**
     * Prepares the right-hand side of an assignment or comparison based on the type of argument.
     *
     * @param  int|string|array $value Value to be compared or assigned
     * @return string            Part of SQL statement that can be placed in front of an operator
     */
    public static function rhsParser($value)
    {
        $sql = " ";
        if (is_array($value) && !empty($value)) {
            $sql .=  "(";
            foreach ($value as $val) {
                if (is_numeric($val))
                    $sql .= $val . ',';
                else
                    $sql .= "'" . $val . "',";
            }
            $sql = rtrim($sql, ',');
            $sql .= ")";
        } else if (is_numeric($value))
            $sql .= $value;
        else if (is_string($value)) {
            if (strtolower($value) == 'null')
                $sql .= 'NULL';
            else
                $sql .= "'" . $value . "'";
        }
        return $sql . " ";
    }

    /**
     * Checks if a column exists in the specified table.
     *
     * @param  string $table   Table name to look in
     * @param  string $column  Column to check
     * @return bool   True if the column exists, false otherwise
     */
    public static function columnExists($table, $column)
    {
        try {
            $cols = $GLOBALS['db']->get_columns($table);
            if (is_array($cols)) {
                if (isset($cols[$column]))
                    return true;
                else
                    return false;
            } else
                return false;
        } catch (\Exception $ex) {
            $GLOBALS['log']->fatal("si_Email_Writer Exception in " . __FILE__ . ":" . __LINE__ . ": " . $ex->getMessage());
            return false;
        }
    }
}
