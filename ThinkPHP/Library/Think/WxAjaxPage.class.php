<?php

// +----------------------------------------------------------------------
// | ThinkPHP [ WE CAN DO IT JUST THINK IT ]
// +----------------------------------------------------------------------
// | Copyright (c) 2009 http://thinkphp.cn All rights reserved.
// +----------------------------------------------------------------------
// | Licensed ( http://www.apache.org/licenses/LICENSE-2.0 )
// +----------------------------------------------------------------------
// | Author: liu21st <liu21st@gmail.com>
// +----------------------------------------------------------------------
// $Id: Page.class.php 2712 2012-02-06 10:12:49Z liu21st $

namespace Think;

class WxAjaxPage {

    // 默认列表每页显示行数
    public $list_rows = 10;
    // 起始行数
    public $first_row;
    // 分页总页面数
    protected $total_pages;
    // 总行数
    protected $total_rows;
    // 当前页数
    protected $now_page;
    // 分页显示定制
    protected $config = array(
        'header' => '<span class="rows">共 %TOTAL_ROW% 条记录</span>',
        'prev' => '上一页',
        'next' => '下一页',
        'theme' => '%UP_PAGE% %FIRST% %LINK_PAGE% %END% %DOWN_PAGE% %HEADER%',
    );
    // 默认分页变量名
    protected $var_page;

    public function __construct($total_rows, $list_rows = '', $ajax_func) {
        $this->total_rows = $total_rows;
        $this->ajax_func = $ajax_func;
        $this->var_page = C('VAR_PAGE') ? C('VAR_PAGE') : 'page';
        if (!empty($list_rows)) {
            $this->list_rows = intval($list_rows);
        }
        $this->total_pages = ceil($this->total_rows / $this->list_rows);
        $this->now_page = !empty($_GET[$this->var_page]) ? intval($_GET[$this->var_page]) : 1;
        if (!empty($this->total_pages) && $this->now_page > $this->total_pages) {
            $this->now_page = $this->total_pages;
        }
        $this->first_row = $this->list_rows * ($this->now_page - 1);
    }

    public function setConfig($name, $value) {
        if (isset($this->config[$name])) {
            $this->config[$name] = $value;
        }
    }

    public function show() {
        if ($this->total_rows == 0 || $this->total_rows <= $this->list_rows)
            return '';
        //上一页
        $prev_page = $this->now_page - 1;
        $prev_page_str = $prev_page > 0 ? '<a href="javascript:' . $this->ajax_func . '(' . $prev_page . ')" class="a1">' . $this->config['prev'] . '</a>' : '<a href="javascript:;" class="a1 disabled">' . $this->config['prev'] . '</a>';
        //下一页
        $next_page = $this->now_page + 1;
        $next_page_str = ($next_page <= $this->total_pages) ? '<a href="javascript:' . $this->ajax_func . '(' . $next_page . ')" class="a3">' . $this->config['next'] . '</a>' : '<a href="javascript:;" class="a3 disabled">' . $this->config['next'] . '</a>';
        $link_page_str = '';
        for ($page = 1; $page <= $this->total_pages; $page++) {
            $selected = '';
            if ($page == $this->now_page) {
                $selected = ' selected';
            }
            $link_page_str .= '<option value="' . $page . '"' . $selected . '>第' . $page . '/' . $this->total_pages . '页</option>';
        }
        $page_str = '<div class="table-cell cell1">' . $prev_page_str . '</div>
            <div class="table-cell cell2"><select class="weui-select a2" onchange="' . $this->ajax_func . '(this.value)">' . $link_page_str . '</select></div>
            <div class="table-cell cell3">' . $next_page_str . '</div>';
        return $page_str;
    }

}
