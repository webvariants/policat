<?php

class Doctrine_Connection_Mysql_Project extends Doctrine_Connection_Mysql {
    // protected $driverName = 'Mysql';

    public function quoteIdentifier($str, $checkOption = true)
    {
        switch ($str) {
            case 'member':
            case 'groups':
            case 'function':
                return '`' . $str . '`';
        }

        return $str;
    }
}