<?php
require 'vendor/autoload.php';

use Firebase\JWT\JWT;

define('PRIVATE_KEY', '1gHuiop975cdashyex9Ud23ldsvm2Xq');

$action = isset($_GET['action']) ? $_GET['action'] : '';

$res = [    
    'result' => 'failed',
];

if($action == 'login')
{
    if ($_SERVER['REQUEST_METHOD'] == 'POST') {
        $username = htmlentities($_POST['user']);
        $password = htmlentities($_POST['pass']);
        
        if ($username == 'demo' && $password == 'demo') {
            $nowtime = time();
            $token = [
                'iss' => 'http://www.helloweba.net', //签发者
                'aud' => 'http://www.helloweba.net', //jwt所面向的用户
                'iat' => $nowtime, //签发时间
                'nbf' => $nowtime + 10, //在什么时间之后该jwt才可用
                'exp' => $nowtime + 600, //过期时间-10min
                'data' => [
                    'userid' => 1,
                    'username' => $username
                ]
            ];
            $jwt = JWT::encode($token, PRIVATE_KEY);
            $res['result'] = 'success';
            $res['jwt'] = $jwt;
        }else{
            
        }
    }else{
        $res['msg'] = '用户名或密码错误!';
    }
    echo json_encode($res);
    exit();
    
}elseif($action == 'api'){
    $jwt = isset($_SERVER['HTTP_X_TOKEN']) ? $_SERVER['HTTP_X_TOKEN'] : '';
    if (empty($jwt)) {
        $res['msg'] = 'You do not have permission to access.';
        echo json_encode($res);
        exit;
    }
    
    try {
        JWT::$leeway = 60;
        $decoded = JWT::decode($jwt, PRIVATE_KEY, ['HS256']);
        $arr = (array)$decoded;
        if ($arr['exp'] < time()) {
            $res['msg'] = '请重新登录';
        } else {
            $res['result'] = 'success';
            $res['info'] = $arr;
        }
    } catch(Exception $e) {
        $res['msg'] = 'Token验证失败,请重新登录';
    }
    
    echo json_encode($res);
    exit();
}
?>

<!DOCTYPE html>  
<html>
	<head>
		<meta http-equiv="Content-Type" content="text/html; charset=UTF-8" /> 
		<title>JWT(json web token)</title>
		<script language="JavaScript" src="https://cdn.bootcss.com/axios/0.17.1/axios.min.js"></script>
	</head>
	
	<body>
        <div id="showpage" >
          <div class="form-group">
            <label for="username">用户名</label>
            <input type="text" class="form-control" id="username" placeholder="请输入用户名">
          </div>
          <div class="form-group">
            <label for="password">密码</label>
            <input type="password" class="form-control" id="password" placeholder="请输入密码">
          </div>
          <button type="submit" id="sub-btn" class="btn btn-default">登录</button>
        
            <br/>
            <p class="bg-warning" style="padding: 10px;">演示用户名和密码都是<code>demo</code>。</p>
        </div>
        <div id="user" style="display: none">
            <p>欢迎<strong id="uname"></strong>，您已登录，<a href="javascript:;" id="logout">退出>></a></p>
        </div>
        
        
        <script>
            document.querySelector('#sub-btn').onclick = function() {
            let username = document.querySelector('#username').value;
            let password = document.querySelector('#password').value;
            
            var params = new URLSearchParams();
            params.append('user', username);
            params.append('pass', password);
         
            axios.post(
                '?action=login',
                params
            )
            .then((response) => {
                if (response.data.result === 'success') {
                    console.log(response.data.jwt)
                    // 本地存储token
                    localStorage.setItem('jwt', response.data.jwt);
                    // 把token加入header里
                    axios.defaults.headers.common['X-token'] = response.data.jwt;
                    axios.get('?action=api').then(function(response) {
                        if (response.data.result === 'success') {
                            document.querySelector('#showpage').style.display = 'none';
                            document.querySelector('#user').style.display = 'block';
                            document.querySelector('#uname').innerHTML = response.data.info.data.username;
                        } else {
                            
                        }
                    });
                } else {
                    console.log(response.data.msg);
                }
            })
            .catch(function (error) {
                console.log(error);
            });
        }

        document.querySelector('#logout').onclick = function() {
            localStorage.removeItem('jwt');
            document.querySelector('#showpage').style.display = 'block';
            document.querySelector('#user').style.display = 'none';
        }         
        </script>
    </body>
</html>