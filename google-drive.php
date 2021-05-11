<?php
require __DIR__ . './api-google/vendor/autoload.php';
function getClient()
{
    $client = new Google_Client();
    $client->setApplicationName('Google Drive API PHP');
    $client->setRedirectUri('https://developers.google.com/oauthplayground');
    $client->setScopes(array(
        Google_Service_Drive::DRIVE_FILE,
        Google_Service_Drive::DRIVE)
    );
    $client->setAuthConfig('credentials.json');
    $client->setAccessType('offline');
    $client->setPrompt('select_account consent');
    $tokenPath = 'token.json';
    if (file_exists($tokenPath)) {
        $accessToken = json_decode(file_get_contents($tokenPath), true);
        $client->setAccessToken($accessToken);
    }
    // Si no hay un token anterior o está vencido.
    if ($client->isAccessTokenExpired()) {
        //Actualice el token si es posible, de lo contrario, busque uno nuevo.
        if ($client->getRefreshToken()) {
            $client->fetchAccessTokenWithRefreshToken($client->getRefreshToken());
        } else {
            // Solicitar autorización del usuario.
            $authUrl = $client->createAuthUrl();
           // printf("Abra el siguiente enlace en su navegador:\n%s\n", $authUrl);
            //print 'Enter verification code: ';
            $authCode = trim(fgets(STDIN));
            // Código de autorización de intercambio por un token de acceso.
            $accessToken = $client->fetchAccessTokenWithAuthCode($authCode);
            $client->setAccessToken($accessToken);

            // Verifique si hubo un error.
            if (array_key_exists('error', $accessToken)) {
                throw new Exception(join(', ', $accessToken));
            }
        }
        //Guarde el token en un archivo.
        if (!file_exists(dirname($tokenPath))) {
            mkdir(dirname($tokenPath), 0700, true);
        }
        file_put_contents($tokenPath, json_encode($client->getAccessToken()));
    }
    return $client;
}

// Obtenga el cliente API y construya el objeto de servicio.
$client = getClient();
