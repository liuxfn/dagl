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
function query()
{
    $pageSize=$_GET['rows'];
    $pageNo=$_GET['page'];
    $ssxm_con =$_SESSION["user_sf"]==1?"1=1":"ssxm='".$_SESSION["user_ssxm"]."'";
    //$userId = $_SESSION['user_sf']; todo 身份校验
    $queryCondition = buildQueryCondition();
    $sql = "";
    $zzzt = isset($_GET['zzzt'])?1:0;
    if(isset($_GET['type'])){
        $sql = "select
        CONCAT(' ',person_id) person_id,
        ssxm,
        ssxm ssxm_ls,
        case zw when 0 then '基层' else '管理层' end zw,
        gw,
        xm,
        CONCAT(' ',date_format(rzrq,'%Y-%m-%d')) rzrq,
        CONCAT(' ',date_format(lzrq,'%Y-%m-%d')) lzrq,
        case zzzt when 0 then '在职' else '离职' end zzzt,
        case zzzt when 1 then null else TIMESTAMPDIFF(YEAR, date_format(rzrq,'%Y-%m-%d'), CURDATE()) end gl,
        CONCAT(' ',sfzh) sfzh,
        CONCAT(' ',yhkh) yhkh,
        case xb when 0 then '男性' else '女性' end xb,
        CONCAT(' ',date_format(csrq,'%Y-%m-%d')) csrq,
        CONCAT(date_format(csrq,'%m'),'月') csyf,
        TIMESTAMPDIFF(YEAR, date_format(csrq,'%Y-%m-%d'), CURDATE()) nl,
        case zzmm when 0 then '中共党员' when 1 then '民主党派' when 2 then '共青团员' else '群众' end zzmm,
        case xl when 0 then '小学' when 1 then '初中' when 2 then '高中' when 3 then '中专' when 4 then '大专' when 5 then '本科' when 6 then '硕士' else '博士' end xl,
        byyx,
        zy,
        CONCAT(' ',date_format(bysj,'%Y-%m-%d')) bysj,
        gzjl,
        case hyzk when 0 then '已婚' when 1 then '未婚' else '离异' end hyzk,
        jtzz,
        xzz,
        CONCAT(' ',lxdh) lxdh,
        jjlxr,
        gx,
        CONCAT(' ',jjlxrdh) jjlxrdh,
        case htlb when 0 then '外包合同' when 1 then '公司合同' else '劳务派遣' end htlb,
        bz,
        CONCAT(' ',date_format(case xgrq when null then lrrq else xgrq end,'%Y-%m-%d %H:%i:%s')) czsj
        from person
        where yxbz = 'Y'
        and zzzt = '".$zzzt."'
        and ".$ssxm_con."
        and ".$queryCondition."
        limit ".(($pageNo-1)*$pageSize).",".$pageSize;
    }else{
        $sql = "select
        person_id,
        ssxm,
        ssxm ssxm_ls,
        case zw when 0 then '基层' else '管理层' end zw,
        gw,
        xm,
        date_format(rzrq,'%Y-%m-%d') rzrq,
        date_format(lzrq,'%Y-%m-%d') lzrq,
        case zzzt when 0 then '在职' else '离职' end zzzt,
        case zzzt when 1 then null else TIMESTAMPDIFF(YEAR, date_format(rzrq,'%Y-%m-%d'), CURDATE()) end gl,
        sfzh,
        yhkh,
        case xb when 0 then '男性' else '女性' end xb,
        date_format(csrq,'%Y-%m-%d') csrq,
        CONCAT(date_format(csrq,'%m'),'月') csyf,
        TIMESTAMPDIFF(YEAR, date_format(csrq,'%Y-%m-%d'), CURDATE()) nl,
        case zzmm when 0 then '中共党员' when 1 then '民主党派' when 2 then '共青团员' else '群众' end zzmm,
        case xl when 0 then '小学' when 1 then '初中' when 2 then '高中' when 3 then '中专' when 4 then '大专' when 5 then '本科' when 6 then '硕士' else '博士' end xl,
        byyx,
        zy,
        date_format(bysj,'%Y-%m-%d') bysj,
        gzjl,
        case hyzk when 0 then '已婚' when 1 then '未婚' else '离异' end hyzk,
        jtzz,
        xzz,
        lxdh,
        jjlxr,
        gx,
        jjlxrdh,
        case htlb when 0 then '外包合同' when 1 then '公司合同' else '劳务派遣' end htlb,
        bz,
        date_format(case xgrq when null then lrrq else xgrq end,'%Y-%m-%d %H:%i:%s') czsj
        from person
        where yxbz = 'Y'
        and zzzt = '".$zzzt."'
        and ".$ssxm_con."
        and ".$queryCondition."
        limit ".(($pageNo-1)*$pageSize).",".$pageSize;
    }

    $reslult = $GLOBALS['dbUtil']->querySql($sql);

    $rtnArray = json_decode($reslult,true);

    $reslult = $GLOBALS['dbUtil']->querySql("select count(*) records,CEILING(count(*)/".$pageSize.") total from person where yxbz = 'Y' and ".$ssxm_con." and ".$queryCondition);
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
    if((!isset($_GET['type']) && (!isset($_GET['filters']) || empty($_GET['filters']))) || (isset($_GET['type']) && (!isset($_SESSION["person_filters"]) || empty($_SESSION["person_filters"]))))
    {
        unset($_SESSION["person_filters"]);
        return $queryCondition;
    }

    $filters=null;
    if(isset($_GET['filters'])){

        $filters = json_decode($_GET['filters'],true);
        $_SESSION["person_filters"] = $_GET['filters'];
    }else{
        $filters = json_decode($_SESSION["person_filters"],true);
    }

    $groupOp = $filters['groupOp'];
    $rules = $filters['rules'];
    $operator = "";
    for ($x=0; $x<count($rules);$x++) {
        $queryCondition.=" ".$groupOp." ";
        $operator = $rules[$x]['op'] == "in" ? "in" : ($rules[$x]['op'] == "ni" ? "not in" : ($rules[$x]['op'] == "eq" ? "=" : ($rules[$x]['op'] == "cn" ? "like" : ($rules[$x]['op'] == "lt" ? "<" : ($rules[$x]['op'] == "le" ? "<=" : ($rules[$x]['op'] == "gt" ? ">" : ">="))))));
        if($operator == 'in' || $operator == 'ni')
        {
            $queryCondition.=$rules[$x]['field']." ".$operator." (".$rules[$x]['data'].") ";
        }else if($operator == 'like'){
            $queryCondition.=$rules[$x]['field']." ".$operator." '%".$rules[$x]['data']."%' ";
        }else{
            $queryCondition.=$rules[$x]['field']." ".$operator." '".$rules[$x]['data']."' ";
        }
    }
    return $queryCondition;
}

//用户消息更新
function updatePerson($sql)
{
    $reslult = $GLOBALS['dbUtil']->updateSql($sql);
    if($reslult == -1)
    {
        return "{\"code\":\"400\",\"message\":\"\"}";
    }
    else
    {
        return "{\"code\":\"200\",\"message\":\"\"}";
    }
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
        return "{\"code\":\"401\"}";
    }

    if("query" == $method)
    {
        return query();
    }else if("add" == $method)
    {
        $lzrq = empty($_POST['lzrq'])?"null":"str_to_date('".$_POST['lzrq']."','%Y-%m-%d')";
        $sql = "INSERT INTO person VALUES(
          NULL,
          '".$_POST['ssxm']."',
          '".$_POST['zw']."',
          '".$_POST['gw']."',
          '".$_POST['xm']."',
          str_to_date('".$_POST['rzrq']."','%Y-%m-%d'),
          ".$lzrq.",
          '".$_POST['zzzt']."',
          '".$_POST['sfzh']."',
          '".$_POST['yhkh']."',
          '".$_POST['xb']."',
          str_to_date(substring('".$_POST['sfzh']."',7,8),'%Y%m%d'),
          '".$_POST['zzmm']."',
          '".$_POST['xl']."',
          '".$_POST['byyx']."',
          '".$_POST['zy']."',
          str_to_date('".$_POST['bysj']."','%Y-%m-%d'),
          '".$_POST['gzjl']."',
          '".$_POST['hyzk']."',
          '".$_POST['jtzz']."',
          '".$_POST['xzz']."',
          '".$_POST['lxdh']."',
          '".$_POST['jjlxr']."',
          '".$_POST['gx']."',
          '".$_POST['jjlxrdh']."',
          '".$_POST['htlb']."',
          '".$_POST['bz']."',
          ".$_SESSION['user_id'].",
          now(),
          null,
          null,
          'Y')";
    }else if("edit" == $method && !isset($_GET['zzzt'])){
        $lzrq = empty($_POST['lzrq'])?"null":"str_to_date('".$_POST['lzrq']."','%Y-%m-%d')";
        $sql = "UPDATE person SET
          ssxm = '".$_POST['ssxm']."',
          zw = '".$_POST['zw']."',
          gw = '".$_POST['gw']."',
          xm = '".$_POST['xm']."',
          rzrq = str_to_date('".$_POST['rzrq']."','%Y-%m-%d'),
          lzrq = ".$lzrq.",
          zzzt = '".$_POST['zzzt']."',
          sfzh = '".$_POST['sfzh']."',
          yhkh = '".$_POST['yhkh']."',
          xb = '".$_POST['xb']."',
          csrq = str_to_date(substring('".$_POST['sfzh']."',7,8),'%Y%m%d'),
          zzmm = '".$_POST['zzmm']."',
          xm = '".$_POST['xm']."',
          xl = '".$_POST['xl']."',
          byyx = '".$_POST['byyx']."',
          zy = '".$_POST['zy']."',
          bysj = str_to_date('".$_POST['bysj']."','%Y-%m-%d'),
          gzjl = '".$_POST['gzjl']."',
          hyzk = '".$_POST['hyzk']."',
          jtzz = '".$_POST['jtzz']."',
          xzz = '".$_POST['xzz']."',
          xm = '".$_POST['xm']."',
          gzjl = '".$_POST['gzjl']."',
          hyzk = '".$_POST['hyzk']."',
          jtzz = '".$_POST['jtzz']."',
          xzz = '".$_POST['xzz']."',
          lxdh = '".$_POST['lxdh']."',
          jjlxr = '".$_POST['jjlxr']."',
          gx = '".$_POST['gx']."',
          jjlxrdh = '".$_POST['jjlxrdh']."',
          htlb = '".$_POST['htlb']."',
          bz = '".$_POST['bz']."',
          xgr = ".$_SESSION['user_id'].",
          XGRQ=now()
          WHERE person_id = ".$_POST['person_id'];
        if(trim($_POST['ssxm']) != trim($_POST['ssxm_ls'])){
            $sql_td = "insert into person_ddls values(
            ".$_SESSION['user_id'].",
            '".$_SESSION['user_name']."',
            ".$_POST['person_id'].",
            '".$_POST['xm']."',
            '".$_POST['ssxm_ls']."',
            '".$_POST['ssxm']."',now())";
            updatePerson($sql_td);
        }
    }else if("edit" == $method && isset($_GET['zzzt'])){
        $sql = "UPDATE person SET
          zzzt = '".$_POST['zzzt']."',
          lzrq = null,
          xgr = ".$_SESSION['user_id'].",
          XGRQ=now()
          WHERE person_id = ".$_POST['person_id'];
    }else if("del" == $method){
        $sql = "UPDATE person SET YXBZ = 'N',XGRQ=now(),XGR=".$_SESSION['user_id']." WHERE person_id in (".$_POST['id'].")";
    }
    return updatePerson($sql);
}

echo personHandler();

?>