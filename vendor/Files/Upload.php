<?php
// +----------------------------------------------------------------------
// | ThinkPHP [ WE CAN DO IT JUST THINK IT ]
// +----------------------------------------------------------------------
// | Copyright (c) 2006-2014 http://thinkphp.cn All rights reserved.
// +----------------------------------------------------------------------
// | Licensed ( http://www.apache.org/licenses/LICENSE-2.0 )
// +----------------------------------------------------------------------
// | Author: 麦当苗儿 <zuojiazi@vip.qq.com> <http://www.zjzit.cn>
// +----------------------------------------------------------------------
namespace Vendor\Files;;
/**
 *上传函数
 *@param array $file 上传文件的信息
 *@param array $typeAllow 允许的上传类型
 *@param int $size 允许上传文件的大小 0:表示不限制大小
 *@param string $path 保存文件的路径
 *@param array $rec 保存返回的信息
 */

    class Upload
    {
        public $path = './public/uploads/';

        public function multiple($file, $path, $typeAllow = array('image/jpeg','image/png'), $size = 0)
        {
            $result = [];
            $data = [];
            $count = count($file['name']);

            for($i = 0; $i < $count; $i ++)
            {
                foreach ($file as $key => $value)
                {
                    $data[$i][$key] = $value[$i];
                }
            }

            if ($data)
            {
                foreach ($data as $value)
                {
                     $result[] = $this->uploaded($value, $path, $typeAllow, $size);  
                }
            }
            
            return $result;
        }

          public  function uploaded($file, $path ='images', $typeAllow = array('image/jpeg','image/png'), $size = 0)
          {
              $path = $this->path . $path;
                //设置返回值的默认标示
                $rec = array('info' => '', 'success' => false);
                //判断错误号
                if ($file['error'] > 0) {
                    switch ($file['error']) {
                        case 1: $info = '上传的文件超过了 php.ini 中 upload_max_filesize 选项限制的值';break;
                        case 2: $info ='上传文件的大小超过了 HTML 表单中 MAX_FILE_SIZE 选项指定的值';break;
                        case 3: $info ='文件只有部分被上传';break;
                        case 4: $info ='没有文件被上传';break;
                        case 6: $info ='找不到临时文件夹';break;
                        case 7: $info ='文件写入失败';break;
                        default: $info = '未知错误';
                    }
                    $rec['info'] = $info;
                    return $rec;
                }
                //处理一下保存路径
                $savePath = rtrim($path, '/').'/'.date('Ym/');
        
                //echo $savePath;
                //判断目录是否存在
                if (!file_exists($savePath)) {
                    //创建目录
                    mkdir($savePath, 0777, true);
                }
      
                //判断是否是允许的类型
                if (!in_array($file['type'], $typeAllow)) {
                    $rec['info'] = '文件类型不被允许';
                    return $rec;
                }
                //判断上传文件的大小
                if ($size > 0 && $file['size'] > $size) {
                    $rec['info'] = '文件过大';
                    return $rec;
                }
                //得到文件类型
                $ext = pathinfo($file['name'], PATHINFO_EXTENSION);
                //随机一下文件名
                $fileName = date('YmdHis').mt_rand(1000, 9999).'.'.$ext;
                //拼接完成的上传路径
                $savaName = rtrim($savePath, '/').'/'.$fileName;
      
                //执行上传
                if (is_uploaded_file($file['tmp_name'])) {
                    //上传文件
                    if (move_uploaded_file($file['tmp_name'], $savaName)) {
                        $rec['info'] = substr($savaName, 1);
                        $rec['success'] = true;
                        $rec['type'] = $file['type'];
                        return $rec;
                    } else {
                        $rec['info'] = '上传失败';
                        return $rec;
                    }
                } else {
                    $rec['info'] = '上传方式不合法';
                    return $rec;
                }
            }
    }
