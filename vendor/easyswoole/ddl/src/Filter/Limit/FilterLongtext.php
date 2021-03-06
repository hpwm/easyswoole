<?php
/**
 * Created by PhpStorm.
 * User: xcg
 * Date: 2019/10/16
 * Time: 16:58
 */

namespace EasySwoole\DDL\Filter\Limit;


use EasySwoole\DDL\Blueprint\Column;
use EasySwoole\DDL\Contracts\FilterInterface;

class FilterLongtext implements FilterInterface
{
    public static function run(Column $column)
    {
        if ($column->getColumnLimit()) {
            throw new \InvalidArgumentException('col ' . $column->getColumnName() . ' type longtext no require fsp ');
        }
    }
}