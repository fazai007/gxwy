<?php

namespace Wx\Model;

use Think\Model;

class BaseModel extends Model {

    /**
     * 获取一行记录
     */
    public function queryRow($sql) {
        $plist = $this->query($sql);
        return empty($plist) ? [] : $plist[0];
    }

    /**
     * 格式化查询语句中传入的in 参与，防止sql注入
     * @param unknown $split
     * @param unknown $str
     */
    public function formatIn($split, $str) {
        if (is_array($str)) {
            $strdatas = $str;
        } else {
            $strdatas = explode($split, $str);
        }
        $data = [];
        for ($i = 0; $i < count($strdatas); $i++) {
            $data[] = (int) $strdatas[$i];
        }
        $data = array_unique($data);
        return implode($split, $data);
    }

}
