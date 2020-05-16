<?php

	include_once('functions.php');

	if (isset($_POST['login'])) 
    {
		$connect = connect();
		$login = htmlentities($_POST['login']);
		$login = mysqli_real_escape_string($connect, $login);
		$password = htmlentities($_POST['password']);
		$password = mysqli_real_escape_string($connect, $password);
        $hashed_password = md5($password);
		$query = "
			SELECT *
			FROM `users`
			WHERE `user_email` = '$login' && `user_password` = '$hashed_password'";
        
		$result = mysqli_query($connect, $query); 
        $row = mysqli_fetch_assoc($result);
        
        if ($row == NULL)
        {   
                echo 'invalid username or password';
        }
        else
        {
            session_start();
			$session_id = session_id();
			$user_id = $row['user_id'];
			$token = generateToken();
			$token_time = time() + 15*60; 
			$query = "
				INSERT INTO `connects`
					SET `connect_token` = '$token', 
						`connect_session` = '$session_id',
						`connect_user_id` = $user_id,
						`connect_token_time` = FROM_UNIXTIME($token_time);
			";
			mysqli_query($connect, $query);
			setcookie('uid', $user_id, time() + 30*24*60*60);
			setcookie('ut', $token, time() + 30*24*60*60);
            header('Location: admin_pan.php');
        }
	}
	view('login');
?>