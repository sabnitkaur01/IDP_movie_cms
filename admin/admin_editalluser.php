<?php 
require_once '../load.php';
confirm_logged_in();

$_SESSION['current'] = $_GET['id'];
$current_user = getSingleUser($_SESSION['current']);
if(empty($current_user))
{
    $message = 'Failed user information';
}
if(isset($_POST['submit'])){
    $data = array(
        'fname'=>trim($_POST['fname']),
        'username'=>trim($_POST['username']),
        'password'=>trim($_POST['password']),
        'email'=>trim($_POST['email']),
        'user_level'=>isCurrentUserAdminAbove()?trim($_POST['user_level']):0,
        'id'=>$_SESSION['current']
    );
    $message = admin_editAllUser($data);
}

?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>EDIT User</title>
</head>

<body>
    <h2>EDIT User</h2>
    <?php echo !empty($message) ? $message : ''; ?>
    <?php if(!empty($current_user)):?>
    <form action="admin_editalluser.php?id=<?php echo $_SESSION['current'];?>" method="post">
        <label for="first_name">First Name</label>
        <input id="first_name" type="text" name="fname" value="<?php echo $user_info['user_fname'];?>"><br><br>

        <label for="username">Username</label>
        <input id="username" type="text" name="username" value="<?php echo $user_info['user_name'];?>"><br><br>

        <label for="password">Password</label>
        <input id="password" type="text" name="password" value="<?php echo $user_info['user_pass'];?>"><br><br>


        <label for="email">Email</label>
        <input id="email" type="email" name="email" value="<?php echo $user_info['user_email'];?>"><br><br>

        <?php if(isCurrentUserAdminAbove()):?>
        <label for="user_level">User Level</label>
        <select id="user_level" name="user_level">
            <?php $user_level_map = getUserLevelMap();
                foreach ($user_level_map as $val => $label): ?>
                    <option value="<?php echo $val; ?>" <?php echo ($val === (int) $user_info['user_level']) ? 'selected' : ''; ?>><?php echo $label; ?>
            </option>
            <?php endforeach;?>
        </select><br><br>
        <?php endif;?>

        <button type="submit" name="submit">Update User</button>
        <?php endwhile;?>
    </form>
    <?php endif;?>
</body>
</html>