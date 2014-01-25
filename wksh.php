<?php
 /*
 Plugin Name: Szyfrator WKSH
 Plugin URI: https://github.com/apsg/wksh
 Description: Szyfrator, rozwinięcie dawnej Wielkiej Księgi Szyfrów Harcerskich
 Version: 0.1.0
 Author: phm. Szymon Gackowski
 Author URI: http://pasterzdrzew.pl
 License: GPL2
 */

//-----------------------------------------------------------------------------------
//SETUP
function wksh_install(){
     //Do some installation work
}
register_activation_hook(__FILE__,'wksh_install');

//-----------------------------------------------------------------------------------
//SCRIPTS
 function wksh_scripts(){
     wp_register_script('wksh_script',plugin_dir_url( __FILE__ ).'js/wksh.js');
     wp_enqueue_script('wksh_script');
     wp_register_style( 'wksh-style', plugins_url('wksh.css', __FILE__) );
     wp_enqueue_style( 'wksh-style' );
}
add_action('wp_enqueue_scripts','wksh_scripts');

//-----------------------------------------------------------------------------------
//SHORTCODES

global $LIT;
$LIT= array(
    'GADERY POLUKI',
    'POLITYKA RENU',
    'KONIEC MATURY',
    'KACEMINUTOWY',
    'NOWE BUTY LISA',
    'MOTYLE CUDAKI',
    'GUBI KALESONY',
    'KALINOWE BUTY'
    );

function wksh_shortcode($atts, $content = null )
{
    global $LIT;
    extract( shortcode_atts( array(
		'p' => 'Insekt1'
	), $atts) );
    $str='<div class="szyfrator">
    <h5>Szyfrator WKSH</h5>
    Wpisz poniżej swój tekst, wybierz szyfr i naciśnij Szyfruj! Po chwili otrzymasz swój zaszyfrowany tekst.
    <form>
    <script type="text/javascript">
        var ajaxUrl = "'.site_url().'/wp-admin/admin-ajax.php";
        var czekajIco = "'.site_url().'/wp-content/plugins/wksh/img/2m.gif";
    </script>
    <textarea id="tekst">'.$content.'</textarea>
    <select id="szyfr">';
    foreach($LIT as $l)
    {
        $la = strtolower(str_replace(" ","",$l));
        $str.='<option value="'.$la.'">'.$l.'</option>';
    }

    $str.='<option value="czekoladka">Czekoladka</option>
    </select>
    </form>
    <input type="button" id="szyfruj" value="Szyfruj!" />
    <div id="wynik"></div>
    </div>';
    return $str;
}

add_shortcode('wksh', 'wksh_shortcode');

//-----------------------------------------------------------------------------------
//AJAX

add_action('wp_ajax_szyfruj', 'ajax_szyfruj');
add_action('wp_ajax_nopriv_szyfruj', 'ajax_szyfruj');

function ajax_szyfruj()
{
    if(isset($_POST['t']))
    {
        $t = $_POST['t'];
        $metoda = $_POST['metoda'];
    }
    elseif(isset($_GET['t']))
    {
        $t = $_GET['t'];
        $metoda = $_GET['metoda'];
    }

    if(!isset($t))
        exit();

    $str = "";

    switch($metoda){
        case "gaderypoluki":
        case "politykarenu":
        case "kaceminutowy":
        case "koniecmatury":
        case "nowebutylisa":
        case "motylecudaki":
        case "kalinowebuty":
        {
            $str = strtoupper(gadery($t, $metoda));
            break;
        }
        case "czekoladka":
        {
            $str = czekoladka($t);
            break;
        }
    }

    echo $str;

    exit();
}



//-----------------------------------------------------------------------------------
//FUNCTIONS
function przygotuj($t)
{
    $t = strtolower($t);
    $t = str_replace('ą', 'a' , $t);
    $t = str_replace('ć', 'c' , $t);
    $t = str_replace('ę', 'e' , $t);
    $t = str_replace('ł', 'l' , $t);
    $t = str_replace('ń', 'n' , $t);
    $t = str_replace('ó', 'o' , $t);
    $t = str_replace('ś', 's' , $t);
    $t = str_replace('ź', 'z' , $t);
    $t = str_replace('ż', 'z' , $t);


    return $t;
}

/*  Przygotowanie kluczy (tablic asocjacyjnych) na podstawie tekstu.
    Tekst musi być w postaci ciągłej - bez znaków, zpacji, myślników...
*/
function gklucz($str)
{
    $str=strtolower($str);
    $litery = str_split($str);
    $k = array();
    for($i=0;$i<count($litery);$i=$i+2)
    {
        $k[$litery[$i]] = $litery[$i+1];
        $k[$litery[$i+1]] = $litery[$i];
    }
    return $k;
}

/*  Szyfrowanie na podstawie klucza
*/
function gadery($t, $met)
{
    $k = gklucz($met);

    if(isset($k))
    {
        $t = przygotuj($t);
        $s = "";
        for($i=0; $i < strlen($t); $i++)
        {
            $l = $t[$i];
            $s = $s.($k[$l] ? $k[$l] : $t[$i]);
        }
    }
    return $s;
}

function czekoladka($t)
{
    $t = str_split(przygotuj($t));
    $s = "";
    $litery = str_split("abcdefghijklmnoprstuwyz");
    foreach($t as $tel)
    {
        if(in_array($tel, $litery))
        {
            $s .= '<div class="czeko czekolitera czeko-'.$tel.'">&nbsp;</div>';
        }
        elseif($tel == " ")
        {
            $s .= '<div class="czeko czekolitera czeko-spacja">&nbsp;</div>';
        }
    }
    return $s;
}

?>
