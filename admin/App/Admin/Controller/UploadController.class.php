<?php
/**
 *=====================================
 *图片上传  
 *=====================================
 */

namespace Admin\Controller;


use Think\Controller;
use Think\Upload;

class UploadController extends  PublicController
{
    public function upload ()
    {
        //实例化上传插件
        $upload = new Upload(C('UPLOAD_CONFIG'));

        $info=$upload->upload($_FILES);
        //如果七牛上传成功
        $imgurl=$info['Filedata']['url'];
//        $imgurl=C('ROOT').'/'.C('UPLOAD_CONFIG')['rootPath'].$info['Filedata']['savepath'].$info['Filedata']['savename'];
        if(!$imgurl) {// 上传错误提示错误信息
            $this->ajaxReturn(array('status'=>'0','mes'=>$upload->getError ()));
        }else{//
           $this->ajaxReturn(array('status'=>'1','url'=>$imgurl));
        }

    }
    public function edit_upload ()
    {
        //实例化上传插件
        $upload = new Upload(C('UPLOAD_CONFIG'));

        $info=$upload->upload($_FILES);

        //如果七牛上传成功
        $imgurl=$info['wangEditorH5File']['url'];
//        $imgurl=C('ROOT').'/'.C('UPLOAD_CONFIG')['rootPath'].$info['wangEditorH5File']['savepath'].$info['wangEditorH5File']['savename'];
         echo $imgurl;

    }

}