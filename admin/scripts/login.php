<?php

    /////////////////////
    $count = 0;
    $login_count = 0;

    date_default_timezone_set("Toronto/Canada");

    $_SESSION['STATUS'] = 0;

    //////////////////////////////




function login($username, $password, $ip)
{
    $pdo = Database::getInstance()->getConnection();
    ## TODO: Finish the following query to check if the username and password are matching in the DB
    $get_user_query = 'SELECT * FROM tbl_user WHERE user_name = :username AND user_pass=:password';
    $user_set       = $pdo->prepare($get_user_query);
    $user_set->execute(
        array(
            ':username' => $username,
            ':password' => $password,
        )
    );

    if ($found_user = $user_set->fetch(PDO::FETCH_ASSOC)) {
        //We found user in the DB, get him in!
        $found_user_id = $found_user['user_id'];

        //Write thhe username and userid into session
        $_SESSION['user_id']    = $found_user_id;
        $_SESSION['user_name']  = $found_user['user_fname'];
        $_SESSION['user_level'] = $found_user['user_level'];
        
        //////////////////

        // present time zone!!

        $currentTime = date("Y-M-D")." ".date("H:I:SA");

        // it will dispay the new update by user on index.php before actually doing the fetch login time.
        $_SESSION['user_date'] = $found_user['last_login'];
        ///////////////////////


        //Update the user IP by the current logged in one
        $update_user_query = 'UPDATE tbl_user SET user_ip= :user_ip WHERE user_id=:user_id';
        $update_user_set   = $pdo->prepare($update_user_query);
        $update_user_set->execute(
            array(
                ':user_ip' => $ip,
                ':user_id' => $found_user_id,
                ':last_login'=>$currentTime
            )
        );

        ////////////////////////////////////////////////////

        $_SESSION['sucess_login'] = $found_user['login_sucess'] + 1;

        $login_sucess = 'UPDATE tbl_user SET login_sucess=:login_sucess WHERE user_name=:username AND user_pass=:password';
        
        $count_login = $pdo-> prepare($login_sucess);

        $count_login->execute(
            array(
                ':username'=>$username,
                ':password'=>$password,
                ':login_sucess'=> $_SESSION['sucess_login']
            )
            );



        ///////////////////////////////////////////////////



        if( $_SESSION['sucess_login'] == 1){

        }
        else{ 
            //Redirect user back to index.php
            redirect_to('index.php');

        }
        


    } else {
        //This is invalid attempt, reject it!
        return 'Learn how to type you dumba&*.';
    }
}

function confirm_logged_in($admin_above_only = false)
{
    if (!isset($_SESSION['user_id'])) {
        redirect_to("admin_login.php");
    }

    if (!empty($admin_above_only) && empty($_SESSION['user_level'])) {
        redirect_to('index.php');
    }
}

function logout()
{
    session_destroy();

    redirect_to('admin_login.php');
}
