<?php


function innlogging(){ 
  #Henter ut feltene ifra skjemaet og gjør dem om til variable
    $epost = $_POST['epost'];
    $passord = $_POST['passord'];
    $robot = $_POST['robot'];
#Ser om Eposten er gyldig
if(!filter_var($epost, FILTER_VALIDATE_EMAIL)){
      echo "ugyldig epost <br>";
    }
#sjekker om en robot fyller ut det skjulte feltet
elseif(!$robot == NULL){
  echo "Ha deg vekk din ekle robot <br>";
}
#Om ingen av feilene utløses, så legges informasjonen ifra skjemaet inn i bruker listen 
#(Bare en mockup, har ikke begynt på funksjonalitet)
else{
    #Liste med brukere
    $brukere = [];
    $brukere[] = ["fornavn" => "Mikael", "etternavn" => "Nyborg", "epost" => "mikaelnyb@gmail.com", 
    "telefon" => "45186828", "passord" => "Ingenting"];
    for($i = 0; $i < count($brukere); $i++){
    foreach($brukere[$i] as $k => $v){
        echo "$k $v";
    }}
}
}
#Starter if setningen dersom en POST metode blir gjort på nettsiden
if ($_SERVER['REQUEST_METHOD'] === 'POST'){
    innlogging();
}


?>