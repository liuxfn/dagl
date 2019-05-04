<?php
/**
 * Created by PhpStorm.
 * User: lxf
 * Desc:消息处理
 * Date: 12/21 0021
 * Time: 10:49
 */

//初始化数据库访问工具类
require "DBUtil.php";
$dbUtil = new DBUtil();

//用户消息查询
function queryPersonDD()
{
    $pageSize=$_GET['rows'];
    $pageNo=$_GET['page'];
    $user_sf = $_SESSION['user_sf']; //todo 身份校验
    $queryCondition = buildQueryCondition();
    if($user_sf != 1){
        if($_SESSION['user_ssxm'] == '售楼部'){
            $queryCondition = empty($queryCondition)?(" ssxm_bgq like '%售楼部%'"):($queryCondition ." and ssxm_bgq like '%售楼部%'");
        }else{
            $queryCondition = empty($queryCondition)?(" ssxm_bgq = '".$_SESSION['user_ssxm']."'"):($queryCondition ." and ssxm_bgq = '".$_SESSION['user_ssxm']."'");
        }

    }
    $reslult = $GLOBALS['dbUtil']->querySql("
      select user_id,
      user_name,
      person_id,
      xm,
      ssxm_bgq,
      ssxm_bgh,
      date_format(lrrq,'%Y-%m-%d %H:%i:%s') lrrq
      from person_ddls where 1=1 and ".$queryCondition." limit ".(($pageNo-1)*$pageSize).",".$pageSize);

    $rtnArray = json_decode($reslult,true);

    $reslult = $GLOBALS['dbUtil']->querySql("select count(*) records,CEILING(count(*)/".$pageSize.") total from person_ddls where 1=1 and ".$queryCondition);
    $reslult = json_decode($reslult,true);
    $rtnArray['total'] = $reslult['rows'][0]['total'];
    $rtnArray['records'] = $reslult['rows'][0]['records'];
    $rtnArray['page'] = $pageNo;

    $rtnStr= json_encode($rtnArray,true);
    return $rtnStr;
}

function buildQueryCondition()
{
    $queryCondition = "1=1";
    if((!isset($_GET['type']) && (!isset($_GET['filters']) || empty($_GET['filters']))) || (isset($_GET['type']) && (!isset($_SESSION["persondd_filters"]) || empty($_SESSION["persondd_filters"]))))
    {
        unset($_SESSION["persondd_filters"]);
        return $queryCondition;
    }

    $filters=null;
    if(isset($_GET['filters'])){

        $filters = json_decode($_GET['filters'],true);
        $_SESSION["persondd_filters"] = $_GET['filters'];
    }else{
        $filters = json_decode($_SESSION["persondd_filters"],true);
    }

    $groupOp = $filters['groupOp'];
    $rules = $filters['rules'];
    $operator = "";
    for ($x=0; $x<count($rules);$x++) {
        $queryCondition.=" ".$groupOp." ";
        $operator = $rules[$x]['op'] == "in" ? "in" : ($rules[$x]['op'] == "ni" ? "not in" : ($rules[$x]['op'] == "eq" ? "=" : ($rules[$x]['op'] == "cn" ? "like" : ($rules[$x]['op'] == "lt" ? "<" : ($rules[$x]['op'] == "le" ? "<=" : ($rules[$x]['op'] == "gt" ? ">" : ">="))))));
        if($operator == 'in' || $operator == 'ni')
        {
            $queryCondition.=$rules[$x]['field']." ".$operator." (".addslashes($rules[$x]['data']).") ";
        }else if($operator == 'like'){
            $queryCondition.=$rules[$x]['field']." ".$operator." '%".addslashes($rules[$x]['data'])."%' ";
        }else{
            $queryCondition.=$rules[$x]['field']." ".$operator." '".addslashes($rules[$x]['data'])."' ";
        }
    }
    return $queryCondition;
}

//信息助手
function personHandler()
{
    session_start();
    if(!isset($_SESSION["authorization"]) || $_SESSION["authorization"] == '' || $_SESSION["authorization"] != $_COOKIE["authorization"]){
        return "{\"code\":\"402\",\"message\":\"请重新登陆\"}";
    }

    $method = !isset($_GET['oper'])?$_POST['oper']:$_GET['oper'];
    if(empty($method))
    {
        return "{\"code\":\"401\",\"message\":\"方法名为空\"}";
    }

    if("query" == $method)
    {
        return queryPersonDD();
    }
}

echo personHandler();

?>