<header class="topbar">
    <nav class="navbar top-navbar">
        <div class="logo-part">
            <img src="images/logo-icon.png" alt="logo">
            <img src="images/logo-text.png" alt="logoText">
        </div>
        <div class="user-part">
            <div class="user-info">
                <img src="images/users/3.jpg" alt="userLoggedIn">
                <div class="user-info-logout">
                    <span><?php echo $_SESSION['User']; ?></span>
                    <a href="./logout.php">
                        <span class="smaller-text">Logout</span>
                    </a>
                </div>
            </div>
        </div>
    </nav>
</header>