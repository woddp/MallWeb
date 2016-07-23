<?php
/**
 * ========================================
 * 用户收货地址
 * ========================================
 */

namespace Home\Model;


use Think\Model;

class AddressModel extends Model
{
    // 是否批处理验证
    protected $patchValidate    =   true;
   // 自动验证定义
    protected $_validate        =   array(
        array('name','require','收货人不能为空'),
        array('province_id','require','省份不能为空'),
        array('city_id','require','城市不能为空'),
        array('area_id','require','区县不能为空'),
        array('detail_address','require','默认地址不能为空'),
        array('tel','require','收货电话号码不能为空'),
    );

    /**
     * 保存收货地址
     */

    function save_address(){
        //获取用户id
        $id=session('user');
        $id=$id['id'];
        //如果当前收货设为默认地址其他所有地址修取消默认地址
        if($this->data['is_default']){
            $cond=[
                'is_default'=>0,
            ];
          $this->where("member_id={$id}")->setField($cond);
        }
        $this->data['member_id']=$id;
        return $this->add();

    }

    /**
     * 修改收货地址
     */
    function  update_address(){
        return $this->save();
    }

}