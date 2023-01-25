<!-- HEAD -->
<?php include_once('includes/head.php'); ?>

<?php

    if(isset($_SESSION['logged_in'])){
        include 'admin.php';
    } else{
        if(isset($_POST['username'], $_POST['password'])){
            $username = $_POST['username'];
            $password = md5($_POST['password']);
        
            if(empty($username) or empty($password)){
                //error but do nothing
            }else{
                $query = $pdo->prepare("SELECT * FROM users WHERE user_name = ? AND user_password = ?");

                $query->bindValue(1, $username);
                $query->bindValue(2, $password);

                $query->execute();

                $num = $query->rowCount();

                if($num == 1){
                    $_SESSION['logged_in'] = true;
                    $_SESSION['User']=$_POST['username'];
                    header('Location: index.php');
                    exit();
                }else{
                    // nije se logovao
                }
            }
        }
        include 'login.php';
    }

?>