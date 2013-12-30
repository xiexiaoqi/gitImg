<?php
    if( !isset( $_GET[ 'dir' ] ) )
        exit( 'error' );

    $dir = $_GET[ 'dir' ];
    $base_dir = '/data/www/test/getImg/';
    $img_dir  = $base_dir.$dir;
    if( !is_dir( $img_dir ) )
        exit( '图片目录不存在' );

    include_once( '../../lib/pclzip.lib.php' );
    $zip_file = "{$dir}.zip";
    $archive = new PclZip( $zip_file );
    $list = $archive->create(array(
                    array( PCLZIP_ATT_FILE_NAME => $dir )
                  )
                  //PCLZIP_OPT_ADD_PATH, 'newpath'
                  //PCLZIP_OPT_REMOVE_PATH, 'data'
            );
	if ($list == 0) {
	  die("ERROR : '".$archive->errorInfo(true)."'");
	}

	$file = $base_dir.$zip_file;
 
    $filename = basename($file);
 
    header("Content-type: application/octet-stream");
 
    //处理中文文件名
    $ua = $_SERVER["HTTP_USER_AGENT"];
    $encoded_filename = rawurlencode($filename);
    if (preg_match("/MSIE/", $ua)) {
     header('Content-Disposition: attachment; filename="' . $encoded_filename . '"');
    } else if (preg_match("/Firefox/", $ua)) {
     header("Content-Disposition: attachment; filename*=\"utf8''" . $filename . '"');
    } else {
     header('Content-Disposition: attachment; filename="' . $filename . '"');
    }
 
    header("Content-Length: ". filesize($file));
    readfile($file);

    @unlink( $file );
?>