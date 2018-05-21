<?php
/**
 * Created by PhpStorm.
 * User: hylanda69874
 * Date: 2017/6/7
 * Time: 16:02
 */
namespace Admin\Model;
use Think\Model;

/**
 * Class UserModel
 * @package Admin\Model
 * 用户模型
 */
class UserModel extends Model{
    /* 用户模型自动验证 */
    protected $_validate = array(
        //验证用户名
        array('username', '1,30', '用户名长度不合法', self::EXISTS_VALIDATE, 'length'),
        array('username', '', '用户名被占用', self::EXISTS_VALIDATE, 'unique'),

        /* 验证密码 */
        array('password', '6,30', '密码长度不合法', self::EXISTS_VALIDATE, 'length', self::MODEL_INSERT),

        /* 验证邮箱 */
        array('email', 'email', '邮箱格式不正确', self::VALUE_VALIDATE),
        array('email', '1,32', '邮箱长度不合法', self::VALUE_VALIDATE, 'length'),
        array('email', '', '邮箱被占用', self::VALUE_VALIDATE, 'unique'),

        /* 验证手机号码 */
        array('mobile', '/^1(3|4|5|7|8)[0-9]\d{8}$/', '手机格式不正确', self::VALUE_VALIDATE),
        array('mobile', '', '手机号被占用', self::VALUE_VALIDATE, 'unique'),
    );

    /* 用户模型自动完成 */
    protected $_auto = array(
        array('password', 'md5_sha1', self::MODEL_INSERT, 'function'),
        array('reg_time', NOW_TIME, self::MODEL_INSERT),
        array('reg_ip', 'get_client_ip', self::MODEL_INSERT, 'function', 1),
        array('update_time', NOW_TIME),
    );


    public function modifyUser($data){
        $this->startTrans();
        if($this->create($data)){
            if(empty($data['uid'])) {
                $uid = $this->add();
            }else{
                $this->save();
                $uid = true;
            }
            return $uid ? true : '未知错误'; //0-未知错误，大于0-注册成功
        } else {
            return $this->getError(); //错误详情见自动验证注释
        }
    }
}