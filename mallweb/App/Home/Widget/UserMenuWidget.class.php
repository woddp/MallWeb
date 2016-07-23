<?php

namespace Home\Widget;
use Think\Controller;

class UserMenuWidget extends Controller
{
    public  function menu(){
       $this->display('UserMenu:menu');
    }

}