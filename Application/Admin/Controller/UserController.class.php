<?php
/**
 * Created by PhpStorm.
 * User: yangfeilong
 * Date: 2018/4/24
 * Time: 15:16
 */

namespace Admin\Controller;
use Admin\Model\UserModel;

class UserController extends AdminController {

    public function index($type=0){
        $username       =   I('username','');
        $map['status']  =   array('eq',1);
        $map['web_type']  =   array('eq',$type);
        if($username) {
            if (is_numeric($username)) {
                $map['uid|username'] = array(intval($username), array('like', '%' . $username . '%'), '_multi' => true);
            } else {
                $map['username'] = array('like', '%' . (string)$username . '%');
            }
        }

        $list   = $this->lists('qmUser', $map, 'last_login_time DESC');
        $list = int_to_string($list);
        $this->assign('_list', $list);
        $this->assign('_username', $username);

        $this->display('list');
    }

    public function add(){
        if(IS_POST){
            $data = I('post.');
            /* 检测密码 */
            if($data['password'] != $data['repassword']){
                $this->error('密码和重复密码不一致！');
            }
            $res = $this->model->modifyUser($data);
            if($res === true){
                $this->success('用户添加成功！',U('index'));
            }else{
                $this->error($res);
            }
        }
        $this->display();
    }

    public function edit(){
        if(IS_POST){
            $data = I('post.');
            foreach($data as $k=>$v){
                if(empty($v)){
                    unset($data[$k]);
                }
            }
            /* 检测密码 */
            if($data['password']) {
                if ($data['password'] != $data['repassword']) {
                    $this->error('密码和重复密码不一致！');
                }
                $data['password'] = md5_sha1($data['password']);
            }
            $res = $this->model->modifyUser($data);
            if($res === true){
                $this->success('用户修改成功！',U('index'));
            }else{
                $this->error($res);
            }
        }
        $uid = intval(I('uid'));
        $info = $this->model->find($uid);
        $this->assign('_info',$info);
        $this->display('add');
    }

    public function del($uid = 0){
        $uids = [];
        if(!is_array($uid)){
            $uids[] = intval($uid);
        }else {
            $uids = array_values($uid);
        }
        $where['uid'] = array('in',$uids);
        $res = $this->model->where($where)->save(array('status'=>1));
        if($res>0){
            $this->success('用户修改成功！',U('index'));
        }else{
            $this->error('未知错误');
        }
    }
}