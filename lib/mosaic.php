<?php

namespace mosaic {

    use Exception;

    class auth //done
    {
        function __construct()
        {
            return;
        }
        //Function to authorize users
        function authorize()
        {

            //set the parameters for what we're authorizing
            $params = [
                'client_id=8ef01039251f4b9a8a213ae17ef0e570',
                'response_type=code',
                'redirect_uri=https://mosaic-dev.paos.io/create.php',
                'scope=playlist-read-private%20playlist-read-collaborative%20user-top-read'
            ];

            //make it a string
            $authparams = implode('&', $params);

            //redirect url
            $url = "https://accounts.spotify.com/authorize?$authparams";

            //return the redirect action
            header("Location: $url");
        }
        //Function to get bearer token for users
        public static function authenticate($code, $type)
        {
            //set the url to the authenticate api
            $url = 'https://accounts.spotify.com/api/token';

            //set the parameters for what we're authorizing
            if ($type == 'authorization_code') {
                $params = [
                    "code=$code",
                    "grant_type=$type",
                    'redirect_uri=https://mosaic-dev.paos.io/create.php'
                ];
            } else {
                $params = [
                    "refresh_token=$code",
                    "grant_type=$type",
                    'redirect_uri=https://mosaic-dev.paos.io/create.php'
                ];
            }

            //make it a string
            $body = implode('&', $params);

            //get secret
            $secret = json_decode(file_get_contents('creds.json'), true);

            //build credentials for the actual system to authenticate with spotify
            $sysauth = base64_encode('8ef01039251f4b9a8a213ae17ef0e570' . ':' . $secret['secret']);

            //build authorization header
            $header = array(
                "Authorization: Basic $sysauth",
                'Content-Type: application/x-www-form-urlencoded'
            );

            //send the post request

            $curl = curl_init();

            curl_setopt_array($curl, array(
                CURLOPT_URL => $url,
                CURLOPT_RETURNTRANSFER => true,
                CURLOPT_FOLLOWLOCATION => true,
                CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
                CURLOPT_CUSTOMREQUEST => 'POST',
                CURLOPT_POSTFIELDS => $body,
                CURLOPT_HTTPHEADER => $header,
            ));

            $response = curl_exec($curl);

            curl_close($curl);

            return $response;
        }
        //Function to see if the user actually exists
        function login($token, $refreshtoken, $guid)
        {

            //create auth header
            $context = stream_context_create([
                "http" => [
                    "header" => "Authorization: Bearer $token"
                ]
            ]);

            //return user info
            $user = json_decode(file_get_contents('https://api.spotify.com/v1/me', false, $context), true);
            $lookup = json_decode(file_get_contents('/var/www/html/db/mosaic-dev/tokens.json'), true);
            foreach ($lookup as $item) {
                if ($item['username'] == $user['id'] and $item['guid'] == $guid) {
                    setcookie('username', $user['id'], 0, '/');
                    setcookie('refresh', $refreshtoken, 0, '/');
                    setcookie('session', $item['guid'], 0, '/');
                    break;
                }
            }
        }
        function logout()
        {
            setcookie('username', null, 0, '/');
            setcookie('refresh', null, 0, '/');
        }
        function verify($token)
        {

            //create auth header
            $context = stream_context_create([
                "http" => [
                    "header" => "Authorization: Bearer $token"
                ]
            ]);

            //return user info
            return file_get_contents('https://api.spotify.com/v1/me', false, $context);
        }
    }
    class playlist
    {
        function __construct()
        {
            return;
        }
        //Function that gets all collaborative playlists related to a person's account
        function get($token)
        {
        }
        //Function that will create a playlist for the new year, arguments should probably include how many songs to include from a playlist. For example: "top 5 songs from each list"
        function create($token)
        {
        }
    }
    class user //done
    {
        function __construct()
        {
            return;
        }
        //Function to pull all user tokens from our text database to use for interacting with spotify under their name
        function get($id = null)
        {
            if ($id != null) {
                $users = json_decode(file_get_contents('/var/www/html/db/mosaic-dev/tokens.json'), true);
                foreach ($users as $item) {
                    if ($item['username'] == $id) {
                        return json_encode($item);
                        break;
                    }
                }
            } else {
                return file_get_contents('/var/www/html/db/mosaic-dev/tokens.json');
            }
        }
        //Function to append a new user to that text database
        function create($id, $refreshtoken, $private = null)
        {
            $found = 'no';
            $obj = [];
            $obj['username'] = $id;
            $obj['token'] = $refreshtoken;
            $obj['private'] = false;
            $obj['guid'] = uniqid();

            //open json array of tokens
            $tokens = json_decode(file_get_contents('/var/www/html/db/mosaic-dev/tokens.json'), true);
            foreach ($tokens as $token) {
                if ($token['username'] == $obj['username']) {
                    $found = 'yes';
                    $guid = $token['guid'];
                    break;
                } else {
                }
            }
            if ($found == 'yes') {
                $result['message'] = 'User exists, logging in';
                $result['guid'] = $guid;
            } else {
                array_push($tokens, $obj);
                $file = fopen('/var/www/html/db/mosaic-dev/tokens.json', 'w');
                fwrite($file, json_encode($tokens));
                fclose($file);
                $result['message'] = 'Successfully added user';
                $result['guid'] = $obj['guid'];
            }
            return json_encode($result);
        }
        function update($id, $guid, $private)
        {
            $users = json_decode(file_get_contents('/var/www/html/db/mosaic-dev/tokens.json'), true);
            $array = [];
            foreach ($users as $item) {
                if ($item['username'] == $id and $item['guid'] == $guid) {
                    $item['private'] = $private;
                    array_push($array, $item);
                } else {
                    array_push($array, $item);
                }
            }
            $file = fopen('/var/www/html/db/mosaic-dev/tokens.json', 'w');
            fwrite($file, json_encode($array));
            fclose($file);
        }
    }
    class top
    {
        function __construct()
        {
            return;
        }
        //Function that gets all collaborative playlists related to a person's account
        function get($token = null, $id = null)
        {
            if ($id != null) {
                $users = json_decode(file_get_contents('/var/www/html/mosaic-dev/lists.json'), true);
                foreach ($users as $user) {
                    if ($user['id'] == $id) {
                        return json_encode($user);
                        break;
                    }
                }
            } else {
                $result = array();

                //create auth header
                $context = stream_context_create([
                    "http" => [
                        "header" => "Authorization: Bearer $token"
                    ]
                ]);

                //start parsing for collabs
                $playlists = json_decode(file_get_contents('https://api.spotify.com/v1/me/top/tracks?time_range=short_term', false, $context), true);



                foreach ($playlists['items'] as $playlist) {
                    $track['name'] = $playlist['name'];
                    $track['link'] = $playlist['external_urls']['spotify'];
                    array_push($result, $track);
                }
                return json_encode($result);
            }
        }
    }
}
