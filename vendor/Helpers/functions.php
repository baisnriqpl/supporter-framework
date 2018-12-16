<?php

	if (! function_exists('autoLoadClass'))
	{
		function autoLoadClass($class)
		{
			$class = explode('\\', $class);
			$class[0] = lcfirst($class[0]);
			$file = PATH_DIR . '/' .implode('/', $class) . '.php';

			if (is_file($file)) {
				require_once($file);
			} else {
				throw new Exception("no file:{$file}-------",1);exit;
			}
		}
	}

	if (! function_exists('request')) 
	{
		function request($key = null, $default = null)
		{
			static $request;
			if (empty($request))
			{
				$request = new \Vendor\Http\RequestService;
			}

			if ($key !== null)
			{
				return $request->all($key, $default);
			}

			return $request;
		}
	}

	if (! function_exists('dd'))
	{
		function dd($data)
		{
			echo '<pre>';
				print_r($data);
			echo '</pre>';exit;
		}
	}

	if (! function_exists('dump'))
	{
		function dump($data)
		{
			echo '<pre>';
				print_r($data);
			echo '</pre>';
		}
	}

	if (! function_exists('isJson'))
	{
		function isJson($string)
		{
			json_decode($string);

			return (json_last_error() == JSON_ERROR_NONE);
		}
	}

	if (! function_exists('jsonTransform'))
	{
		function jsonTransform($data)
		{
			return json_decode($data, true)?: $data;
		}
	}

	if (! function_exists('ifArrayToJson'))
	{
		function ifArrayToJson($data)
		{
			if (is_array($data))
			{
				$data = json_encode($data);
			}

			return $data;
		}
	}

	if (! function_exists('objToArray'))
	{
		function objToArray($attributes)
		{
			$data = [];
			if (! empty($attributes)) {
				foreach ($attributes as $key => $val) {
					$data[$key] = $val;
				}
			}
			return $data;
		}
	}

	if (! function_exists('response'))
	{
		function response()
		{
			$response = new \Vendor\Http\ResponseService;

			return $response;
		}
	}

	if (! function_exists('redirect'))
	{
		function redirect($url = null)
		{
			$redirect = new \Vendor\Http\Redirect;

			if ($url !== null)
			{
				return $redirect->goto($url);
			}

			return $redirect;

		}
	}

	if (! function_exists('respond'))
	{
		function respond($code, $message = '', $data = [])
		{
			return [
				'code'		=>	 $code,
				'message'	=>	 $message,
				'data'		=>	$data
			];
		}
	}

	if (! function_exists('view'))
	{
		function view($path, $data = [])
		{
			$view = new \Vendor\View\ViewServerProvider();

			return $view->make($path, $data);
		}
	}

	if (! function_exists('strFilter'))
	{
		function strFilter($str)
		{
			$str=preg_replace("/<(style.*?)>(.*?)<(\/style.*?)>/si","",$str);
			$str=preg_replace("/<(\/?html.*?)>/si","",$str);
			$str=preg_replace("/<(\/?body.*?)>/si","",$str);
			$str=preg_replace("/<(\/?link.*?)>/si","",$str); 
			$str=preg_replace("/<(\/?form.*?)>/si","",$str);
			$str=preg_replace("/cookie/si","COOKIE",$str); 
			$str=preg_replace("/<(\/?style.*?)>/si","",$str); 
			$str=preg_replace("/<(title.*?)>(.*?)<(\/title.*?)>/si","",$str);
			$str=preg_replace("/<(\/?title.*?)>/si","",$str);
			$str=preg_replace("/<(i?frame.*?)>(.*?)<(\/i?frame.*?)>/si","",$str); 
			$str=preg_replace("/<(script.*?)>(.*?)<(\/script.*?)>/si","",$str);
			$str=preg_replace("/<(\/?script.*?)>/si","",$str);
			$str=preg_replace("/javascript/si","Javascript",$str);
			$str=preg_replace("/vbscript/si","Vbscript",$str); 
			$str=preg_replace("/on([a-z]+)\s*=/si","On\\1=",$str); 
			$str=preg_replace("/&#/si","&＃",$str);
			return $str;
		}
	}

	if (! function_exists('getBrowser'))
	{
		function getBrowser()
		{
	    	 $sys = $_SERVER['HTTP_USER_AGENT'];  //获取用户代理字符串
	         if (stripos($sys, "Firefox/") > 0) {
	             preg_match("/Firefox\/([^;)]+)+/i", $sys, $b);
	             $exp[0] = "Firefox";
	             $exp[1] = $b[1];  //获取火狐浏览器的版本号
	         } elseif (stripos($sys, "Maxthon") > 0) {
	             preg_match("/Maxthon\/([\d\.]+)/", $sys, $aoyou);
	             $exp[0] = "傲游";
	             $exp[1] = $aoyou[1];
	         } elseif (stripos($sys, "MSIE") > 0) {
	             preg_match("/MSIE\s+([^;)]+)+/i", $sys, $ie);
	             $exp[0] = "IE";
	             $exp[1] = $ie[1];  //获取IE的版本号
	         } elseif (stripos($sys, "OPR") > 0) {
	    		     preg_match("/OPR\/([\d\.]+)/", $sys, $opera);
	             $exp[0] = "Opera";
	             $exp[1] = $opera[1];  
	         } elseif(stripos($sys, "Edge") > 0) {
	             //win10 Edge浏览器 添加了chrome内核标记 在判断Chrome之前匹配
	             preg_match("/Edge\/([\d\.]+)/", $sys, $Edge);
	             $exp[0] = "Edge";
	             $exp[1] = $Edge[1];
	         } elseif (stripos($sys, "Chrome") > 0) {
	    		     preg_match("/Chrome\/([\d\.]+)/", $sys, $google);
	             $exp[0] = "Chrome";
	             $exp[1] = $google[1];  //获取google chrome的版本号
	         } elseif(stripos($sys,'rv:')>0 && stripos($sys,'Gecko')>0){
	             preg_match("/rv:([\d\.]+)/", $sys, $IE);
	    		     $exp[0] = "IE";
	             $exp[1] = $IE[1];
	         }else {
	    		$exp[0] = "未知浏览器";
	            $exp[1] = ""; 
	    	 }
	         return $exp[0].'('.$exp[1].')';
    	}
	}

	if (! function_exists('getOs'))
	{
		function getOs()
		{
		    $agent = $_SERVER['HTTP_USER_AGENT'];
		        $os = false;
		     
		        if (preg_match('/win/i', $agent) && strpos($agent, '95'))
		        {
		          $os = 'Windows 95';
		        }
		        else if (preg_match('/win 9x/i', $agent) && strpos($agent, '4.90'))
		        {
		          $os = 'Windows ME';
		        }
		        else if (preg_match('/win/i', $agent) && preg_match('/98/i', $agent))
		        {
		          $os = 'Windows 98';
		        }
		        else if (preg_match('/win/i', $agent) && preg_match('/nt 6.0/i', $agent))
		        {
		          $os = 'Windows Vista';
		        }
		        else if (preg_match('/win/i', $agent) && preg_match('/nt 6.1/i', $agent))
		        {
		          $os = 'Windows 7';
		        }
		    	  else if (preg_match('/win/i', $agent) && preg_match('/nt 6.2/i', $agent))
		        {
		          $os = 'Windows 8';
		        }else if(preg_match('/win/i', $agent) && preg_match('/nt 10.0/i', $agent))
		        {
		          $os = 'Windows 10';#添加win10判断
		        }else if (preg_match('/win/i', $agent) && preg_match('/nt 5.1/i', $agent))
		        {
		          $os = 'Windows XP';
		        }
		        else if (preg_match('/win/i', $agent) && preg_match('/nt 5/i', $agent))
		        {
		          $os = 'Windows 2000';
		        }
		        else if (preg_match('/win/i', $agent) && preg_match('/nt/i', $agent))
		        {
		          $os = 'Windows NT';
		        }
		        else if (preg_match('/win/i', $agent) && preg_match('/32/i', $agent))
		        {
		          $os = 'Windows 32';
		        }
		        else if (preg_match('/linux/i', $agent))
		        {
		          $os = 'Linux';
		        }
		        else if (preg_match('/unix/i', $agent))
		        {
		          $os = 'Unix';
		        }
		        else if (preg_match('/sun/i', $agent) && preg_match('/os/i', $agent))
		        {
		          $os = 'SunOS';
		        }
		        else if (preg_match('/ibm/i', $agent) && preg_match('/os/i', $agent))
		        {
		          $os = 'IBM OS/2';
		        }
		        else if (preg_match('/Mac/i', $agent) && preg_match('/PC/i', $agent))
		        {
		          $os = 'Macintosh';
		        }
		        else if (preg_match('/PowerPC/i', $agent))
		        {
		          $os = 'PowerPC';
		        }
		        else if (preg_match('/AIX/i', $agent))
		        {
		          $os = 'AIX';
		        }
		        else if (preg_match('/HPUX/i', $agent))
		        {
		          $os = 'HPUX';
		        }
		        else if (preg_match('/NetBSD/i', $agent))
		        {
		          $os = 'NetBSD';
		        }
		        else if (preg_match('/BSD/i', $agent))
		        {
		          $os = 'BSD';
		        }
		        else if (preg_match('/OSF1/i', $agent))
		        {
		          $os = 'OSF1';
		        }
		        else if (preg_match('/IRIX/i', $agent))
		        {
		          $os = 'IRIX';
		        }
		        else if (preg_match('/FreeBSD/i', $agent))
		        {
		          $os = 'FreeBSD';
		        }
		        else if (preg_match('/teleport/i', $agent))
		        {
		          $os = 'teleport';
		        }
		        else if (preg_match('/flashget/i', $agent))
		        {
		          $os = 'flashget';
		        }
		        else if (preg_match('/webzip/i', $agent))
		        {
		          $os = 'webzip';
		        }
		        else if (preg_match('/offline/i', $agent))
		        {
		          $os = 'offline';
		        }
		        else
		        {
		          $os = '未知操作系统';
		        }
		        return $os;  
		    }
	}

	if (! function_exists('imageHandle'))
	{
		function imageHandle($path = '')
		{

			$path = $path ?: defaultImg();
			
			return request()->catalogueUrl() . $path;
		}	
	}

	if(! function_exists('defaultImg'))
	{
		function defaultImg()
		{
			return '/public/images/b5720f9181735a8b.jpg';
		}
	}

	if (! function_exists('session'))
	{
		function session($key = null, $value = null)
		{
			$session = new \Vendor\Storage\SessionService();

			if (isset($key) && ! isset($value))
			{
				return $session->get($key);
			}
			elseif (isset($key) && isset($value))
			{
				return $session->put($key, $value);
			}

			return $session;
		}
	}

	if (! function_exists('old'))
	{
		function old($key = null)
		{
			$history = new \Vendor\Http\History();

			if (isset($key))
			{
				return $history->getOnce($key);
			}

			return $history;
		}
	}

	if (! function_exists('error'))
	{
		function error($value = null, $type = null)
		{
			$history = new \Vendor\Http\History();

			if ($value)
			{
				return $history->error($value, $type);
			}

			return $history->getError();
		}
	}

	if (! function_exists('getCoverImages'))
	{
		function getCoverImages($fileUrl)
		{ 
	        $result = array(); 

	        if(!empty($fileUrl)){   

	            $result = execCommandLine($fileUrl);  
       		}  

       		return json_encode($result);  
        }  

        function execCommandLine($file){  
               $result = array();  

               $pathParts = pathinfo($file);  
               $filename = $pathParts['dirname']."/".$pathParts['filename']."_";  

               $times = array(8,15,25);  
               foreach ($times as $k => $v) {  
                   $destFilePath = $filename.$v.".jpg";  
                   $command = "/usr/bin/ffmpeg -i {$file} -y -f image2 -ss {$v} -vframes 1 -s 640x360 {$destFilePath}";  
                   exec($command);  
                   //chmod($filename.$v."jpg",0644);  
                   // $destFilePath = str_replace("/data/images/", "http://img.baidu.cn/", $destFilePath);  
                   $selected = $k == 0 ? "1" : "0";//默认将第一张图片作为封面图  
                   array_push($result,array($destFilePath,$selected));  
               }  

       return $result;  
    }  
  
	}



	

