<?php
/**
 * Created by PhpStorm.
 * User: qzc
 * Date: 2018/5/23
 * Time: 下午2:08
 */

namespace app\home\models;


use DB\Mysql;

class Index extends Mysql
{
    public function __construct(){
        parent::__construct();
    }

    /**
     * 创建出库单
     * @param $data
     * @return int
     */
    public function createOutSheet($data){
        return $this->insert('cpk_out_sheet',$data);
    }

    /**
     * 批量创建出库箱码
     * @param $data
     * @return int
     */
    public function createOutCodeAll($data){
        return $this->insertAll('cpk_wlm',$data);
    }

    /**
     * 批量创建出库商品类型数据
     * @param $data
     * @return int
     */
    public function createOutCodeDataAll($data){
        return $this->insertAll('cpk_out_data',$data);
    }

    /**
     * 获取商户信息
     * @param string $field
     * @param array $where
     * @return mixed
     */
    public function getMerchantInfo($field = '*',$where = [])
    {
        $data = $this->field($field)
            ->where($where)
            ->find('code_dealer');
        return $data;
    }

    /**
     * 获取出库记录信息
     * @param string $field
     * @param array $where
     * @return mixed
     */
    public function getCpkOutSheetInfo($field = '*',$where = [])
    {
        $data = $this->field($field)
            ->where($where)
            ->find('cpk_out_sheet');
        return $data;
    }

    /**
     * 获取出库记录
     * @param string $field
     * @param array $where
     * @return mixed
     */
    public function getCpkOutCodeList($field = '*',$where = [])
    {
        $data = $this->field($field)
            ->where($where)
            ->select('cpk_wlm');
        return $data;
    }

    /**
     * 获取出库记录
     * @param array $where
     * @param array $limit
     * @param string $order
     * @return mixed
     */
    public function getCpkOutSheet($where = [],$limit = [1,10],$order = 'a.OUT_TIME desc')
    {
        $data = $this->field('a.*,c.NAME dealer_name ,d.NAME cpk_name')
            ->join([
                "left join {$this->_tbPrefix}code_dealer as c on c.ID = a.DEALER",
                "left join {$this->_tbPrefix}code_cpk as d on d.ID = a.CPK_ID",
            ])
            ->where($where)
            ->order($order)
            ->limit($limit[0],$limit[1])
            ->select('cpk_out_sheet as a');
        return $data;
    }

    /**
     * 搜索出库记录
     * @param array $where
     * @return array
     */
    public function searchCpkOutSheet($where = [])
    {
        $this->where($where);
        $sql = "SELECT
                    *
                FROM
                    (
                        SELECT
                            a.*, c. NAME dealer_name ,
                            d. NAME cpk_name
                        FROM
                            cpk_out_sheet AS a
                        LEFT JOIN cpk_wlm AS b ON b.SHEET_ID = a.ID
                        LEFT JOIN code_dealer AS c ON c.ID = a.DEALER
                        LEFT JOIN code_cpk AS d ON d.ID = a.CPK_ID
                        {$this->_where}
                    ) t
                GROUP BY
                    t.ID";

        return $this->doSql($sql);
    }

    /**
     * 获取出库记录
     * @param string $sheetId
     * @return mixed
     */
    public function getCpkOutCode($sheetId = '')
    {
        $sql = "SELECT
                    BOX_CODE AS box_code ,
                    ck. NAME AS kind_name ,
                    cder. NAME AS dealer_name ,
                    cdgr.DEGREE ,
                    cca.CAPACITY ,
                    cs.SPEC ,
                    FROM_UNIXTIME(
                        cw.OUT_TIME ,
                        '%Y-%m-%d %H:%i:%s'
                    ) OUT_TIME
                FROM
                    cpk_wlm AS cw
                LEFT JOIN code_kind AS ck ON ck.ID = cw.KIND_ID
                LEFT JOIN code_dealer AS cder ON cder.ID = cw.DEALER_ID
                LEFT JOIN code_degree AS cdgr ON cdgr.ID = cw.DEGREE_ID
                LEFT JOIN code_capacity AS cca ON cca.ID = cw.CAPACITY_ID
                LEFT JOIN code_spec AS cs ON cs.ID = cw.SPEC_ID
                WHERE
                    (cw.SHEET_ID = '{$sheetId}')
                ORDER BY
                    cw.OUT_TIME DESC";
        return $this->doSql($sql);
    }

    /**
     * 获取出库商品统计
     * @param string $sheetId
     * @return mixed
     */
    public function getCpkOutCodeStatistics($sheetId = '')
    {
        $data = $this->field('ck. NAME AS kind_name ,cdgr.DEGREE ,cca.CAPACITY ,cs.SPEC ,cod.BOX,cod.PRICE,cod.MONEY')
            ->join([
                "left join {$this->_tbPrefix}code_kind as ck ON ck.ID = cod.KIND",
                "left join {$this->_tbPrefix}code_degree as cdgr ON cdgr.ID = cod.DEGREE",
                "left join {$this->_tbPrefix}code_capacity as cca ON cca.ID = cod.CAPACITY",
                "left join {$this->_tbPrefix}code_spec as cs ON cs.ID = cod.SPEC",
            ])
            ->where(['cod.SHEET_ID' => $sheetId])
            ->select('cpk_out_data as cod');

        return $data;
    }

    /**
     * 获取出库箱码商品信息
     * @param $param
     * @return array
     */
    public function getOutCodeGoodsInfo($param){
        $sql = "SELECT
                    (
                        SELECT
                            NAME
                        FROM
                            code_kind
                        WHERE
                            ID = '{$param['kindId']}'
                    ) AS kind_name ,
                    (
                        SELECT
                            DEGREE
                        FROM
                            code_degree
                        WHERE
                            ID = '{$param['degreeId']}'
                    ) AS DEGREE ,
                    (
                        SELECT
                            CAPACITY
                        FROM
                            code_capacity
                        WHERE
                            ID = '{$param['capacityId']}'
                    ) AS CAPACITY ,
                    (
                        SELECT
                            SPEC
                        FROM
                            code_spec
                        WHERE
                            ID = '{$param['specId']}'
                    ) AS SPEC
                FROM
                    DUAL";

        $data = $this->doSql($sql);
        return isset($data[0]) ? $data[0] : [];
    }

    /**
     * 获取出库次数
     * @param $where
     * @return array
     */
    public function getOutCpkNum($where){
        $data = $this->field('ID')
            ->where($where)
            ->count('cpk_out_sheet');
        return $data;
    }
}