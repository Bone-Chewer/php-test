<?php
$data = [
    ['id'=>1, 'newsid'=>"100", 'pid'=>0],
    ['id'=>2, 'newsid'=>"110", 'pid'=>0],
    ['id'=>3, 'newsid'=>"120", 'pid'=>0],
    ['id'=>4, 'newsid'=>"130", 'pid'=>0],
    ['id'=>5, 'newsid'=>"140", 'pid'=>0],
    ['id'=>6, 'newsid'=>"150", 'pid'=>1],
    ['id'=>7, 'newsid'=>"160", 'pid'=>2],
    ['id'=>8, 'newsid'=>"170", 'pid'=>3],
    ['id'=>9, 'newsid'=>"180", 'pid'=>3],
    ['id'=>10, 'newsid'=>"180", 'pid'=>9],
    ['id'=>11, 'newsid'=>"180", 'pid'=>9],
    ['id'=>12, 'newsid'=>"180", 'pid'=>10],
];

function getAllPid($cid)
{
    global $data;
    
    $retVal = [];
    foreach($data as $key=>$val)
    {
        if($val['id'] == $cid)
        {
            $retVal[] = $val['pid'];
        }
    }
    
    foreach($retVal as $pid)
    {
        $retVal = array_merge($retVal, getAllPid($pid) );
    }
    
    $retVal = array_unique($retVal);
    return $retVal;
}

$res = getAllPid(12);
print_r($res);exit;
