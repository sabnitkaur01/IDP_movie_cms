<?php


require_once '../load.php';


confirm_logged_in();
?>

<!DOCTYPE html>

<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <title>Welcome to the admin panel</title>
</head>

<body>
    <h2>Welcome to the dashboard page, <?php echo $_SESSION['user_name']; ?>!</h2>

    <?php echo $_SESSION['user_date'];?>

    <?php if($_SESSION['user_date'] == NULL): ?>

        <P><strong><?php echo $_SESSION['user_name'];?></strong> Log in first time. </p>
    <?php else: ?>

        <p><strong>Last Login: </strong><?php echo $_SESSION['user_date'];?></p>
    <?php endif;?>

    <p><strong>Total Sucessful login: </strong><?php echo $_SESSION['sucess_login'];?></p>

    <h3>you are in level: <?php echo getCurrentUserLevel();?>
    
    </h3>
    <?php if (isCurrentUserAdminAbove());?>

    <a href="admin_createuser.php">Create user </a>
    
    <?php endif;?>
    <a href="admin_edituser.php">Edit user</a>

    <a href="admin_logout.php">Sign out</a>

    <?php if( $_SESSION['user_level'] == 1) {
        
        echo "<h4>Edit Users</h4>";
        $mysqli = new mysqli("localhost","root","","db_movies");

        if ($mysqli -> connect_errno) {
            echo "Failed to connect to MySQl: " . $mysqli -> connect_error;
            exit();
        }

        $sql = "SELECT * FROM tbl_user";
        $result = mysqli_query($mysqli, $sql);

        $users = mysqli_fetch_all($result);

        foreach($users as $user) {

            echo "Name: ".$user[1]." <a href=admin_editalluser.php?id=$user[0]>Edit user</a>".'</br>';
        }
    } ?>
    
</body>

</html>