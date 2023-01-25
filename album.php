<?php 

    include_once('includes/head.php');
    include_once('includes/header.php');

    $id_albuma = $_GET['album'];

    class Album {
        public function fetchAlbum($album_id) {
            global $pdo;
            $query = $pdo->prepare("SELECT * FROM albumi WHERE albumi_id = ? ORDER BY albumi_id ASC");
            $query->bindValue(1, $album_id);
            $query->execute();
            return $query->fetch();
        }
    }

    $album = new Album;
    $albumi = $album->fetchAlbum($id_albuma);

    class Slike {
      public function fetchImages($album_id) {
          global $pdo;
          $query = $pdo->prepare("SELECT * FROM galerija WHERE album_id = ? ORDER BY galerija_id ASC");
          $query->bindValue(1, $album_id);
          $query->execute();
          return $query->fetchAll();
      }
  }
  
  $slika = new Slike;
  $slike = $slika->fetchImages($id_albuma);

?>

<section class="page-title bg-1">
  <div class="overlay"></div>
  <div class="container">
    <div class="row">
      <div class="col-md-12">
        <div class="block text-center">
          <span class="text-white">ALBUM</span>
          <h1 class="text-capitalize mb-5 text-lg"><?php echo $albumi['albumi_naziv'] ?></h1>
        </div>
      </div>
    </div>
  </div>
</section>

<section class="customSection">
    <div class="container">
      <div class="row">
          <?php foreach($slike as $slika) { ?>
            <div class="col-lg-4">
            <img width="100%" class="lightboxed" rel="group1" src="admin/<?php echo $slika['galerija_link'] ?>" data-link="admin/<?php echo $slika['galerija_link'] ?>" alt="Image Alt" data-caption="<?php echo $slika['galerija_opis'] ?>" />
            </div>
          <?php } ?>
        </div>
      </div>
    </div>
</section>


<?php include_once('includes/footer.php'); ?>