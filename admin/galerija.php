<!-- HEAD -->
<?php include_once('includes/head.php'); ?>

<?php

    if(isset($_SESSION['logged_in'])){

        class Album {
            public function fetchAlbums() {
                global $pdo;
                $query = $pdo->prepare("SELECT * FROM albumi ORDER BY albumi_id ASC");
                $query->execute();
                return $query->fetchAll();
            }
        }
        
        $album = new Album;
        $albumi = $album->fetchAlbums();
    
        /* POVUCI SLIKU */
        class Galerija {
            public function fetchGaleries($q) {
                global $pdo;
                $query = $pdo->prepare("SELECT * FROM galerija WHERE album_id = ? ORDER BY galerija_id ASC");
                $query->bindValue(1, $q);
                $query->execute();
                return $query->fetchAll();
            }
        }

        $slika = new Galerija;

        //INSERT INTO ALBUMI
        if(isset($_POST['submitAlbum'])){
            foreach($_POST as $key => $value) {
                $$key = $value;
            }
            $query = $pdo->prepare('INSERT INTO albumi(albumi_naziv, albumi_datum, albumi_autor) VALUES (?,?,?)');
            $i=1;
            foreach($_POST as $key => $value) {
                if($i < 4){
                    $query->bindValue($i,$$key);
                    $i++;
                    var_dump($$key);
                 }
            }
            $query->execute();
            header('Location: galerija.php');
        }

        if(isset($_POST['submitGalerija'])){
            //ID ALBUMA
            $album_id = $_POST['galerija_idalbuma'];
            var_dump($album_id);

            //OSTALE VARIJABLE
            $galerija_datum = $_POST['galerija_datum'];
            var_dump($galerija_datum);
            $galerija_autor = $_POST['galerija_autor'];
            var_dump($galerija_autor);
            $galerija_opis = $_POST['galerija_opis'];
            var_dump($galerija_opis);

            //QUERY
            $query = "INSERT INTO galerija(album_id, galerija_file,galerija_link, galerija_datum, galerija_autor, galerija_opis) VALUES (?,?,?,?,?,?)";
            $statement = $pdo->prepare($query);

            //FAJL SLIKA
            $countfiles = count($_FILES['files']['name']);
            var_dump($countfiles);

            for($i = 0; $i < $countfiles; $i++) {
     
                //echo("<script>alert(".$i.")</script>");
                $helpVar = $_FILES['files']['name'][$i];
                $helpVar = substr($helpVar, -4);
                //Timestamp
                //$target_time = $date = date('d-m-y h:i:s');
                $date = date('d-m-y');
                $time = date('h:i:s');
                $date = trim($date, " ");
                $time = str_replace(":", "", $time);
                // File name
                $filename = $date."-".$time.$i.$helpVar;
                var_dump($filename);
             
                // Location
                $target_file = './images/uploads/'.$filename;
                var_dump($target_file);
             
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
                            array($album_id,$filename,$target_file,$galerija_datum,$galerija_autor,$galerija_opis));
                    }
                }
            }
            
            //REDIREKCIJA
            header('Location: galerija.php');
        }

        //EDIT ALBUM
        if(isset($_POST['submit_edit_album'])){
            foreach($_POST as $key => $value) {
                $$key = $value;
            }
            $query = $pdo->prepare('UPDATE albumi SET albumi_naziv="'.$galerija_editAlbum.'" WHERE albumi_id="'.$galerija_editAlbumId.'"');
            $query->execute();
            header('Location: galerija.php');
        }

        //EDIT IMAGE
        if(isset($_POST['submit_edit_image'])){
            foreach($_POST as $key => $value) {
                $$key = $value;
            }
            $query = $pdo->prepare('UPDATE galerija SET galerija_opis="'.$galerija_editImageDesc.'" WHERE galerija_id="'.$galerija_editImageId.'"');
            $query->execute();
            header('Location: galerija.php');
        }

        //DELETE ALBUM
        if(isset($_GET['deleteAlbum_id'])){
            //POKUPI ID ALBUMA ZA BRISANJE
            $album_id = $_GET['deleteAlbum_id'];

            //POKUPI SVE SLIKE IZ ALBUMA KOJI BRIŠEMO
            class Slike {
                public function fetch_data_slike($album_id) {
                    global $pdo;
                    $query = $pdo->prepare("SELECT * FROM galerija WHERE album_id = ?");
                    $query->bindValue(1, $album_id);
                    $query->execute();
                    return $query->fetchAll();
                }
            }

            $slike = new Slike;
            $slikee = $slike->fetch_data_slike($album_id);

            //Kreiraj niz slika
            $items = array();
            foreach($slikee as $slike_){
                $items[] = $slike_['galerija_id'];
                //get image path
                $imageUrl = $slike_['galerija_link'];
                //check if image exists
                if(file_exists($imageUrl)){
                    //delete the image
                    unlink($imageUrl);
                }
            }

            $items = implode("','", $items);

            //var_dump($items);
            //print_r($items);

            //Briši sve slike iz baze
            $query = $pdo->prepare("DELETE FROM galerija WHERE galerija_id IN ('".$items."')");
            $query->execute();


            //OBRIŠI ALBUM IZ BAZE
            $query_ = $pdo->prepare('DELETE FROM albumi WHERE albumi_id = ?');
            $query_->bindValue(1, $album_id);
            $query_->execute();

            header('Location: galerija.php');
        }

        //delete image from album
        if(isset($_GET['deleteImage_id'])){
            $gallery_id = $_GET['deleteImage_id'];

            class Slika {
                public function fetch_data_slika($gallery_id) {
                    global $pdo;
                    $query = $pdo->prepare("SELECT * FROM galerija WHERE galerija_id = ?");
                    $query->bindValue(1, $gallery_id);
                    $query->execute();
                    return $query->fetch();
                }
            }
            
            $slika = new Slika;
            $slike = $slika->fetch_data_slika($gallery_id);

            //get image path
            $imageUrl = $slike['galerija_link'];

            $query_ = $pdo->prepare('DELETE FROM galerija WHERE galerija_id = ?');
            $query_->bindValue(1, $gallery_id);
            $query_->execute();            

            //check if image exists
            if(file_exists($imageUrl)){

                //delete the image
                unlink($imageUrl);

            }
            
            header('Location: galerija.php');
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
                <div class="row">
                    <div class="col-lg-12">
                        <button class="button blue-button" data-toggle="modal" data-target="#insertAlbumModal"><i class="me-3 mdi mdi-plus fs-3" aria-hidden="true"></i>Dodaj album</button>
                    </div>
                </div>
                
                <?php 

                    $countAlbums = count($albumi);

                    if($countAlbums == 0){
                        echo"<h1>Trenutno nemate dodanih albuma.</h1>";
                    }else{
                        foreach ($albumi as $album) { 
                            $albumid = $album['albumi_id'];
                            $$albumid = $slika->fetchGaleries($album['albumi_id']);
                        ?>
                        <div class="row">
                            <div class="col-lg-12">
                                <div class="album-single">
                                    <div class="album-header">
                                        <div class="header-headline">
                                            <h1><?php echo $album['albumi_naziv'] ?></h1>
                                            <i 
                                                data-toggle="modal" 
                                                data-target="#editAlbumModal" 
                                                data-id="<?php echo $album['albumi_id'] ?>"
                                                data-naziv="<?php echo $album['albumi_naziv'] ?>"
                                                class="me-3 mdi mdi-pencil fs-3 editAlbumId" 
                                                aria-hidden="true"
                                            ></i>
                                        </div>
                                        <div class="header-buttons">
                                            <button class="button blue-button addimagebutton" data-toggle="modal" data-target="#insertGalleryModal" data-albumid="<?php echo $album['albumi_id'] ?>" data-albumnaziv="<?php echo $album['albumi_naziv'] ?>"><i class="me-3 mdi mdi-plus fs-3" aria-hidden="true"></i>Dodaj sliku</button>
                                            <button 
                                            class="button blue-button deletewholealbum" data-toggle="modal" data-target="#deleteAlbumModal" data-albumid="<?php echo $album['albumi_id'] ?>" dataLink="galerija.php?deleteAlbum_id=<?php echo $album['albumi_id'] ?>"><i class="me-3 mdi mdi-delete-forever fs-3" aria-hidden="true"></i>Obriši album</button>
                                        </div>
                                    </div>
                                    <?php 
                                        $countImages = count($$albumid);
                                        if($countImages == 0){
                                            echo"<h4>Trenutno nemate dodanih slika u ovom albumu.</h4>";
                                        }else{ ?>
                                            <table class="table">
                                                <thead>
                                                    <tr>
                                                        <th class="centeredContent" scope="col" width="10%">ID slike</th>
                                                        <th class="centeredContent" scope="col" width="20%">Slika</th>
                                                        <th class="centeredContent" scope="col" width="10%">Datum</th>
                                                        <th class="centeredContent" scope="col" width="10%">Autor</th>
                                                        <th class="centeredContent" scope="col" width="30%">Opis</th>
                                                        <th class="centeredContent" scope="col" width="10%">Uredi</th>
                                                        <th class="centeredContent" scope="col" width="10%">Obriši</th>
                                                    </tr>
                                                </thead>
                                                <tbody>
                                                    <?php foreach($$albumid as $row => $innerArray){ ?>
                                                        <tr>
                                                            <th class="centeredContent"><?php echo $innerArray['galerija_id']?></th>
                                                            <td class="centeredContent">
                                                                <img src="<?php echo $innerArray['galerija_link']?>" alt="" width="200px">
                                                            </td>
                                                            <td class="centeredContent"><?php echo $innerArray['galerija_datum']?></td>
                                                            <td class="centeredContent"><?php echo $innerArray['galerija_autor']?></td>
                                                            <td class="centeredContent"><?php echo $innerArray['galerija_opis']?></td>
                                                            <td class="centeredContent editGalerija editGalerijaImage">
                                                                <i 
                                                                    data-toggle="modal" 
                                                                    data-target="#editGalleryModal" 
                                                                    class="me-3 mdi mdi-pencil fs-3 editImageModalShow" 
                                                                    aria-hidden="true"
                                                                    data-id="<?php echo $innerArray['galerija_id'] ?>"
                                                                    data-link="<?php echo $innerArray['galerija_link'] ?>"
                                                                    data-date="<?php echo $innerArray['galerija_datum'] ?>"
                                                                    data-author="<?php echo $innerArray['galerija_autor'] ?>"
                                                                    data-desc="<?php echo $innerArray['galerija_opis'] ?>"
                                                                ></i>
                                                            </td>
                                                            <td class="centeredContent deleteItemGallery">
                                                                <i 
                                                                    class="me-3 mdi mdi-delete-forever fs-3" 
                                                                    aria-hidden="true"
                                                                    data-toggle="modal" 
                                                                    data-target="#deleteGalleryModal"
                                                                    dataID_="<?php echo $innerArray['galerija_id'] ?>"
                                                                    dataLink="galerija.php?deleteImage_id=<?php echo $innerArray['galerija_id'] ?>"
                                                                ></i>
                                                            </td>
                                                        </tr>
                                                    <?php } ?>
                                                </tbody>
                                            </table>
                                        <?php }
                                    ?>
                                </div>
                            </div>
                        </div>
                        <?php } 
                    } ?>
            </div>

            <!-- Modal INSERT ALBUM -->
            <div class="modal fade" id="insertAlbumModal">
                <div class="modal-dialog">
                    <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title">Dodaj album</h5>
                        <button type="button" class="close" data-dismiss="modal">
                            <span aria-hidden="true">&times;</span>
                        </button>
                    </div>
                    <form action="galerija.php" method="post" autocomplete="off" id="insert_form">
                        <div class="modal-body">

                            <!-- NASLOV -->
                            <label for="albumi_naziv">Naziv albuma:</label>
                            <input type="text" name="albumi_naziv" placeholder="Unesi naziv albuma..." required>
                            
                            <!-- DATUM -->
                            <label for="albumi_datum">Datum kreiranja:</label>
                            <input name="albumi_datum" type="timestamp" value="<?php echo date('d.m.Y. H:i:s', time()) ?>" readOnly>

                            <!-- AUTOR -->
                            <label for="albumi_autor">Autor:</label>
                            <input name="albumi_autor" type="text" value="<?php echo $_SESSION['User'] ?>" readOnly>

                        </div>
                        <div class="modal-footer">
                            <button type="button" class="btn btn-secondary" data-dismiss="modal">Odustani</button>
                            <button type="submit" class="btn btn-primary" name="submitAlbum">Sačuvaj</button>
                        </div>
                    </form>
                    </div>
                </div>
            </div>

            <!-- Modal INSERT IMAGES -->
            <div class="modal fade" id="insertGalleryModal">
                <div class="modal-dialog">
                    <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title">Dodaj sliku u album</h5>
                        <button type="button" class="close" data-dismiss="modal">
                            <span aria-hidden="true">&times;</span>
                        </button>
                    </div>
                    <form action="galerija.php" method="post" autocomplete="off" id="insert_form_" enctype='multipart/form-data'>
                        <div class="modal-body">

                            <!-- ID ALBUMA -->
                            <label for="galerija_idalbuma">ID albuma:</label>
                            <input id="galerija_idalbuma" name="galerija_idalbuma" type="text" value="" readOnly>

                            <!-- NAZIV ALBUMA -->
                            <label for="galerija_nazivalbuma">Naziv albuma:</label>
                            <input id="galerija_nazivalbuma" name="galerija_nazivalbuma" type="text" value="" readOnly>

                            <!-- SLIKA -->
                            <label for="addImages">Slika:</label>
                            <input type="file" name="files[]" id="addImages" required>
                            <p>Preporučena dimenzija slike: 800x600px</p>

                            <!-- OPIS -->
                            <label for="galerija_opis">Opis slike:</label>
                            <textarea name="galerija_opis" id="" cols="30" rows="10" placeholder="Unesi tekst novosti..." required></textarea>
                            
                            <!-- DATUM -->
                            <label for="galerija_datum">Datum i vrijeme unosa:</label>
                            <input name="galerija_datum" type="text" value="<?php echo date('d.m.Y. H:i:s', time()) ?>" readOnly>

                            <!-- AUTOR -->
                            <label for="galerija_autor">Autor:</label>
                            <input name="galerija_autor" type="text" value="<?php echo $_SESSION['User'] ?>" readOnly>
                        </div>
                        <div class="modal-footer">
                            <button type="button" class="btn btn-secondary" data-dismiss="modal">Odustani</button>
                            <button type="submit" class="btn btn-primary" name="submitGalerija">Sačuvaj</button>
                        </div>
                    </form>
                    </div>
                </div>
            </div>

            <!-- Modal EDIT -->
            <div class="modal fade" id="editAlbumModal">
                <div class="modal-dialog">
                    <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title">Uredi album</h5>
                        <button type="button" class="close" data-dismiss="modal">
                            <span aria-hidden="true">&times;</span>
                        </button>
                    </div>
                    <form action="galerija.php" method="post" autocomplete="off" id="edit_form" enctype='multipart/form-data'>
                        <div class="modal-body" id="poljaAlbum">
                            <!-- ID ALBUMA -->
                            <label for="galerija_editAlbumId">Naziv albuma:</label>
                            <input id="galerija_editAlbumId" name="galerija_editAlbumId" type="text" value="" readOnly>
                            <!-- NAZIV ALBUMA -->
                            <label for="galerija_editAlbum">Naziv albuma:</label>
                            <input id="galerija_editAlbum" name="galerija_editAlbum" type="text" value="">
                        </div>
                        <div class="modal-footer">
                            <button id="closeModalEditCar" type="button" class="btn btn-secondary" data-dismiss="modal">Odustani</button>
                            <button type="submit" class="btn btn-primary" name="submit_edit_album">Sačuvaj</button>
                        </div>
                    </form>
                    </div>
                </div>
            </div>

            <!-- MODAL DELETE ALBUM -->
            <div class="modal fade" id="deleteAlbumModal">
                <div class="modal-dialog">
                    <div class="modal-content">
                        <div class="modal-header">
                            <h5 class="modal-title">Da li ste sigurni da želite da obrišete album i sve slike u albumu?</h5>
                            <button type="button" class="close" data-dismiss="modal">
                                <span aria-hidden="true">&times;</span>
                            </button>
                        </div>
                        <div class="modal-body">
                        </div>
                        <div class="modal-footer">
                            <button type="button" class="btn btn-secondary" data-dismiss="modal">Odustani</button>
                            <a id="confirmDeleteAlbum" href="">
                                <button type="submit" class="btn btn-primary" name="submitDeleteAlbum">Obriši</button>  
                            </a>
                        </div>
                    </div>
                </div>
            </div>

            <!-- MODAL EDIT IMAGE -->
            <div class="modal fade" id="editGalleryModal">
                <div class="modal-dialog">
                    <div class="modal-content">
                        <div class="modal-header">
                            <h5 class="modal-title">Uredite sliku</h5>
                            <button type="button" class="close" data-dismiss="modal">
                                <span aria-hidden="true">&times;</span>
                            </button>
                        </div>
                        <form action="galerija.php" method="post" autocomplete="off" id="edit_form" enctype='multipart/form-data'>
                            <div class="modal-body" id="poljaEditGalerije">
                                <!-- ID ALBUMA -->
                                <label for="galerija_editImageId">Naziv albuma:</label>
                                <input id="galerija_editImageId" name="galerija_editImageId" type="text" value="" readOnly>
                                <!-- SLIKA -->
                                <label for="galerija_editImageImage">Slika:</label>
                                <img src="" alt="" id="galerija_editImageImage">
                                <!-- DATUM UNOSA -->
                                <label for="galerija_editImageDate">Datum unosa:</label>
                                <input id="galerija_editImageDate" name="galerija_editImageDate" type="text" value="" readOnly>
                                <!-- AUTOR -->
                                <label for="galerija_editImageAuthor">Autor:</label>
                                <input id="galerija_editImageAuthor" name="galerija_editImageAuthor" type="text" value="" readOnly>
                                <!-- OPIS -->
                                <label for="galerija_editImageDesc">Opis:</label>
                                <textarea id="galerija_editImageDesc" name="galerija_editImageDesc"></textarea>
                            </div>
                            <div class="modal-footer">
                                <button id="closeModalEditCar" type="button" class="btn btn-secondary" data-dismiss="modal">Odustani</button>
                                <button type="submit" class="btn btn-primary" name="submit_edit_image">Sačuvaj</button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>

            <!-- MODAL DELETE IMAGE -->
            <div class="modal fade" id="deleteGalleryModal">
                <div class="modal-dialog">
                    <div class="modal-content">
                        <div class="modal-header">
                            <h5 class="modal-title">Da li ste sigurni da želite da obrišete sliku?</h5>
                            <button type="button" class="close" data-dismiss="modal">
                                <span aria-hidden="true">&times;</span>
                            </button>
                        </div>
                        <div class="modal-body">
                        </div>
                        <div class="modal-footer">
                            <button type="button" class="btn btn-secondary" data-dismiss="modal">Odustani</button>
                            <a id="confirmDeleteGallery" href="">
                                <button type="submit" class="btn btn-primary" name="submitDeleteImage">Obriši</button>  
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

        $(".addimagebutton").click(function(){
            var albumid = $(this).attr('data-albumid');
            var albumnaziv = $(this).attr('data-albumnaziv');
            $("#galerija_idalbuma").val(albumid);
            $("#galerija_nazivalbuma").val(albumnaziv);
        });

        $(".editAlbumId").click(function(){
            var wantedId = $(this).attr('data-id');
            var wantedName = $(this).attr('data-naziv');
            $("#galerija_editAlbumId").val(wantedId);
            $("#galerija_editAlbum").val(wantedName);
        });

        $(".editImageModalShow").click(function(){
            var idZaEdit = $(this).attr('data-id');
            var linkZaEdit = $(this).attr('data-link');
            var dateZaEdit = $(this).attr('data-date');
            var authorZaEdit = $(this).attr('data-author');
            var descZaEdit = $(this).attr('data-desc');
            $("#galerija_editImageId").val(idZaEdit);
            $("#galerija_editImageImage").attr('src',linkZaEdit);
            $("#galerija_editImageDate").val(dateZaEdit);
            $("#galerija_editImageAuthor").val(authorZaEdit);
            $("#galerija_editImageDesc").html(descZaEdit);
        });

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