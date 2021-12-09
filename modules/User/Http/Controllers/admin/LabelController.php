<?php


namespace Modules\User\Http\Controllers\admin;


class LabelController
{
    public function index(){
        return view('user::admin.article_label.index');
    }

    public function add(){
        return view('user::admin.article_label.add');
    }

}
