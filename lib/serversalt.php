<?php

// Generate a large random hexadecimal salt.
function generateRandomSalt($len = 128)
{
    $randomSalt = NULL;
    if (function_exists("mcrypt_create_iv"))
    {
        $randomSalt = bin2hex(mcrypt_create_iv(256, MCRYPT_DEV_URANDOM));
    }
    else 
    {
        generateRandomString($len);
    }

    return $randomSalt;
}

function generateRandomString($len = 128, $lowercase_only = false)
{
    $characters = '0123456789abcdefghijklmnopqrstuvwxyz';

    if(!$lowercase_only)
    {
        $characters .= 'ABCDEFGHIJKLMNOPQRSTUVWXYZ';
    }

    $string = NULL;
    for($i = 0; $i < $len; $i++) 
    { 
        $string .= $characters[mt_rand(0, strlen($characters) -1)];
    }
    return $string;
}

function generateSaltFile($file)
{
    return file_put_contents($file,'<?php die(); /* |'.generateRandomSalt().'| */ ?>', LOCK_EX);
}

function getSaltFromFile($file)
{
    if(!is_file($file))
    {
        $gen = generateSaltFile($file);
        if($gen == false)
            return false;
    }

    $items = explode('|',file_get_contents($file));
    if(!isset($items[1]))
        return false; // ?

    return $items[1];
}


function getServerSalt()
{
    global $_config;

    return getSaltFromFile( $_config[ 'data_dir' ]. '/salt.php' );
}



function getPasteSalt( $pasteid )
{
    global $_config;

    $file = dataid2path ( $pasteid ).$pasteid.$_config[ 'salt_append '];
    return getSaltFromFile($file);
}

?>
