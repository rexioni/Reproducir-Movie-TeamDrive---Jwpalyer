<?php
error_reporting(E_ERROR | E_PARSE);
require __DIR__ . './google-drive.php';

define('apikeyGoogle', 'AIzaSyAo1UC6c83biUv0bb_Bn-t8AHVQk2zouV0');//Claves de API
define('api_key', 'a93c0a63c2f3747bea667d520a7cd939');//key TMDB https://www.themoviedb.org/signup  
define('apiIdioma', 'es-MX');//el Idioma Segun Themoviedb "es-MX = Latino es = Español"
define('calidad', '480p');// esta opción son para la calidad por defecto del video Segun JWplayer las mas comerciales[480p, 720p,1080p]


if (isset($_GET['folder']) && isset($_GET['idTMDB']) && isset($_GET['temp'])) {

    $xx1 = $_GET['folder'];
    $xx2 = $_GET['idTMDB'];
    $xx3 = $_GET['temp'];

    header("Content-Type: application/json; charset=UTF-8");
    TeamDriveListaTV($xx1, $xx2, $xx3);

}
if (isset($_GET['folder']) && isset($_GET['idTMDB']) && isset($_GET['movie']) ) {

    $xx1 = $_GET['folder'];
    $xx2 = $_GET['idTMDB'];
  

    header("Content-Type: application/json; charset=UTF-8");
    TeamDriveMovie($xx1, $xx2);

}

function TeamDriveListaTV($xx1, $xx2, $xx3)
{
    $apikeyGoogle = apikeyGoogle;
    $api_key = api_key;
    $apiIdioma = apiIdioma;
    $calidad = calidad ; 

    $folderId = $xx1; 
    $idTMDB = $xx2;
    $tem = $xx3; 
    //TMDB
   
    $url = file_get_contents("https://api.themoviedb.org/3/tv/$idTMDB/season/$tem?api_key=$api_key&language=$apiIdioma");
    $listaTemporadas = json_decode($url, true);
    //API GOOGLE DRIVE
    $service = new Google_Service_Drive($GLOBALS['client']);
    $keyApiDrive = "?alt=media&key=$apikeyGoogle";
    $urlTeamdrive = "https://www.googleapis.com/drive/v3/files/";
    $optParams = array('supportsTeamDrives' => true);
    $results = $service->files->get($folderId, $optParams);
    $teamDriveId = $results["teamDriveId"];
    $params = [
        'q' => "'{$folderId}' in parents",
        'pageSize' => $pageSize,
        'corpora' => 'teamDrive',
        'includeTeamDriveItems' => true,
        'supportsTeamDrives' => true,
        'teamDriveId' => $teamDriveId, // Muestra el id principal del Team Drive donde esta la carpeta
        'orderBy' => 'name',
    ];
    $files = $service->files->listFiles($params);

    for ($i = 0; $i < count($files['files']); $i++) {
        $partes_ruta = pathinfo($files['files'][$i]['name']);
        $porciones = explode("[", $files['files'][$i]['name']);
        $archivos[] = array(
            "name" => $porciones[0],
            "resolucion" => str_replace("].mp4", "", $porciones[1]),
            "idFile" => $files['files'][$i]['id'],
        );
    }
    $y = 1;
    $datos = array();
    $newDato = array();
    $j = 1;
    foreach ($archivos as $i => $dato) {
        if ($dato['name'] == $j) {
            foreach ($dato as $key => $value) {
                $newDato[$key] = ($value);
            }
        } else {
            $j++;
            $datos[] = $newDato;
            $newDato = array();
            foreach ($dato as $key => $value) {
                $newDato[$key] = $value;
            }
        }
    }
    $datos[] = $newDato;
    echo '[', "\n";
    for ($i = 0; $i < count($datos); $i++) {
        $num = intval($datos[$i]['name']);
        echo '{
            "title": "' . $listaTemporadas['episodes'][$num - 1]['name'] . '(' . $num . ')",
            "description": "' . $listaTemporadas['episodes'][$num - 1]['overview'] . '",
            "image": "https://image.tmdb.org/t/p/w1280' . $listaTemporadas['episodes'][$num - 1]['still_path'] . '",
            "sources":[';
        for ($yy = 0; $yy < count($archivos); $yy++) {

            if ($archivos[$yy]['name'] == $datos[$i]['name']) {
                if ($archivos[$yy]['resolucion'] == $calidad) {$default = ',"default": "true"';} else { $default = '';}
                $datosJson .= '{
                    "file": "' . $urlTeamdrive . $archivos[$yy]['idFile'] . $keyApiDrive . '",
                    "label": "' . $archivos[$yy]['resolucion'] . '",
                    "type": "mp4",
                    "primary": "html5",
                    "name": "' . $archivos[$yy]['name'] . '"
                    '.$default.'    
                },';
            }
        }
        $datosJson = substr($datosJson, 0, -1);
        $datosJson .= ']';

        echo $datosJson;
        echo '}', "\n";
        if (($i + 1) < count($datos)) {
            echo ',';
        }
        $datosJson = '';
    }
    echo ']', "\n";
}
function TeamDriveMovie($xx1, $xx2)
{
    $apikeyGoogle = apikeyGoogle;
    $api_key = api_key;
    $apiIdioma = apiIdioma;
    $calidad = calidad ; 

    $folderId = $xx1; 
    $idTMDB = $xx2;
    $tem = $xx3; 
    //TMDB
    $url = file_get_contents("https://api.themoviedb.org/3/movie/$idTMDB?api_key=$api_key&language=$apiIdioma");
    $listaTemporadas = json_decode($url, true);
    //API GOOGLE DRIVE
    $service = new Google_Service_Drive($GLOBALS['client']);
    $keyApiDrive = "?alt=media&key=$apikeyGoogle";
    $urlTeamdrive = "https://www.googleapis.com/drive/v3/files/";
    $optParams = array('supportsTeamDrives' => true);
    $results = $service->files->get($folderId, $optParams);
    $teamDriveId = $results["teamDriveId"];
    $params = [
        'q' => "'{$folderId}' in parents",
        'pageSize' => $pageSize,
        'corpora' => 'teamDrive',
        'includeTeamDriveItems' => true,
        'supportsTeamDrives' => true,
        'teamDriveId' => $teamDriveId, // Muestra el id principal del Team Drive donde esta la carpeta
        'orderBy' => 'name',
    ];
    $files = $service->files->listFiles($params);

    for ($i = 0; $i < count($files['files']); $i++) {
        $partes_ruta = pathinfo($files['files'][$i]['name']);
        $porciones = explode("[", $files['files'][$i]['name']);
        $archivos[] = array(
            "name" => $porciones[0],
            "resolucion" => str_replace("].mp4", "", $porciones[1]),
            "idFile" => $files['files'][$i]['id'],
        );
    }
    $y = 1;
    $datos = array();
    $newDato = array();
    $j = 1;
    foreach ($archivos as $i => $dato) {
        if ($dato['name'] == $j) {
            foreach ($dato as $key => $value) {
                $newDato[$key] = ($value);
            }
        } else {
            $j++;
            $datos[] = $newDato;
            $newDato = array();
            foreach ($dato as $key => $value) {
                $newDato[$key] = $value;
            }
        }
    }
    $datos[] = $newDato;
    echo '[', "\n";
    for ($i = 0; $i < count($datos); $i++) {
        $num = intval($datos[$i]['name']);
        echo '{
            "title": "' . $listaTemporadas['title']. '(' . $num . ')",
            "description": "' . $listaTemporadas['overview'] . '",
            "image": "https://image.tmdb.org/t/p/w1280' . $listaTemporadas['backdrop_path'] . '",
            "sources":[';
        for ($yy = 0; $yy < count($archivos); $yy++) {

            if ($archivos[$yy]['name'] == $datos[$i]['name']) {
                if ($archivos[$yy]['resolucion'] == $calidad) {$default = ',"default": "true"';} else { $default = '';}
                $datosJson .= '{
                    "file": "' . $urlTeamdrive . $archivos[$yy]['idFile'] . $keyApiDrive . '",
                    "label": "' . $archivos[$yy]['resolucion'] . '",
                    "type": "mp4",
                    "primary": "html5",
                    "name": "' . $archivos[$yy]['name'] . '"
                    '.$default.'    
                },';
            }
        }
        $datosJson = substr($datosJson, 0, -1);
        $datosJson .= ']';

        echo $datosJson;
        echo '}', "\n";
        if (($i + 1) < count($datos)) {
            echo ',';
        }
        $datosJson = '';
    }
    echo ']', "\n";
}



