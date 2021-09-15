<?php

namespace app\portal\controller;

use cmf\controller\HomeBaseController;

class UploadController extends HomeBaseController
{

    public function index()
    {
        $setting = C('UPLOAD_SITEIMG_QINIU');
        $Upload = new \Think\Upload($setting);
        $info = $Upload->upload($_FILES);
        echo($info["file"]["url"]);
    }

}