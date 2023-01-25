<?php

include_once('../connection.php'); 

$id = intval($_POST['id']);

class Slika {
    public function fetch_data_slika($id) {
        global $pdo;
        $query = $pdo->prepare("SELECT * FROM novostislike WHERE novostislike_id = ?");
        $query->bindValue(1, $id);
        $query->execute();
        return $query->fetch();
    }
}

$slika = new Slika;
$slike = $slika->fetch_data_slika($id);

//get image path
$imageUrl = $slike['novostislike_link'];

//check if image exists
if(file_exists($imageUrl)){

    //delete the image
    unlink($imageUrl);

}

$query = $pdo->prepare('DELETE FROM novostislike WHERE novostislike_id = ?');
$query->bindValue(1, $id);
$query->execute();


?>