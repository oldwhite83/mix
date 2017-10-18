<?php

namespace mix\web;

/**
 * Pagination类
 * @author 刘健 <coder.liu@qq.com>
 */
class Pagination
{

    // 内容
    public $items = [];

    // 总记录数
    public $totalItems;

    // 当前页码
    public $currentPage;

    // 每页数量
    public $perPage;

    // 数字链接数量
    public $numberLinks = 5;

    // 固定最小最大数字
    public $fixedMinMax = true;

    // 构造
    public function __construct($config = [])
    {
        // 导入配置
        foreach ($config as $key => $value) {
            $this->$key = $value;
        }
    }

    // 有首页
    public function hasFirst()
    {
        return $this->currentPage == 1 ? false : true;
    }

    // 有上一页
    public function hasBefore()
    {
        return !is_null($this->before());
    }

    // 有下一页
    public function hasNext()
    {
        return !is_null($this->before());
    }

    // 有尾页
    public function hasLast()
    {
        return $this->currentPage == $this->totalPages() ? false : true;
    }

    // 上一页
    public function before()
    {
        $page = $this->currentPage - 1;
        return $page < 1 ? null : $page;
    }

    // 下一页
    public function next()
    {
        $page = $this->currentPage + 1;
        return $page > $this->totalPages() ? null : $page;
    }

    // 总页数
    public function totalPages()
    {
        return (int)ceil($this->totalItems / $this->perPage);
    }

    // 数字页码
    public function numbers()
    {
        $totalPages  = $this->totalPages();
        $number      = $this->numberLinks > $totalPages ? $totalPages : $this->numberLinks;
        $leftNumber  = $number / 2;
        $leftNumber  = is_integer($leftNumber) ? ($leftNumber - 1) : (int)floor($leftNumber);
        $rightNumber = $number - $leftNumber - 1;
        $leftShort   = ($this->currentPage - $leftNumber) < 1 ? true : false;
        $rightShort  = ($this->currentPage + $rightNumber) > $totalPages ? true : false;
        $center      = (!$leftShort && !$rightShort) ? true : false;
        $data        = [];
        $numberRange = [];
        // 左边短
        if ($leftShort) {
            $numberRange = range(1, $number);
        }
        // 右边短
        if ($rightShort) {
            $startNumber = $totalPages - $number + 1;
            $numberRange = range($startNumber, $startNumber + ($number - 1));
        }
        // 居中
        if ($center) {
            $startNumber = $this->currentPage - $leftNumber;
            $numberRange = range($startNumber, $startNumber + $number - 1);
        }
        // 生成数据
        foreach ($numberRange as $value) {
            $data[] = (object)[
                'text'     => $value,
                'selected' => ($value == $this->currentPage) ? true : false,
            ];
        }
        // 固定最小最大数字
        if ($this->fixedMinMax) {
            $temp  = $data;
            $pop   = array_pop($temp);
            $shift = array_shift($temp);
            // 后面加省略号
            if (($leftShort || $center) && $pop->text < $totalPages) {
                $pop->text != ($totalPages - 1) and array_push(
                    $data,
                    (object)[
                        'text'     => 'ellipsis',
                        'selected' => false,
                    ]
                );
                array_push(
                    $data,
                    (object)[
                        'text'     => $totalPages,
                        'selected' => false,
                    ]
                );
            }
            // 前面加省略号
            if (($rightShort || $center) && $shift->text > 1) {
                $shift->text != 2 and array_unshift(
                    $data,
                    (object)[
                        'text'     => 'ellipsis',
                        'selected' => false,
                    ]
                );
                array_unshift(
                    $data,
                    (object)[
                        'text'     => 1,
                        'selected' => false,
                    ]
                );

            }
        }
        return $data;
    }

}
