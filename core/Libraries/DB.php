<?php

namespace Core\Libraries;

use PDO;
use PDOStatement;

/**
 * Class DB
 * @package Core\Libraries
 * @property PDO $pdo
 */
class DB
{
    private $config;
    private $pdo;

    private $distinct = false;
    private $select = [];
    private $from = [];
    private $join = [];
    private $where = [];
    private $group = [];
    private $having = [];
    private $order = [];
    private $limit = null;
    private $offset = null;

    private $binds = [];

    public function __construct(Config $config)
    {
        $this->config = $config;

        $this->connect();
    }

    private function connect()
    {
        $config = $this->config;
        $host = $config('db.host');
        $user = $config('db.user');
        $pass = $config('db.pass');
        $dbname = $config('db.dbname');

        $this->pdo = new PDO("mysql:dbname=$dbname;host=$host", $user, $pass);
    }

    public function select($select)
    {
        if (is_array($select)) {
            foreach ($select as $field) {
                $this->select($field);
            }
            return $this;
        }

        $this->select[] = $select;

        return $this;
    }

    public function distinct($distinct = true)
    {
        $this->distinct = $distinct;

        return $this;
    }

    public function from($table)
    {
        if (is_array($table)) {
            foreach ($table as $field) {
                $this->from($field);
            }
            return $this;
        }

        $this->from[] = $table;

        return $this;
    }

    public function group($group)
    {
        if (is_array($group)) {
            foreach ($group as $field) {
                $this->group($field);
            }

            return $this;
        }

        $this->group[] = $group;

        return $this;
    }

    public function order($order)
    {
        if (is_array($order)) {
            foreach ($order as $field) {
                $this->order($field);
            }

            return $this;
        }

        $this->order[] = $order;


        return $this;
    }

    public function limit($limit, $offset = null)
    {
        $this->limit = $limit;
        $this->offset = $offset;

        return $this;
    }

    public function join($table, $cond, $type = 'inner')
    {
        $join = [];
        $join[] = $table;
        $join[] = $cond;

        $this->join = "{$type} {$table} {$cond}";

        return $this;
    }

    public function where($field, $cond, $value = null, $not = false, $escape = true)
    {
        $commonConditions = ['=', '!=', '>', '<', '>=', '<='];

        if (is_array($field)) {
            foreach ($field as $values) {
                if (count($values) == 2) {
                    $this->where($values[0], $values[1]);
                } elseif (count($values) == 3) {
                    $this->where($values[0], $values[1], $values[2]);
                }
            }
            return $this;
        }

        if (is_null($value) and $cond == 'is') {
            $this->where[] = $field . ' is ' . ($not ? 'not ' : '') . 'null';
            return $this;
        }

        if (!is_null($value) and $cond == 'like') {
            $whereCondition = $field . ($not ? ' not' : '') . ' like ';

            if ($escape) {
                $whereCondition .= '?';
                $this->binds[] = $value;
            } else {
                $whereCondition .= $value;
            }

            $this->where[] = $whereCondition;

            return $this;
        }

        if (in_array($cond, $commonConditions)) {

            $whereCondition = "{$field} {$cond} ";
            if ($escape) {
                $whereCondition .= '?';
                $this->binds[] = $value;
            } else {
                $whereCondition .= $value;
            }

            $this->where[] = $whereCondition;
        }

        return $this;
    }

    public function not_where($field, $cond, $value = null, $escape = true)
    {
        return $this->where($field, $cond, $value, true, $escape);
    }

    public function where_in($field, $array, $not = false, $escape = true)
    {
        if ($escape) {
            foreach ($array as $key => $val) {
                $array[$key] = $this->escape($array[$key]);
            }
        }

        $array = implode(',', $array);

        $notCond = $not ? 'not ' : '';

        $this->where[] = "$field {$notCond}in ($array)";

        return $this;
    }

    public function not_where_in($field, $array, $escape = true)
    {
        return $this->where_in($field, $array, true, $escape);
    }


    public function where_between($field, $array, $not = false, $escape = true)
    {
        if (count($array) != 2) {
            return $this;
        }

        if ($escape) {
            foreach ($array as $key => $val) {
                $array[$key] = $this->escape($array[$key]);
            }
        }

        $array = implode(' and ', $array);

        $notCond = $not ? 'not ' : '';

        $this->where[] = "$field {$notCond}between $array";

        return $this;
    }

    public function not_where_between($field, $array, $escape = true)
    {
        return $this->where_between($field, $array, true, $escape);
    }

    public function having($cond)
    {
        $this->having[] = $cond;

        return $this;
    }

    /**
     * @param null $from
     * @return false|PDOStatement
     */
    public function get($from = null)
    {
        $sql = [];
        if (!is_null($from)) {
            $this->from($from);
        }

        $sql[] = 'select';

        if ($this->distinct) {
            $sql[] = 'distinct';
        }

        if (count($this->select) == 0) {
            $sql[] = '*';
        } else {
            $sql[] = implode(', ', $this->select);
        }
        $sql[] = "\n";

        $sql[] = 'from';
        $sql[] = implode(', ', $this->from);

        if (count($this->join)) {
            $sql[] = "\n";
            $sql[] = implode("\n", $this->join);
        }

        if (count($this->where)) {
            $sql[] = "\n";
            $sql[] = "where";
            $sql[] = implode("\nand", $this->where);
        }

        if (count($this->group)) {
            $sql[] = "\n";
            $sql[] = "group by";
            $sql[] = implode(", ", $this->group);
        }

        if (count($this->having)) {
            $sql[] = "\n";
            $sql[] = "having";
            $sql[] = implode("\nand ", $this->having);
        }

        if (count($this->order)) {
            $sql[] = "\n";
            $sql[] = "having";
            $sql[] = implode(", ", $this->order);
        }

        if (!is_null($this->limit)) {
            $sql[] = "\n";
            $sql[] = "limit";
            $sql[] = $this->limit;
            if (!is_null($this->offset)) {
                $sql[] = ',';
                $sql[] = $this->offset;
            }
        }

        $prepare = $this->pdo->prepare(implode(' ', $sql));
//        dd(implode(' ', $sql));
        $prepare->execute($this->binds);

        $this->distinct = [];
        $this->select = [];
        $this->from = [];
        $this->join = [];
        $this->where = [];
        $this->group = [];
        $this->having = [];
        $this->order = [];
        $this->limit = [];
        $this->offset = [];
        $this->binds = [];


        return $prepare;
    }


    public function escape($data)
    {
        return $this->pdo->quote($data);
    }


}