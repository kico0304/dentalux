<?php

include_once('../connection.php'); 

$q = intval($_GET['act']);

class Novost {
    //jedan auto
    public function fetch_data_info($q) {
        global $pdo;
        $query = $pdo->prepare("SELECT * FROM novosti WHERE novosti_id = ?");
        $query->bindValue(1, $q);
        $query->execute();
        return $query->fetch();
    }
}

$novost = new Novost;
$novosti = $novost->fetch_data_info($q);

class Slike {
    //jedan auto
    public function fetch_slike_info($q) {
        global $pdo;
        $query = $pdo->prepare("SELECT * FROM novostislike WHERE novosti_id = ?");
        $query->bindValue(1, $q);
        $query->execute();
        return $query->fetchAll();
    }
}

$slika = new Slike;
$slike = $slika->fetch_slike_info($q);

$numberOfImg = count($slike);

echo "

    <!-- ID NOVOSTI -->
    <label for='novosti_id'>ID novosti:</label>
    <input type='text' name='novosti_id' value='$novosti[novosti_id]' readOnly>

    <!-- NASLOV -->
    <label for='novosti_naslov'>Naslov:</label>
    <input type='text' name='novosti_naslov' value='$novosti[novosti_naslov]' placeholder='Unesi naslov novosti...' oninvalid='this.setCustomValidity('Ovo polje je obavezno')' required>
    
    <!-- TEXT -->
    <label for='novosti_text'>Tekst:</label>
    <textarea id='hereaddbreak' name='novosti_text' id='' cols='30' rows='10' placeholder='Unesi tekst novosti...' oninvalid='this.setCustomValidity('Ovo polje je obavezno')' required>$novosti[novosti_text]</textarea>
    <a class='btn btn-secondary' onclick='insertAtCaret(\"hereaddbreak\",\"<br>\");'>Dodaj novi red</a>

    <!-- DATUM -->
    <label for='novosti_datum'>Datum unosa:</label>
    <input name='novosti_datum' type='date' value='$novosti[novosti_datum]' readOnly>

    <!-- AUTOR -->
    <label for='novosti_autor'>Autor:</label>
    <input name='novosti_autor' type='text' value='$novosti[novosti_autor]' readOnly>

    <div style='display: flex;flex-direction: column;align-items: flex-start;'>";
    
    if($numberOfImg != 0){
       foreach ($slike as $slika) {
            echo"
            <label for='novosti_slike'>Slika:</label>
            <img width='150px' src='$slika[novostislike_link]'>
            <a class='deleteThisImage btn btn-secondary' onclick='obrisiSliku(".$slika['novostislike_id'].")'>Obriši sliku</a>
            ";
        }

        echo"</div>";
    } else{
        echo "<label for='novosti_slike'>Slika:</label>
        <p class='font14'>Ova novost nema ni jednu sliku.</p>";
        echo "
        <label for='addImages'>Dodaj sliku:</label>
        <input type='file' name='files[]' id='addImages'>
    ";
    }

?>