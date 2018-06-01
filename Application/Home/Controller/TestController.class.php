<?php
/**
 * Created by PhpStorm.
 * User: hylanda69874
 * Date: 2018/5/28
 * Time: 13:53
 */
namespace Home\Controller;

use Think\Controller;

class TestController extends Controller{

    public function index(){
        header('Content-Type:text/html;charset=utf8');
        $model = M('kol_tags');
        $res = $model->field('last_tag')->group('last_tag')->select();
        $tagsArr = array_column($res, 'last_tag');
        if(!$tagsArr) {
            exit('tags is null');
        }
        $res = $model->field('wbwh_uid')->group('wbwh_uid')->select();
        $analyModel = M('kol_tags_analy');
        if($res) {
            foreach ($res as $v){
                if(empty($v['wbwh_uid'])) continue;
                $_res = $model->field('last_tag,sum(fans_counts) as cnt, sum(fans_counts_percent) as percent')->where(array('wbwh_uid'=>$v['wbwh_uid']))->group('last_tag')->select();
                $tagsCntArr = array_column($_res, 'cnt', 'last_tag');
                $tagsPercentArr = array_column($_res, 'percent', 'last_tag');
                $data = array();
                foreach($tagsArr as $_tag){
                    $temp = array();
                    $temp['wbwh_uid'] = $v['wbwh_uid'];
                    $temp['tags'] = $_tag;
                    $temp['cnt'] = intval($tagsCntArr[$_tag]);
                    $temp['percent'] = floatval($tagsPercentArr[$_tag]);
                    $data[] = $temp;
                }
                if($data) $analyModel->addAll($data);
            }
        }
    }

    public function kolComp(){
        $model = M('kol_tags_analy');
        $res = M('kol_tags')->field('last_tag')->group('last_tag')->select();
        $tagsArr = array_column($res, 'last_tag');
        if(!$tagsArr) {
            exit('tags is null');
        }
        $res = $model->field('wbwh_uid')->group('wbwh_uid')->select();
        $kolArr = array_column($res,'wbwh_uid');
        $main = '1987892760';
        $mainRes = $model->field('tags,cnt,percent')->where(array('wbwh_uid'=>$main))->select();
        $mainCntArr = array_column($mainRes, 'cnt', 'tags');
        $mainPercentArr = array_column($mainRes, 'percent', 'tags');
        $cmpData = array();
        foreach($kolArr as $_kol){
            if($_kol == $main) continue;
            $res = $model->field('tags,cnt,percent')->where(array('wbwh_uid'=>$_kol))->select();
            $cntArr = array_column($res, 'cnt', 'tags');
            $percentArr = array_column($res, 'percent', 'tags');
            /*$cnt_sum = $percent_sum = 0;
            foreach($tagsArr as $_tags){
                $cnt_sum += pow($mainCntArr[$_tags]-$cntArr[$_tags], 2);
                $percent_sum += pow($mainPercentArr[$_tags]-$percentArr[$_tags], 2);
            }
            $cmpData[$_kol]['cnt_ED'] = sqrt($cnt_sum);
            $cmpData[$_kol]['percent_ED'] = sqrt($percent_sum);*/
            $cnt_M = $percent_M = 0;
            $cnt_A = $cnt_B = $percent_A = $percent_B = 0;
            foreach($tagsArr as $_tags){
                $cnt_M += $mainCntArr[$_tags]*$cntArr[$_tags];
                $cnt_A += pow($mainCntArr[$_tags],2);
                $cnt_B += pow($cntArr[$_tags],2);
                $percent_M += $mainPercentArr[$_tags]*$percentArr[$_tags];
                $percent_A += pow($mainPercentArr[$_tags],2);
                $percent_B += pow($percentArr[$_tags],2);
            }
            $cmpData[$_kol]['cnt_COS'] = $cnt_M/(sqrt($cnt_A)*sqrt($cnt_B));
            $cmpData[$_kol]['percent_COS'] = $percent_M/(sqrt($percent_A)*sqrt($percent_B));
        }

        echo '<pre>';
        print_r($cmpData);
    }

    public function kol(){
        $res = S('main_tags');
        if(!$res) {
            $res = importExcel('main_tags.xlsx');
        }
        if($res){
            S('main_tags', $res);
            header('Content-Type:text/html;charset=utf8');
            $model = M('kol_tags');
            $head = $res[1];unset($res[1]);
            $data = array();
            foreach($res as $v){
                $temp = array();
                $temp['wbwh_uid'] = $v['B'];
                $temp['fans_counts'] = $v['H'];
                $tagsArr = array('C','D','E','F','G');
                $tags = $last_tags = '';
                foreach($tagsArr as $_k){
                    if(empty($v[$_k])) break;
                    $last_tags = $v[$_k];
                    if(empty($tags)) $tags = $v[$_k];
                    else $tags .= '/'.$v[$_k];
                }
                $temp['tags'] = $tags;
                $temp['last_tag'] = $last_tags;
                $data[] = $temp;
            }

            if($data){
                $model->addAll($data);
            }

        }
    }
}