<?php
#Starter if setningen dersom en POST metode blir gjort på nettsiden
function registrering(){ 
  #Henter ut feltene ifra skjemaet og gjør dem om til variable
    $fornavn = $_POST['fornavn'];
    $etternavn = $_POST['etternavn'];
    $epost = $_POST['epost'];
    $telefon = $_POST['telefon'];
    $passord = $_POST['passord'];
    $bekreftPassord = $_POST['bekreftPassord'];
    $robot = $_POST['robot'];
#Sjekker om fornavn og etternavn er mer en ett symbol
if(strlen($fornavn) <= 1){
      echo "Ugyldig fornavn <br>";
    }
elseif(strlen($etternavn) <= 1){
      echo "Ugyldig etternavn <br>";
    }
#Ser om Eposten er gyldig
elseif(!filter_var($epost, FILTER_VALIDATE_EMAIL)){
      echo "ugyldig epost <br>";
    }
#sjekker om en robot fyller ut det skjulte feltet
elseif(!$robot == NULL){
  echo "Ha deg vekk din ekle robot <br>";
}
elseif($passord != $bekreftPassord){
    echo "Passordene må være like";
}
#Om ingen av feilene utløses, så legges informasjonen ifra skjemaet inn i bruker listen
else{
    $brukere[] = [
        'fornavn' => $fornavn,
        'etternavn' => $etternavn,
        'epost' => $epost,
        'telefon' => $telefon,
        'passord' => $passord
    ];  
    echo "<br>Bruker lagt til! <br>";
    #Sjekker lengden av listen brukere, for å så kunne bruke det for å finne det nyeste elementet i listen
    $bruker = $brukere[count($brukere) -1];
      echo "Fornavn: " . $bruker["fornavn"] . "<br>";
      echo "Etternavn: " . $bruker["etternavn"] . "<br>";
      echo "Epost: " . $bruker["epost"] . "<br>";
      echo "Telefon: " . $bruker["telefon"] . "<br>";
    }
}

if ($_SERVER['REQUEST_METHOD'] === 'POST'){
    registrering();
}

?>