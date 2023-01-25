<?php 

    include_once('includes/head.php');
    include_once('includes/header.php');

    $id = $_GET['id'];

    /* POVUCI NOVOSTI */
    class News {
        public function fetch($id) {
            global $pdo;
            $query = $pdo->prepare("SELECT * FROM novosti WHERE novosti_id = ?");
            $query->bindValue(1, $id);
            $query->execute();
            return $query->fetch();
        }
    }
    
    $news = new News;
    $novosti = $news->fetch($id);

    /* POVUCI SLIKU */
    class Images {
        public function fetchImages($q) {
            global $pdo;
            $query = $pdo->prepare("SELECT * FROM novostislike WHERE novosti_id=?");
            $query->bindValue(1, $q);
            $query->execute();
            return $query->fetch();
        }
    }

    $image = new Images;
    $images = $image->fetchImages($novosti['novosti_id']);

?>

<section class="page-title bg-1">
  <div class="overlay"></div>
  <div class="container">
    <div class="row">
      <div class="col-md-12">
        <div class="block text-center">
          <span class="text-white"></span>
          <h1 class="text-capitalize mb-5 text-lg">AKTUELNOSTI</h1>
        </div>
      </div>
    </div>
  </div>
</section>

<section class="customSection">
    <div class="container">
        <div class="row">
        <?php
            $datum = $novosti['novosti_datum'];
            $datumNiz = explode("-", $datum);
            switch ($datumNiz[1]) {
                case '01':
                    $mjesec = "JAN";
                    break;
                case '02':
                    $mjesec = "FEB";
                    break;
                case '03':
                    $mjesec = "MAR";
                    break;
                case '04':
                    $mjesec = "APR";
                    break;
                case '05':
                    $mjesec = "MAJ";
                    break;
                case '06':
                    $mjesec = "JUN";
                    break;
                case '07':
                    $mjesec = "JUL";
                    break;
                case '08':
                    $mjesec = "AVG";
                    break;
                case '09':
                    $mjesec = "SEP";
                    break;
                case '10':
                    $mjesec = "OKT";
                    break;
                case '11':
                    $mjesec = "NOV";
                    break;
                case '12':
                    $mjesec = "DEC";
                    break;
            }

            ?>
            <div class="col-md-12">
              <div class="article">
                <div class="article-image">
                    <img src="<?php echo "admin/".$images['novostislike_link'] ?>" alt="">
                    <!-- <p class="article-date">
                        <span><?php //echo $datumNiz[2] ?></span><br><?php //echo $mjesec ?>
                    </p> -->
                </div>
                <div class="article-content">
                    <div class="article-author">
                        <i class="fa-regular fa-user"></i>
                        <p><?php echo $novosti['novosti_autor'] ?></p>
                        <i class="fa-regular fa-calendar"></i>
                        <p><?php echo $datumNiz[2]." ".$mjesec ?></p>
                    </div>
                    <div class="article-text_">
                        <h3><?php echo $novosti['novosti_naslov'] ?></h3>
                        <p><?php echo $novosti['novosti_text'] ?></p>
                    </div>
                    <!-- <div class="article-button">ВИШЕ</div> -->
                </div>
              </div>
            </div>
        </div>
    </div>
</section>


<?php include_once('includes/footer.php'); ?>