<?php
/**
 * Created by PhpStorm.
 * User: hylanda69874
 * Date: 2018/6/6
 * Time: 10:29
 */
namespace Home\Model;

class CompKolModel {
    private $table = 'kol_tags_all';

    public function __construct()
    {
        ini_set('memory_limit', '1024M');
        set_time_limit(0);
        header('Content-Type:text/html;charset=utf8');
        $this->model = M($this->table);
    }

    public function addKol($table){
        if(empty($table)) return false;

        $res = $this->model->getDbFields();
        if($res){
            $fields = '`'.implode('`,`', $res).'`';
            $sql = "insert {$this->table} ({$fields}) (select {$fields} from {$table})";
            $this->model->execute($sql);
        }

    }

    private function tagsRepair(){
        $sql = "UPDATE `kol_tags_all` SET `last_tag`=SUBSTRING_INDEX(tags,'/',-1) WHERE `last_tag` = '' OR `last_tag` IS NULL";
        if($this->model->execute($sql) !== false){
            $where['last_tag'] = array(
                array('eq', ''), array('exp', 'IS NULL'), 'or'
            );
            $this->model->where($where)->delete();
        }
    }

    private function getKol(){
        $field = 'wbwh_uid';
        $res = $this->model->field($field)->group($field)->select();
        return array_column($res, $field);
    }

    private function getKolTags($kol){
        if(empty($kol)) return array();
        $field = 'last_tag';
        $where = array('wbwh_uid' => $kol);
        $res = $this->model->field($field)->group($field)->where($where)->select();
        return array_column($res, $field);
    }

    public function compKol($mainKol){
        if(empty($mainKol)) return false;
        $kolArr = $this->getKol();
        if($kolArr) {
            $field = 'last_tag as tags,sum(fans_counts) as cnt, sum(fans_counts_percent) as percent';
            $mainRes = $this->model->field($field)->where(array('wbwh_uid' => $mainKol))->group('last_tag')->select();
            $mainCntArr = array_column($mainRes, 'cnt', 'tags');
            $mainPercentArr = array_column($mainRes, 'percent', 'tags');
            $mainTags = array_column($mainRes, 'tags');
            foreach($kolArr as $_kol){
                if($_kol == $mainKol) continue;

                $res = $this->model->field($field)->where(array('wbwh_uid'=>$_kol))->group('last_tag')->select();
                $cntArr = array_column($res, 'cnt', 'tags');
                $percentArr = array_column($res, 'percent', 'tags');
                $kolTags = array_column($res, 'tags');
                $tagsArr = array_unique(array_merge($mainTags, $kolTags));

                $cnt_M = $percent_M = 0;
                $cnt_A = $cnt_B = $percent_A = $percent_B = 0;
                $cnt_sum = $percent_sum = 0;
                foreach($tagsArr as $_tags){
                    $cnt_sum += pow($mainCntArr[$_tags]-$cntArr[$_tags], 2);
                    $percent_sum += pow($mainPercentArr[$_tags]-$percentArr[$_tags], 2);

                    $cnt_M += $mainCntArr[$_tags]*$cntArr[$_tags];
                    $cnt_A += pow($mainCntArr[$_tags],2);
                    $cnt_B += pow($cntArr[$_tags],2);
                    $percent_M += $mainPercentArr[$_tags]*$percentArr[$_tags];
                    $percent_A += pow($mainPercentArr[$_tags],2);
                    $percent_B += pow($percentArr[$_tags],2);
                }
                $temp = array(
                    $mainKol,  // 主UID
                    $_kol,  // 辅UID
                    sqrt($cnt_sum), //欧式距离1
                    sqrt($percent_sum), //欧式距离2
                    $cnt_M/(sqrt($cnt_A)*sqrt($cnt_B)),   //余弦距离
                    $percent_M/(sqrt($percent_A)*sqrt($percent_B))
                );
                $cmpData[] = $temp;
            }

            $headlist = array('主UID','辅UID','欧式距离1','欧式距离2','余弦距离1', '余弦距离2');
            $this->csv_export($cmpData, $headlist);
        }
    }

    private function csv_export($data = array(), $headlist = array(), $fileName='KOL相似度') {

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
}