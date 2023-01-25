<!-- body start -->
<div id="main-wrapper">

    <!-- MAIN PAGE -->
    <div class="page-wrapper">

        <!-- MAIN CONTAINER -->
        <div class="container-fluid">
            <div class="login-wrapper">
                <div class="login-inner">
                    <form method="post" action="index.php">
                        <h4>DENTALUX Admin</h4>
                        <div class="username-holder">
                            <label for="username">Korisničko ime:</label>
                            <input type="text" name="username" id="">
                        </div>
                        <div class="password-holder">
                            <label for="password">Lozinka:</label>
                        <input type="password" name="password" id="">
                        </div>
                        <div class="submit-holder">
                            <button type="submit">Prijava</button>
                        </div>
                        
                    </form>
                </div>
            </div>
        </div>
            
    </div>

</div>
<!-- body end -->

<?php include_once('includes/footer.php'); ?>