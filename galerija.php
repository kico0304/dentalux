<?php 

    include_once('includes/head.php');
    include_once('includes/header.php');

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
  //$slike = $slika->fetchImages();

?>

<section class="page-title bg-1">
  <div class="overlay"></div>
  <div class="container">
    <div class="row">
      <div class="col-md-12">
        <div class="block text-center">
          <span class="text-white"></span>
          <h1 class="text-capitalize mb-5 text-lg">GALERIJA</h1>
        </div>
      </div>
    </div>
  </div>
</section>

<section class="customSection">
    <div class="container">
      <div class="row">
          <?php foreach($albumi as $album) { ?>
            <div class="col-lg-4">
              <a href="album.php?album=<?php echo $album['albumi_id'] ?>">
              <div class="singleAlbumFront">
                <?php ${"slike".$album['albumi_id']} = $slika->fetchImages($album['albumi_id']);
                ${"i".$album['albumi_id']} = 0;
                foreach(${"slike".$album['albumi_id']} as ${"slika".$album['albumi_id']}){ 
                if(${"i".$album['albumi_id']} == 0){ ?>
                  <div class="singleAlbum-imagePart">
                    <img width="200px" src="./admin/<?php echo ${"slika".$album['albumi_id']}['galerija_link'] ?>" alt="">
                    <div class="albumImageOverlay"></div>
                  </div>
                  <?php }   
                  ${"i".$album['albumi_id']}++; } ?>
                  <div class="singleAlbum-headlinePart">
                    <p><?php echo $album['albumi_naziv'] ?></p>
                  </div>
              </div>
              </a>
            </div>
          <?php } ?>
        </div>
      </div>
    </div>
</section>


<?php include_once('includes/footer.php'); ?>