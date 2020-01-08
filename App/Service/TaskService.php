<?php


namespace App\Service;


use App\Model\Task;
use EasySwoole\EasySwoole\Config;

class TaskService extends Base
{
    public function pullSvnCode($data)
    {
        $svn_name = $data['svn_name'];
        $task_id = $data['task_id'];
        //拉取版本库文件
        go(function () use($svn_name,$task_id){
            $command = "/bin/svn checkout svn://185.207.153.185/juxindaijigou /www/wwwroot/$svn_name --username hp --password hp --no-auth-cache";
            //$start_time = get_microtime();
            //echo time();
            //这里使用 sleep 5 来模拟一个很长的命令
            $result = \Co::exec($command);
            //echo time();
            //$end_time = get_microtime();
            //$take_time = take_time($start_time,$end_time);
            //file_put_contents('1.json','任务耗时：'.$take_time.json_encode($result),FILE_APPEND);
            //var_dump($result);
            //TaskModel::update(['state'=>1],['id'=>$task_id]);
            if($result['code'] ==0){
                Task::create()->update([
                    'status'=>1,'remark'=>'代码拉取完成！',
                    'update_time'=>date('Y-m-d H:i:s')
                ],['id'=>$task_id]);
            }
//            if($result['code'] ==0){
//                $start_time = get_microtime();
//                $cp_command = "/bin/cp -r /www/wwwroot/jigou_junyi/config /www/wwwroot/$svn_name/  && /bin/cp -r /www/wwwroot/jigou_junyi/log /www/wwwroot/$svn_name/";
//                $result = \Co::exec($cp_command);
//                $end_time = get_microtime();
//                $take_time = take_time($start_time,$end_time);
//                file_put_contents('1.json','复制任务耗时：'.$take_time.json_encode($result),FILE_APPEND);
//            }
        });
    }

    //创建数据库
    public function createDatabase($data)
    {
        $mysql_config = Config::getInstance()->getConf('TOOL_TASK');
        if(empty($mysql_config)) return '配置文件不存在！';
        $db_name = $data['db_name'];
        //$sql_file = $mysql_config['sql_file_path'];
        date_default_timezone_set("Asia/Shanghai");
        $conn = new \mysqli($mysql_config['db_host'], $mysql_config['db_user'], $mysql_config['db_pwd']);
        // 检测连接
        if ($conn->connect_error) {
            return '连接失败！';
        }
        //创建数据库
        //$db_sql = "CREATE DATABASE ".$db_name;
        $db_sql = "CREATE DATABASE IF NOT EXISTS $db_name DEFAULT CHARACTER SET utf8 COLLATE utf8_general_ci;";
        if($conn->query($db_sql) === TRUE){
            echo '创建数据库成功！';
        }

//        $set_password = '123456';
//        //创建用户
//        $db_user_sql = "CREATE USER $db_name@'%'  IDENTIFIED BY $set_password;";
//        echo $conn->query($db_user_sql);
//
//        //赋予权限
//        $access_sql = "GRANT ALL PRIVILEGES ON $db_name.* TO $db_name@'%' identified by $set_password;";
//        echo $conn->query($access_sql);
//
//        //刷新
//        $flush_sql = "flush privileges;";
//        echo $conn->query($flush_sql);

        //导入sql
        $use_db_sql = "USE $db_name";
        $conn->query($use_db_sql);
        //添加数据
        $file_sql = $mysql_config['sql_file_path'];//
        $str = file_get_contents($file_sql);
        $arr = explode(';',$str);
        foreach($arr as $k=>$v){
            $conn->query($v);
        }
        echo '创建表成功！';
        $task_id = $data['task_id'];
        //更新任务成功
        Task::create()->update([
            'status'=>1,'remark'=>'创建数据库成功！',
            'update_time'=>date('Y-m-d H:i:s'),
        ],['id'=>$task_id]);
        $conn->close();
        return true;
    }

    public function test($var)
    {
        echo $var.'test reload';
    }
}