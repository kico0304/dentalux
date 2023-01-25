<!-- HEAD -->
<?php include_once('includes/head.php'); ?>

<?php

    if(isset($_SESSION['logged_in'])){

        /* POVUCI NOVOSTI */
        class News {
            public function fetch_all() {
                global $pdo;
                $query = $pdo->prepare("SELECT * FROM novosti ORDER BY novosti_id ASC");
                $query->execute();
                return $query->fetchAll();
            }
        }
        
        $news = new News;
        $novosti = $news->fetch_all();

        //insert into novosti
        if(isset($_POST['submit'])){
            foreach($_POST as $key => $value) {
                $$key = $value;
            }
            $query = $pdo->prepare('INSERT INTO novosti(novosti_naslov, novosti_text, novosti_datum, novosti_autor) VALUES (?,?,?,?)');
            $count = count($_POST);
            $i=1;
            foreach($_POST as $key => $value) {
                if($i < 5){
                    $query->bindValue($i,$$key);
                    $i++;
                 }
            }
            $query->execute();

            $last_id = $pdo->lastInsertId();
  
            // Count total files
            $countfiles = count($_FILES['files']['name']);
               
            // Prepared statement
            $query = "INSERT INTO novostislike (novosti_id,novostislike_file,novostislike_link,novostislike_timestamp) VALUES(?,?,?,?)";
              
            $statement = $pdo->prepare($query);
              
            // Loop all files
            for($i = 0; $i < $countfiles; $i++) {
                    
                echo("<script>alert(".$i.")</script>");
        
                $helpVar = $_FILES['files']['name'][$i];
                $helpVar = substr($helpVar, -4);
        
                //Timestamp
                $target_time = $date = date('d-m-y h:i:s');
        
                $date = date('d-m-y');
                $time = date('h:i:s');
                $date = trim($date, " ");
                $time = str_replace(":", "", $time);
        
                // File name
                $filename = $date."-".$time.$i.$helpVar;
                
                // Location
                $target_file = './images/uploads/'.$filename;
                
                // file extension
                $file_extension = pathinfo(
                    $target_file, PATHINFO_EXTENSION);
                     
                $file_extension = strtolower($file_extension);
                
                // Valid image extension
                $valid_extension = array("png","jpeg","jpg");
                
                if(in_array($file_extension, $valid_extension)){
              
                    // Upload file
                    if(move_uploaded_file(
                        $_FILES['files']['tmp_name'][$i],
                        $target_file)
                    ) {
             
                        // Execute query
                        $statement->execute(
                            array($last_id,$filename,$target_file,$target_time));
                    }
                }
            }

            header('Location: novosti.php');
        }

        //edit novosti
        if(isset($_POST['submit__'])){
            foreach($_POST as $key => $value) {
                $$key = $value;
                $$key = str_replace('"', '\"', $$key);
            }
        
            $query = $pdo->prepare('UPDATE novosti SET novosti_naslov = "'.$novosti_naslov.'", novosti_text = "'.$novosti_text.'", novosti_datum = "'.$novosti_datum.'", novosti_autor = "'.$novosti_autor.'" WHERE novosti_id='.$novosti_id);

            $query->execute();

            $countImages = count($_FILES['files']['name']);

            if($countImages > 0){
                $last_id = $_POST['novosti_id'];

                // Count total files
                $countfiles = count($_FILES['files']['name']);
                
                // Prepared statement
                $query = "INSERT INTO novostislike (novosti_id,novostislike_file,novostislike_link,novostislike_timestamp) VALUES(?,?,?,?)";
                
                $statement = $pdo->prepare($query);
                
                // Loop all files
                for($i = 0; $i < $countfiles; $i++) {
                        
                    //echo("<script>alert(".$i.")</script>");
            
                    $helpVar = $_FILES['files']['name'][$i];
                    $helpVar = substr($helpVar, -4);
            
                    //Timestamp
                    $target_time = $date = date('d-m-y h:i:s');
            
                    $date = date('d-m-y');
                    $time = date('h:i:s');
                    $date = trim($date, " ");
                    $time = str_replace(":", "", $time);
            
                    // File name
                    $filename = $date."-".$time.$i.$helpVar;
                    
                    // Location
                    $target_file = './images/uploads/'.$filename;
                    
                    // file extension
                    $file_extension = pathinfo(
                        $target_file, PATHINFO_EXTENSION);
                        
                    $file_extension = strtolower($file_extension);
                    
                    // Valid image extension
                    $valid_extension = array("png","jpeg","jpg");
                    
                    if(in_array($file_extension, $valid_extension)){
                
                        // Upload file
                        if(move_uploaded_file(
                            $_FILES['files']['tmp_name'][$i],
                            $target_file)
                        ) {
                
                            // Execute query
                            $statement->execute(
                                array($last_id,$filename,$target_file,$target_time));
                        }
                    }
                }
            }
            
            header('Location: novosti.php');
        
        }

        //delete news
        if(isset($_GET['deleteNews_id'])){
            $news_id = $_GET['deleteNews_id'];

            $query = $pdo->prepare('DELETE FROM novosti WHERE novosti_id = ?');
            $query->bindValue(1, $news_id);
            $query->execute();

            class Slika {
                public function fetch_data_slika($news_id) {
                    global $pdo;
                    $query = $pdo->prepare("SELECT * FROM novostislike WHERE novosti_id = ?");
                    $query->bindValue(1, $news_id);
                    $query->execute();
                    return $query->fetch();
                }
            }
            
            $slika = new Slika;
            $slike = $slika->fetch_data_slika($news_id);

            $query_ = $pdo->prepare('DELETE FROM novostislike WHERE novosti_id = ?');
            $query_->bindValue(1, $news_id);
            $query_->execute();

            //get image path
            $imageUrl = $slike['novostislike_link'];

            //check if image exists
            if(file_exists($imageUrl)){

                //delete the image
                unlink($imageUrl);

            }
            
            header('Location: novosti.php');
        }

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

                <button class="button blue-button" data-toggle="modal" data-target="#insertModal"><i class="me-3 mdi mdi-plus fs-3" aria-hidden="true"></i>Dodaj novost</button>

                <table class="table">
                    <thead>
                        <tr>
                            <th class="" scope="col">ID</th>
                            <th class="" scope="col">Naslov</th>
                            <th class="" scope="col">Tekst</th>
                            <th class="" scope="col">Datum</th>
                            <th class="" scope="col">Autor</th>
                            <th class="centeredContent" scope="col">Uredi</th>
                            <th class="centeredContent" scope="col">Obriši</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($novosti as $novost) { ?>     
                        <tr>
                            <th scope="row"><?php echo $novost['novosti_id'] ?></th>
                            <td><?php echo $novost['novosti_naslov'] ?></td>
                            <td><?php echo $novost['novosti_text'] ?></td>
                            <td><?php echo $novost['novosti_datum'] ?></td>
                            <td><?php echo $novost['novosti_autor'] ?></td>
                            <td class="centeredContent editNovost editNovostImages">
                                <i 
                                    data-toggle="modal" 
                                    data-target="#editModal" 
                                    class="me-3 mdi mdi-pencil fs-3" 
                                    aria-hidden="true"
                                    dataID="<?php echo $novost['novosti_id'] ?>"
                                    onclick="showModalInfo(<?php echo $novost['novosti_id'] ?>)"
                                ></i>
                            </td>
                            <td class="centeredContent deleteItemNews">
                                <i 
                                    class="me-3 mdi mdi-delete-forever fs-3" 
                                    aria-hidden="true"
                                    data-toggle="modal" 
                                    data-target="#deleteModal"
                                    dataID_="<?php echo $novost['novosti_id'] ?>"
                                    dataLink="novosti.php?deleteNews_id=<?php echo $novost['novosti_id'] ?>"
                                ></i>
                            </td>
                        </tr>
                        <?php } ?>

                    </tbody>
                </table>
            </div>

            <!-- Modal INSERT -->
            <div class="modal fade" id="insertModal">
                <div class="modal-dialog">
                    <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title">Dodaj novost</h5>
                        <button type="button" class="close" data-dismiss="modal">
                            <span aria-hidden="true">&times;</span>
                        </button>
                    </div>
                    <form action="novosti.php" method="post" autocomplete="off" id="insert_form" enctype='multipart/form-data'>
                        <div class="modal-body">
                            <!-- NASLOV -->
                            <label for="novosti_naslov">Naslov:</label>
                            <input type="text" name="novosti_naslov" placeholder="Unesi naslov novosti..." required>
                            <!-- TEXT -->
                            <label for="novosti_text">Tekst:</label>
                            <textarea name="novosti_text" id="" cols="30" rows="10" placeholder="Unesi tekst novosti..." required></textarea>
                            
                            <!-- DATUM -->
                            <label for="novosti_datum">Datum unosa:</label>
                            <input name="novosti_datum" type="date" value="<?php echo date('Y-m-d') ?>" readOnly>

                            <!-- AUTOR -->
                            <label for="novosti_autor">Autor:</label>
                            <input name="novosti_autor" type="text" value="<?php echo $_SESSION['User'] ?>" readOnly>

                            <!-- SLIKE -->
                            <label for="addImages">Slika:</label>
                            <input type="file" name="files[]" id="addImages">
                            <p>Preporučena dimenzija slike: 800x600px</p>
                        </div>
                        <div class="modal-footer">
                            <button type="button" class="btn btn-secondary" data-dismiss="modal">Odustani</button>
                            <button type="submit" class="btn btn-primary" name="submit">Sačuvaj</button>
                        </div>
                    </form>
                    </div>
                </div>
            </div>

            <!-- Modal EDIT -->
            <div class="modal fade" id="editModal">
                <div class="modal-dialog">
                    <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title">Uredi novost</h5>
                        <button type="button" class="close" data-dismiss="modal">
                            <span aria-hidden="true">&times;</span>
                        </button>
                    </div>
                    <form action="novosti.php" method="post" autocomplete="off" id="edit_form" enctype='multipart/form-data'>
                        <div class="modal-body" id="poljaNovosti">
                        </div>
                        <div class="modal-footer">
                            <button id="closeModalEditCar" type="button" class="btn btn-secondary" data-dismiss="modal">Odustani</button>
                            <button type="submit" class="btn btn-primary" name="submit__">Sačuvaj</button>
                        </div>
                    </form>
                    </div>
                </div>
            </div>

            <!-- MODAL DELETE -->
            <div class="modal fade" id="deleteModal">
                <div class="modal-dialog">
                    <div class="modal-content">
                        <div class="modal-header">
                            <h5 class="modal-title">Da li ste sigurni da želite da obrišete novost?</h5>
                            <button type="button" class="close" data-dismiss="modal">
                                <span aria-hidden="true">&times;</span>
                            </button>
                        </div>
                        <div class="modal-body">
                        </div>
                        <div class="modal-footer">
                            <button type="button" class="btn btn-secondary" data-dismiss="modal">Odustani</button>
                            <a id="confirmDeleteNews" href="">
                                <button type="submit" class="btn btn-primary" name="submit_">Obriši</button>  
                            </a>

                        </div>
                    </div>
                </div>
            </div>
                
        </div>

    </div>
    <!-- body end -->

    <!-- scripts -->

    <script>

        function showModalInfo(str) {
            if (str == "") {
                document.getElementById("poljaNovosti").innerHTML = "";
                return;
            } else {
                var xmlhttp = new XMLHttpRequest();
                xmlhttp.onreadystatechange = function() {
                if (this.readyState == 4 && this.status == 200) {
                    document.getElementById("poljaNovosti").innerHTML = this.responseText;
                }
                };
                xmlhttp.open("GET","getNovost.php?act="+str,true);
                xmlhttp.send();
            }
        }

        function obrisiSliku(id){
            $.ajax({
            'url': 'brisiSliku.php',
            'type': 'POST',
            'crossDomain': true,
            'dataType': 'html', 
            'data': {id: id},
            'success': function(data) {}
            });
        }

        function insertAtCaret(areaId, text) {
            var txtarea = document.getElementById(areaId);
            if (!txtarea) {
                return;
            }

            var scrollPos = txtarea.scrollTop;
            var strPos = 0;
            var br = ((txtarea.selectionStart || txtarea.selectionStart == '0') ?
                "ff" : (document.selection ? "ie" : false));
            if (br == "ie") {
                txtarea.focus();
                var range = document.selection.createRange();
                range.moveStart('character', -txtarea.value.length);
                strPos = range.text.length;
            } else if (br == "ff") {
                strPos = txtarea.selectionStart;
            }

            var front = (txtarea.value).substring(0, strPos);
            var back = (txtarea.value).substring(strPos, txtarea.value.length);
            txtarea.value = front + text + back;
            strPos = strPos + text.length;
            if (br == "ie") {
                txtarea.focus();
                var ieRange = document.selection.createRange();
                ieRange.moveStart('character', -txtarea.value.length);
                ieRange.moveStart('character', strPos);
                ieRange.moveEnd('character', 0);
                ieRange.select();
            } else if (br == "ff") {
                txtarea.selectionStart = strPos;
                txtarea.selectionEnd = strPos;
                txtarea.focus();
            }

            txtarea.scrollTop = scrollPos;
        }

    </script>

    <?php 
    
    include_once('includes/footer.php'); 

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