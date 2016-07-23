<?php
/**
 * ================================================
 * 三级联动地区地址
 * ================================================
 */

namespace Home\Model;


use Think\Model;

class LocationsModel extends Model
{

    /**
     * 根据parent_id不同取出不同级别的地址
     * @param int $pid parent_id 父级id
     * @return mixed 返回地址资源
     */
    function get_address($pid=0)
    {
       return $this->where("parent_id={$pid}")->select();
    }

}