<?php
    if( !isset( $_GET[ 'dir' ] ) )
        exit( 'error' );

    $dir = $_GET[ 'dir' ];
    $base_dir = '/data/www/test/getImg/';
    $img_dir  = $base_dir.$dir;
    if( !is_dir( $img_dir ) )
        exit( '图片目录不存在' );

    include_once( 'function.php' );
    $scandir = scandir( $img_dir );
    $host = $_SERVER[ 'HTTP_HOST' ];
    $base_uri = 'http://'.dirname( $host.baseUri() ).'/'.$dir;
    $arr_diff = array( '.', '..' );
    $scandir = array_diff( $scandir, $arr_diff );
    sort( $scandir );
?>

<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01//EN" "http://www.w3.org/TR/html4/strict.dtd">
<html><head>
<title>图片浏览</title>
<link rel="stylesheet" href="css/visuallightbox.css" type="text/css" />
<link rel="stylesheet" href="css/vlightbox.css" type="text/css" />
<script type="text/javascript" src="js/jquery.js"></script>
<script type="text/javascript" src="js/masonry.js"></script>
</head>
<body>
    <div id="vlightbox" style = "width: 100%">
        <?php for( $i = 0; $i < count( $scandir ); $i++ ){
            $img_path = $base_uri.'/'.$scandir[ $i ];
            if( $i == 0 ){
        ?>
            <a id="firstImage" title="<?php echo $scandir[ $i ]?>" href="<?php echo $img_path;?>" class="vlightbox">
                <img alt="image 1" src="<?php echo $img_path;?>" />
            </a>
        <?php
            }else{
        ?>
            <a title="<?php echo $scandir[ $i ]?>" href="<?php echo $img_path;?>" class="vlightbox">
                <img alt="image 2" src="<?php echo $img_path;?>" />
            </a>
            
        <?php }}?>
    </div>
    <script type="text/javascript">
        jQuery(window).load(function() {
           $( '#vlightbox' ).masonry({
                             columnWidth: 10,
                             itemSelector: '.vlightbox'
            });
        });
        var $VisualLightBoxParams$ = {autoPlay:true,borderSize:21,enableSlideshow:true,overlayOpacity:0.4,startZoom:true};
        </script>
    

    <script type="text/javascript" src="js/visuallightbox.js"></script>
</body>
</html>