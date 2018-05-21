<?php
/**
 * Created by PhpStorm.
 * User: hylanda69874
 * Date: 2018/4/25
 * Time: 9:45
 */
/**
 * 检测用户是否登录
 */
function is_login(){
    $user = session('user_auth_zm');
    if (empty($user)) {
        return 0;
    } else {
        return session('user_auth_sign_zm') == data_auth_sign($user) ? $user['uid'] : 0;
    }
}

/**
 * 数据签名认证
 * @param  array  $data 被认证的数据
 * @return string       签名
 */
function data_auth_sign($data) {
    //数据类型检测
    if(!is_array($data)){
        $data = (array)$data;
    }
    ksort($data); //排序
    $code = http_build_query($data); //url编码并生成query字符串
    $sign = sha1($code); //生成签名
    return $sign;
}

/**
 * 获取PowerBI的Embed Token和配置
 * @param $appName
 * @return mixed|string
 */
function getPbCfg($appName){
    if(empty($appName)) return false;
    $appName = strtolower($appName);
    $cfg = C('POWERBI_CFG');
    $reportInfo = getReportID($appName);
    if(empty($cfg) || empty($reportInfo)) return false;

    $_rid = $reportInfo['report_id'];
    $_token = S($appName.'_PB_TOKEN');
    if(empty($_token) || $reportInfo['is_new']){
        vendor('JWT');
        $jwt = new JWT();
        $param = array(
            'ver'  => '0.2.0',
            'aud'  => 'https://analysis.windows.net/powerbi/api',
            'iss'  => 'Power BI Node SDK',
            'type' => 'embed',
            'wcn'  => $cfg['WORKSPACE_NAME'],
            'wid'  => $cfg['WORKSPACE_ID'],
            //'rid'  => $cfg['REPORT_ID'],
            'rid'  => $_rid,
            'nbf'  => time(),
            'exp'  => time()+$cfg['TOKEN_EXPIRE']
        );
        $key = $cfg['PB_KEY'];
        $algo = $cfg['PB_ALGO'];
        $_token = $jwt->encode($param,$key,$algo);
        S($appName.'_PB_TOKEN', $_token, ['expire'=>$cfg['TOKEN_EXPIRE']]);
    }

    return array(
        'type' => 'report',
        'accessToken' => $_token,
        //'id' => $cfg['REPORT_ID'],
        'id' => $_rid,
        //'name' => $cfg['WORKSPACE_NAME'],
        //'embedUrl' => 'https://embedded.powerbi.cn/appTokenReportEmbed?reportId='.$cfg['REPORT_ID']
        'embedUrl' => 'https://embedded.powerbi.cn/appTokenReportEmbed?reportId='.$_rid
    );
}

function getReportID($appName){
    if(empty($appName)) return false;
    $model = M('report_config');
    $fields = 'id,report_id,qm_status';
    $where['token_name'] = $appName;
    $where['used'] = array('eq', 1);
    $info = $model->field($fields)->where($where)->find();
    if(empty($info)) return false;
    $data = array();
    $data['is_new'] = false;
    $data['report_id'] = $info['report_id'];unset($info['report_id']);
    if(1 == $info['qm_status']) {
        $info['qm_status'] = 0;
        $data['is_new'] = true;
        $model->save($info);
    }

    return $data;
}


//获取sign
function get_sign($paramAry){
    if(isset($paramAry['sign']))
        unset($paramAry['sign']);
    ksort($paramAry);
    $paramsTmp = array();
    foreach ($paramAry as $k => $v) {
        $paramsTmp[] = "$k=$v";
    }
    return md5(implode("&", $paramsTmp).'hylanda');
}

function curlHttp($url, $data=null, $timeout=0){
    $curl = curl_init();
    curl_setopt($curl, CURLOPT_URL, $url);
    curl_setopt($curl, CURLOPT_SSL_VERIFYPEER, FALSE);
    curl_setopt($curl, CURLOPT_SSL_VERIFYHOST, FALSE);
    if(!empty($data)){
        curl_setopt($curl, CURLOPT_CUSTOMREQUEST, 'POST');
        curl_setopt($curl, CURLOPT_POSTFIELDS, $data);
    }
    curl_setopt($curl, CURLOPT_HTTPHEADER, array(
            "cache-control: no-cache",
            "content-type: application/json",)
    );
    curl_setopt($curl, CURLOPT_RETURNTRANSFER, TRUE);
    if ($timeout > 0) { //超时时间秒
        curl_setopt($curl, CURLOPT_TIMEOUT, $timeout);
    }
    $output = curl_exec($curl);
    $error = curl_errno($curl);
    curl_close($curl);

    if($error){
        return false;
    }
    return $output;
}

function importExcel($file='', $sheet=0){
    $file = iconv("utf-8", "gb2312", $file);   //转码
    if(empty($file) OR !file_exists($file)) {
        die('file not exists!');
    }
    vendor('PHPExcel.PHPExcel');  //引入PHP EXCEL类
    $objRead = new PHPExcel_Reader_Excel2007();   //建立reader对象
    if(!$objRead->canRead($file)){
        $objRead = new PHPExcel_Reader_Excel5();
        if(!$objRead->canRead($file)){
            die('No Excel!');
        }
    }

    $cellName = array('A', 'B', 'C', 'D', 'E', 'F', 'G', 'H', 'I', 'J', 'K', 'L', 'M', 'N', 'O', 'P', 'Q', 'R', 'S', 'T', 'U', 'V', 'W', 'X', 'Y', 'Z', 'AA', 'AB', 'AC', 'AD', 'AE', 'AF', 'AG', 'AH', 'AI', 'AJ', 'AK', 'AL', 'AM', 'AN', 'AO', 'AP', 'AQ', 'AR', 'AS', 'AT', 'AU', 'AV', 'AW', 'AX', 'AY', 'AZ', 'BA', 'BB', 'BC', 'BD', 'BE', 'BF', 'BG', 'BH', 'BI', 'BJ', 'BK', 'BL', 'BM', 'BN', 'BO', 'BP', 'BQ', 'BR', 'BS', 'BT', 'BU', 'BV', 'BW', 'BX', 'BY', 'BZ');

    $obj = $objRead->load($file);  //建立excel对象
    $currSheet = $obj->getSheet($sheet);   //获取指定的sheet表
    $columnH = $currSheet->getHighestColumn();   //取得最大的列号
    $columnCnt = array_search($columnH, $cellName);
    $rowCnt = $currSheet->getHighestRow();   //获取总行数

    $data = array();
    for($_row=1; $_row<=$rowCnt; $_row++){  //读取内容
        for($_column=0; $_column<=$columnCnt; $_column++){
            $cellId = $cellName[$_column].$_row;
            if($_column == 0) {
                $cellValue = gmdate('Y-m-d', PHPExcel_Shared_Date::ExcelToPHP($currSheet->getCell($cellId)->getValue()));
            }else {
                $cellValue = $currSheet->getCell($cellId)->getValue();
            }
            if($cellValue instanceof PHPExcel_RichText){   //富文本转换字符串
                $cellValue = $cellValue->__toString();
            }

            $data[$_row][$cellName[$_column]] = $cellValue;
        }
    }

    return $data;
}


/**
 * mysql 缓存方法
 * @param $name
 * @param string $val
 * @return bool|mixed
 */
function sqlCache($name,$val=''){
    if(empty($name)) return false;
    $_model = M('cache');
    $_where['name'] = $name;
    if('' === $val){
        return $_model->where($_where)->getField('val');
    }else{
        $_data['val'] = empty($val)?'':$val;
        $_data['update_date'] = date('Y-m-d H:i:s');
        $_id = $_model->where($_where)->getField('id');
        if($_id){
            return $_model->where(array('id'=>$_id))->save($_data);
        }else{
            $_data['name'] = $name;
            return $_model->add($_data);
        }
    }
}


//去除xxs的攻击的公共方法
function clean_xss($string){
    $string = trim($string);
    $string = strip_tags($string);
    $string = htmlspecialchars($string);
    $string = str_ireplace('<script>', '', $string);
    $string = str_ireplace('</script>', '', $string);
    $string = str_replace(array ('"', "\\", "'", "/", "..", "../", "./", "//" ), '', $string);
    $no = '/%0[0-8bcef]/';
    $string = preg_replace ($no,'',$string);
    $no = '/%1[0-9a-f]/';
    $string = preg_replace ($no,'',$string);
    $no = '/[\x00-\x08\x0B\x0C\x0E-\x1F\x7F]+/S';
    $string = preg_replace ($no,'',$string);
    return $string;
}


//检测表是否存在
function tableExist($tableName){
    if(empty($tableName)) return false;
    $tableName = C('DB_PREFIX').$tableName;
    $model = new \Think\Model();
    $tableArr = $model->query('SHOW TABLES');
    $_fName = 'tables_in_'.C('DB_NAME');
    return in_array($tableName, array_column($tableArr, $_fName));
}


/**
 * 密码加密
 * @param $word
 * @param string $key
 * @return string
 */
function md5_sha1($word, $key = 'xmei'){
    return empty($word)?'':md5(sha1($word).$key);
}

/**
 * 检测验证码
 * @param $code
 * @param  integer $id 验证码ID
 * @return bool 检测结果
 */
function check_verify($code, $id = 1){
    $verify = new \Think\Verify();
    return $verify->check($code, $id);
}

/**
 * 验证手机号是否正确
 * @param INT $mobile
 * @return bool
 */
function isMobile($mobile) {
    if (!is_numeric($mobile)) {
        return false;
    }
    return preg_match('#^13[\d]{9}$|^14[5,7]{1}\d{8}$|^15[^4]{1}\d{8}$|^17[0,6,7,8]{1}\d{8}$|^18[\d]{9}$#', $mobile) ? true : false;
}

/**
 * 验证邮箱是否正确
 * @param INT $email
 * @return bool
 */
function isEmail($email) {
    return preg_match('#^[a-z0-9._%-]+@([a-z0-9-]+\.)+[a-z]{2,4}$#', $email) ? true : false;
    //return preg_match('#^[a-z0-9._%-]+@([a-z0-9-]+\.)+[a-z]{2,4}$|^1[3|4|5|7|8]\d{9}$#', $email) ? true : false;
}

/**
 * xss过滤函数
 *
 * @param $string
 * @return string
 */
function remove_xss($string)
{
    $string = preg_replace('/[\x00-\x08\x0B\x0C\x0E-\x1F\x7F]+/S', '', $string);

    $parm1 = Array('javascript', 'vbscript', 'expression', 'applet', 'meta', 'xml', 'blink', 'link', 'script', 'embed', 'object', 'iframe', 'frame', 'frameset', 'ilayer', 'layer', 'bgsound', 'title', 'base');

    $parm2 = Array('onabort', 'onactivate', 'onafterprint', 'onafterupdate', 'onbeforeactivate', 'onbeforecopy', 'onbeforecut', 'onbeforedeactivate', 'onbeforeeditfocus', 'onbeforepaste', 'onbeforeprint', 'onbeforeunload', 'onbeforeupdate', 'onblur', 'onbounce', 'oncellchange', 'onchange', 'onclick', 'oncontextmenu', 'oncontrolselect', 'oncopy', 'oncut', 'ondataavailable', 'ondatasetchanged', 'ondatasetcomplete', 'ondblclick', 'ondeactivate', 'ondrag', 'ondragend', 'ondragenter', 'ondragleave', 'ondragover', 'ondragstart', 'ondrop', 'onerror', 'onerrorupdate', 'onfilterchange', 'onfinish', 'onfocus', 'onfocusin', 'onfocusout', 'onhelp', 'onkeydown', 'onkeypress', 'onkeyup', 'onlayoutcomplete', 'onload', 'onlosecapture', 'onmousedown', 'onmouseenter', 'onmouseleave', 'onmousemove', 'onmouseout', 'onmouseover', 'onmouseup', 'onmousewheel', 'onmove', 'onmoveend', 'onmovestart', 'onpaste', 'onpropertychange', 'onreadystatechange', 'onreset', 'onresize', 'onresizeend', 'onresizestart', 'onrowenter', 'onrowexit', 'onrowsdelete', 'onrowsinserted', 'onscroll', 'onselect', 'onselectionchange', 'onselectstart', 'onstart', 'onstop', 'onsubmit', 'onunload', 'prompt');

    $parm = array_merge($parm1, $parm2);

    for ($i = 0; $i < sizeof($parm); $i++) {
        $pattern = '/';
        for ($j = 0; $j < strlen($parm[$i]); $j++) {
            if ($j > 0) {
                $pattern .= '(';
                $pattern .= '(&#[x|X]0([9][a][b]);?)?';
                $pattern .= '|(&#0([9][10][13]);?)?';
                $pattern .= ')?';
            }
            $pattern .= $parm[$i][$j];
        }
        $pattern .= '/i';
        $string = preg_replace($pattern, ' ', $string);
        $string = preg_replace('/\(/', ' ', $string);
        $string = preg_replace('/\)/', ' ', $string);
    }
    return $string;
}

/**
 * 导出excel(csv)
 * @data 导出数据
 * @headlist 第一行,列名
 * @fileName 输出Excel文件名
 * @param array $data
 * @param array $headlist
 * @param $fileName
 */
function csv_export($data = array(), $headlist = array(), $fileName='KOL信息导出') {

    header('Content-Type: application/vnd.ms-excel');
    header('Content-Disposition: attachment;filename="'.$fileName.'.csv"');
    header('Cache-Control: max-age=0');

    //打开PHP文件句柄,php://output 表示直接输出到浏览器
    $fp = fopen('php://output', 'a');

    //输出Excel列名信息
    foreach ($headlist as $key => $value) {
        //CSV的Excel支持GBK编码，一定要转换，否则乱码
        $headlist[$key] = iconv('utf-8', 'gbk', $value);
    }

    //将数据通过fputcsv写到文件句柄
    fputcsv($fp, $headlist);

    //计数器
    $num = 0;

    //每隔$limit行，刷新一下输出buffer，不要太大，也不要太小
    $limit = 100000;

    //逐行取出数据，不浪费内存
    $count = count($data);
    for ($i = 0; $i < $count; $i++) {

        $num++;

        //刷新一下输出buffer，防止由于数据过多造成问题
        if ($limit == $num) {
            ob_flush();
            flush();
            $num = 0;
        }

        $row = $data[$i];
        foreach ($row as $key => $value) {
            $row[$key] = iconv('utf-8', 'gbk', $value);
        }

        fputcsv($fp, $row);
    }
}

function taobaoIP($clientIP){
    $taobaoIP = 'http://ip.taobao.com/service/getIpInfo.php?ip='.$clientIP;
    $IPinfo = json_decode(file_get_contents($taobaoIP));
    $country = $IPinfo->data->country;
    $province = $IPinfo->data->region;
    $city = $IPinfo->data->city;
    $data = $country.$province.$city;
    return $data;
}

function getDevice(){
    vendor('mobiledetectlib.Mobile_Detect');
    $detect = new Mobile_Detect();
    if($detect->isMobile()){
        if($detect->isiOS()){
            return '移动端(IOS)';
        }elseif($detect->isAndroidOS()){
            return '移动端(Android)';
        }else{
            return '移动端(其他)';
        }
    }else{
        return 'PC端';
    }
}

/**
 * @param $url
 * @return string
 */
function checkJumpUrl($url){
    if(empty($url)) return '';
    $jumpUrl = remove_xss(urldecode($url));
    //URL 格式错误
    if (!filter_var($jumpUrl, FILTER_VALIDATE_URL, FILTER_FLAG_PATH_REQUIRED)) $jumpUrl = '';

    return $jumpUrl;
}