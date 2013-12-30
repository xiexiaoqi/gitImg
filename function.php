<?php
    
/**
 * 确定请求的完整 URL
 *
 * 几个示例：
 *
 * <ul>
 *   <li>请求 http://www.example.com/index.php?controller=posts&action=create</li>
 *   <li>返回 /index.php?controller=posts&action=create</li>
 * </ul>
 * <ul>
 *   <li>请求 http://www.example.com/news/index.php?controller=posts&action=create</li>
 *   <li>返回 /news/index.php?controller=posts&action=create</li>
 * </ul>
 * <ul>
 *   <li>请求 http://www.example.com/index.php/posts/create</li>
 *   <li>返回 /index.php/posts/create</li>
 * </ul>
 * <ul>
 *   <li>请求 http://www.example.com/news/show/id/1</li>
 *   <li>返回 /news/show/id/1</li>
 * </ul>
 *
 * 此方法参考 Zend Framework 实现。
 *
 * @return string 请求的完整 URL
 */
function requestUri()
{

    if (isset($_SERVER['HTTP_X_REWRITE_URL']))
    {
        $uri = $_SERVER['HTTP_X_REWRITE_URL'];
    }
    elseif (isset($_SERVER['REQUEST_URI']))
    {
        $uri = $_SERVER['REQUEST_URI'];
    }
    elseif (isset($_SERVER['ORIG_PATH_INFO']))
    {
        $uri = $_SERVER['ORIG_PATH_INFO'];
        if (! empty($_SERVER['QUERY_STRING']))
        {
            $uri .= '?' . $_SERVER['QUERY_STRING'];
        }
    }
    else
    {
        $uri = '';
    }
    return $uri;
}

/**
 * 返回不包含任何查询参数的 URI（但包含脚本名称）
 *
 * 几个示例：
 *
 * <ul>
 *   <li>请求 http://www.example.com/index.php?controller=posts&action=create</li>
 *   <li>返回 /index.php</li>
 * </ul>
 * <ul>
 *   <li>请求 http://www.example.com/news/index.php?controller=posts&action=create</li>
 *   <li>返回 /news/index.php</li>
 * </ul>
 * <ul>
 *   <li>请求 http://www.example.com/index.php/posts/create</li>
 *   <li>返回 /index.php</li>
 * </ul>
 * <ul>
 *   <li>请求 http://www.example.com/news/show/id/1</li>
 *   <li>返回 /news/show/id/1</li>
 *   <li>假设使用了 URL 重写，并且 index.php 位于根目录</li>
 * </ul>
 *
 * 此方法参考 Zend Framework 实现。
 *
 * @return string 请求 URL 中不包含查询参数的部分
 */
function baseUri()
{

    $filename = basename($_SERVER['SCRIPT_FILENAME']);

    if (basename($_SERVER['SCRIPT_NAME']) === $filename)
    {
        $url = $_SERVER['SCRIPT_NAME'];
    }
    elseif (basename($_SERVER['PHP_SELF']) === $filename)
    {
        $url = $_SERVER['PHP_SELF'];
    }
    elseif (isset($_SERVER['ORIG_SCRIPT_NAME']) && basename($_SERVER['ORIG_SCRIPT_NAME']) === $filename)
    {
        $url = $_SERVER['ORIG_SCRIPT_NAME']; // 1and1 shared hosting compatibility
    }
    else
    {
        // Backtrack up the script_filename to find the portion matching
        // php_self
        $path = $_SERVER['PHP_SELF'];
        $segs = explode('/', trim($_SERVER['SCRIPT_FILENAME'], '/'));
        $segs = array_reverse($segs);
        $index = 0;
        $last = count($segs);
        $url = '';
        do
        {
            $seg = $segs[$index];
            $url = '/' . $seg . $url;
            ++ $index;
        } while (($last > $index) && (false !== ($pos = strpos($path, $url))) && (0 != $pos));
    }

    // Does the baseUrl have anything in common with the request_uri?
    $request_uri = requestUri();

    if (0 === strpos($request_uri, $url))
    {
        // full $url matches
        return $url;
    }

    if (0 === strpos($request_uri, dirname($url)))
    {
        // directory portion of $url matches
        return rtrim(dirname($url), '/') . '/';
    }

    if (! strpos($request_uri, basename($url)))
    {
        // no match whatsoever; set it blank
        return '';
    }

    // If using mod_rewrite or ISAPI_Rewrite strip the script filename
    // out of baseUrl. $pos !== 0 makes sure it is not matching a value
    // from PATH_INFO or QUERY_STRING
    if ((strlen($request_uri) >= strlen($url))
        && ((false !== ($pos = strpos($request_uri, $url)))
        && ($pos !== 0)))
    {
        $url = substr($request_uri, 0, $pos + strlen($url));
    }

    return rtrim($url, '/') . '/';
}


?>