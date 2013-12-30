<?php

    /**
     *  采集指定页面的图片并下载到本地
     */
    class CollectionImg{
        /** 
         *指定图片格式
         *@var $img_ext array
         */
        public  $img_ext  = array( 'jpg', 'png', 'jpeg', 'gif', 'bmp' );
        /** 
         *图片存放在本地的目录(绝对路径)
         *@var $base_dir string
         */
        public  $base_dir = '';
        /** 
         *要获取页面的host
         *@var $host string
         */
        public  $host     = '';
        /** 
         *要获取的页面
         *@var $url string
         */
        public  $url      = '';
        /** 
         *获取结果
         *@var $_result array
         */
        private $_result  = '';

        public  $get_flag = '';

        /**
         *构造函数
         *初始化信息
         */
        function __construct( $host, $url, $base_dir, $get_flag = 'src' ){
            if( !function_exists( 'curl_init' ) ){
                throw new Exception( '无法使用CURL' );
            }
            $this->host = $host;
            $this->url  = $url;
            $this->base_dir = $base_dir;
            $this->get_flag = $get_flag;
            $this->request_file();
        }

        /**
         * 获取页面内容
         */
        private function request_file(){
        	$get_flag = $this->get_flag;
            $ch = curl_init() ;
            curl_setopt( $ch, CURLOPT_URL, $this->url ) ;
            curl_setopt ($ch, CURLOPT_REFERER, $this->host);//伪造来路  
         
            ob_start();
            curl_exec( $ch );
            $result = ob_get_contents() ;
            ob_end_clean();
             
            curl_close( $ch );
            //echo $result;
            //exit();
            preg_match_all( "/{$get_flag}=[\'\"]?([^\'\"]*)[\'\"]?/is", $result, $img_array );

            $img_array = array_unique( $img_array[0] );

            $this->_result = $img_array;

        }

        /**
         *将图片存放到指定目录
         *@param  $c_fication boolean 是否根据图片路径对图片进行保存
         *@return $ret_arr array
         */
        public function get_file( $c_fication = false ){
        	set_time_limit( 0 );
            $files= $this->_result;
            $host = $this->host;
            $url  = $this->url;
            $get_flag= $this->get_flag;
            $img_ext = $this->img_ext;
            $base_dir= $this->base_dir;
            $ret_arr = array();

            foreach( $files as $i => $file ){
                $file = preg_replace( "/({$get_flag}=\")|(\")/is", '', $file );
                $extension = pathinfo( $file, PATHINFO_EXTENSION  );
                if( !in_array( strtolower( $extension ), $img_ext ) )
                    continue;

                $file = str_replace( $host, '', $file );
                $parse_url = parse_url( $file );
                if( isset( $parse_url[ 'host' ] ) ){
                    $dir_name = $base_dir.$parse_url[ 'host' ].dirname( $parse_url[ 'path' ] );
                    $get_file = $file;
                }else{
                    $dir_name = $base_dir.dirname( $file );
                    $get_file = $host.$file;
                }
                if( !$c_fication )
                    $dir_name = $base_dir;
                
                if( !is_dir( $dir_name ) )
                    mkdir( $dir_name, 0777, true );

                $file_name = basename( $file );
                $file_path = $dir_name.'/'.$file_name;

                error_reporting( 0 );
                $file_source = $this->file_get_pic( $host, $get_file );
                //$file_source = file_get_contents( $get_file );
                if( $file_source ){
                    file_put_contents( $file_path, $file_source );

                    $ret_arr[] = $get_file;
                    $ret_arr[] = $file_path;
                }
            }
            return $ret_arr;
        }

        public function file_get_pic( $host, $file ) {
            $ch = curl_init(); 
            curl_setopt($ch, CURLOPT_URL, $file);  
            curl_setopt($ch, CURLOPT_HEADER, 0);            //返回内容中包含 HTTP 头 

            $useragent="Mozilla/4.0 (compatible; MSIE 7.0; Windows NT 5.1; QQDownload 1.7; TencentTraveler 4.0"; 
            curl_setopt($ch, CURLOPT_USERAGENT, $useragent); 

            //来源地址
            curl_setopt($ch, CURLOPT_REFERER, $host);
            curl_setopt($ch,CURLOPT_RETURNTRANSFER, 1); 
            $str = curl_exec($ch);
            curl_close($ch);
            return $str;
        }
    }

    $show_info = false;
    if( isset( $_GET[ 'submit' ] ) ){
        $url = $_GET[ 'url' ];
        $parse_url = parse_url( $url );
        if( !isset( $parse_url[ 'host' ] ) )
            throw new Exception( '网站地址格式错误' );

        $type = $_GET[ 'src_type' ];
        $h = $parse_url[ 'host' ];
        $scheme = isset( $parse_url[ 'scheme' ] ) ? $parse_url[ 'scheme' ] : 'http';
        $host    = $scheme.'://'.$h.'/';
        $base_dir= "/data/www/test/getImg/{$h}/";
        $test    = new CollectionImg( $host, $url, $base_dir, $type );
        $test->get_file();
        //echo '<pre>';
        //print_r( $test->get_file() );
        //exit();
        $show_info = true;
    }
    

?>

<!DOCTYPE html PUBLIC '-//W3C//DTD XHTML 1.0 Transitional//EN' 'http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dli'>
<html xmlns='http://www.w3.org/1999/xhtml'>
<head>
<meta http-equiv="Content-Type" content="text/html; charset=UTF-8" />
<title>网站图片抓取测试程序</title>
<meta name="Keywords" content="" />
<meta name="Description" content=""/>
</head>
<style type = "text/css">
body{ font-size: 13px; color: #333; }
.main{ width: 620px; height: 310px; margin: 0 auto; }
form, .show_info{width: 300px; height: 300px;border: 1px solid #999; float: left;}
form{ text-align: center; margin-right: 10px;}
.show_info{ text-align: left;}
</style>
<body>
    <div class = "main">
        <form action = "" method = 'get' name = 'get_info'>
            <p><label>网站地址:<input type = "text" name = "url" value = "" /></label></p>
            <p><label>路径属性:<input type = "text" name = "src_type" value = "src" /></label></p>
            <p><input type = "submit" name = "submit" value = "开抓" /></p>
        </form>
        <?php if( $show_info ){?>
        <div class = "show_info">
            <p><?php echo $url;?></p>
            <p>页面的图片抓取成功。</p>
            <p>文件存放在：<?php echo $base_dir; ?></p>
            <p><a target = "_blank" href = "view_img.php?dir=<?php echo $h; ?>">查看</a></p>
            <p><a href = "down_zip.php?dir=<?php echo $h; ?>">下载</a></p>
        </div>
        <?php }?>
    </div>
</body>
</html>
