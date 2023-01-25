<!-- HEAD -->
<?php include_once('includes/head.php'); ?>

<?php

    if(isset($_SESSION['logged_in'])){
        /* POVUCI KORISNIKE */
        class Users {
            public function fetch_all() {
                global $pdo;
                $query = $pdo->prepare("SELECT * FROM users");
                $query->execute();
                return $query->fetchAll();
            }
        }
        
        $user = new Users;
        $users = $user->fetch_all();
    ?>

    <!-- MAIN CONTENT -->

    <!-- HEADER -->
    <?php include_once('includes/header.php'); ?>

    <!-- body start -->
    <div id="main-wrapper">

        <!-- SIDEBAR -->
        <?php include_once('includes/sidebar.php'); ?>

        <!-- MAIN PAGE -->
        <div class="page-wrapper">

            <!-- MAIN CONTAINER -->
            <div class="container-fluid">
                <table class="table">
                    <thead>
                        <tr>
                        <th scope="col">ID</th>
                        <th scope="col">Username</th>
                        <th scope="col">Password</th>
                        <th scope="col">Token</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($users as $user) { ?>     
                        <tr>
                            <th scope="row"><?php echo $user['user_id'] ?></th>
                            <td><?php echo $user['user_name'] ?></td>
                            <td><?php echo $user['user_password'] ?></td>
                            <td><?php echo $user['user_token'] ?></td>
                        </tr>
                        <?php } ?>

                    </tbody>
                </table>
            </div>

        </div>

    </div>
    <!-- body end -->

    <?php include_once('includes/footer.php'); ?>

    <?php
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
        header('Location: index.php');
    }

?>