<?php

namespace app\home\controller;

use think\Controller;

class Wap extends Controller
{
    public function home()
    {
        return view('index/home');
    }

    public function admin()
    {
        return view('index/admin');
    }
}
