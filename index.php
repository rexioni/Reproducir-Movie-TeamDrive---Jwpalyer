
<!DOCTYPE html>
<html lang="Es">

<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <script src="https://content.jwplatform.com/libraries/R6N0vQlD.js"></script>
    <title>Script TeamDrive</title>
</head>

<body>
<?php
//folder=1N9_jblXG8tME1pEXJGx-Yr9VufrR5mkl&idTMDB=88396&temp=1
if (isset($_GET['folder']) && isset($_GET['idTMDB']) && isset($_GET['temp'])) {

    $xx1 = $_GET['folder'];
    $xx2 = $_GET['idTMDB'];
    $xx3 = $_GET['temp'];

    ?> 
    <div id="container"></div>
    <script>
        var playerInstance = jwplayer("container");
        playerInstance.setup({
            playlist: "generador.php?folder=<?php echo $xx1;?>&idTMDB=<?php echo $xx2;?>&temp=<?php echo $xx3;?>",
            displaytitle: true,
            width: "100%",
            aspectratio: "16:9"
        });
    </script>

<?php 

}else if (isset($_GET['folder']) && isset($_GET['idTMDB']) ) {

    $xx1 = $_GET['folder'];
    $xx2 = $_GET['idTMDB'];


    ?> 
    <div id="container"></div>
    <script>
        var playerInstance = jwplayer("container");
        playerInstance.setup({
            playlist: "generador.php?folder=<?php echo $xx1;?>&idTMDB=<?php echo $xx2;?>&movie=si",
            displaytitle: true,
            width: "100%",
            aspectratio: "16:9"
        });
    </script>

<?php 

}

else {
    echo '<h1>Reproduce Tus Videos Desde TeamDrive en Jwplayer con la API TMDB</h1>
    <h2>El formato para Series de TV</h2>
    <p> index.php?folder=[TU CARPETA]&idTMDB=[ID THEMOVIE]&temp=[TEMPORADA] </p>
    <h2>El formato para Peliculas</h2>
    <p> index.php?folder=[TU CARPETA]&idTMDB=[ID THEMOVIE]&movie=si</p>
    
    ';
    
}

?>
</body>

</html>
    

