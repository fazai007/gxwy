<?php

class ExcelToArrary {

    public function __construct() {
        //导入phpExcel核心类
        include_once('PHPExcel.php');
    }

    /**
     * 读取excel
     * @param string $file_name 路径文件名
     * @param string $encode 返回数据的编码,默认为utf8
     * @return array
     */
    public function read($file_name, $encode = 'utf-8') {
        if (!file_exists($file_name)) {
            return false;
        }
        $ext = pathinfo($file_name, PATHINFO_EXTENSION);
        if (strtolower($ext) == 'xls') {
            $reader = PHPExcel_IOFactory::createReader('Excel5');
        } elseif (strtolower($ext) == 'xlsx') {
            $reader = PHPExcel_IOFactory::createReader('Excel2007');
        }
        //载入excel文件
        $php_excel = $reader->load($file_name);
        //读取第一個工作表
        $sheet = $php_excel->getSheet(0);
        //取得总行数
        $highest_row = $sheet->getHighestRow();
        //取得总列数
        $highest_columm = $sheet->getHighestColumn();
        $excel_data = [];
        /** 循环读取每个单元格的数据 */
        //行数是以第1行开始
        for ($row = 1; $row <= $highest_row; $row++) {
            //列数是以A列开始
            for ($column = 'A'; $column <= $highest_columm; $column++) {
                $excel_data[$row][] = $sheet->getCell($column . $row)->getValue();
            }
        }
        return $excel_data;
    }

}
