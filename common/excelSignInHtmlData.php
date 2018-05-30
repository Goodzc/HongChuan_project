<!DOCTYPE html>
<html xmlns:o="urn:schemas-microsoft-com:office:office"
      xmlns:x="urn:schemas-microsoft-com:office:excel"
      xmlns="http://www.w3.org/TR/REC-html40" lang="en">
<head>
    <meta http-equiv=Content-Type content="text/html; charset=utf-8">
    <meta name=ProgId content=Excel.Sheet>
    <meta name=Generator content="Microsoft Excel 11">
    <style>
        table thead .title{
            line-height: 50px;
            font-size: 25px;
            font-weight: 600;

        }
        table thead .title,table thead .create-time{
             color: #407782;
        }
        table thead .head-title{
            color: #000;
        }
        table th,table td{
            border: 1px solid #000000;
            vertical-align: middle;
        }
    </style>
</head>
<body>
    <table>
        <thead>
            <tr class="title"><th bgcolor="#9fdef1" align="left" colspan="<?php echo count($GLOBALS['export_data_header']);?>">月度汇总表 统计日期：<?php echo $GLOBALS['export_start_date'];?>至<?php echo $GLOBALS['export_end_date'];?></th></tr>
            <tr class="create-time"><th bgcolor="#9fdef1" align="left" colspan="<?php echo count($GLOBALS['export_data_header']);?>">报表生成时间：<?php echo date('Y-m-d H:i');?></th></tr>
            <tr class="head-title">
                <?php
                foreach($GLOBALS['export_data_header'] as $v){
                    echo "<th bgcolor=\"#e6da3d\">{$v}</th>";
                }
                ?>
            </tr>
        </thead>
        <tbody>
            <?php
            foreach($GLOBALS['export_data'] as $v){
                $tmp = [];
                foreach($GLOBALS['export_data_header'] as $k1 => $v1){
                    $bgColor = '#ffffff';
                    if($v[$k1] === '旷工'){
                        $bgColor = '#f554a6';
                    }elseif(strstr($v[$k1],'迟到') > -1 || strstr($v[$k1],'缺卡') > -1 || strstr($v[$k1],'早退') > -1){
                        $bgColor = '#e88407';
                    }elseif(strstr($v[$k1],'出差') > -1 || strstr($v[$k1],'请假') > -1){
                        $bgColor = '#f3ad56';
                    }

                    $tmp[] = "<td bgcolor=\"$bgColor\" align=\"center\" valign='middle'>{$v[$k1]}</td>";
                }
                $str = '<tr>' . implode('',$tmp) . '</tr>';
                echo $str;
            }
            ?>
        </tbody>
    </table>
</body>
</html>