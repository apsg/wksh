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

function wksh_shortcode($atts, $content = null )
{
    extract( shortcode_atts( array(
		'p' => 'Insekt1'
	), $atts) );
    return '<div class="szyfrator">
    <form>
    <script type="text/javascript">
        var ajaxUrl = "'.site_url().'/wp-admin/admin-ajax.php";
        var czekajIco = "'.site_url().'/wp-content/plugins/wksh/img/2m.gif";
    </script>
    <textarea id="tekst">'.$content.'</textarea>
    <select id="szyfr">
        <option value="gadery">GA-DE-RY PO-LU-KI</option>
        <option value="polityka">PO-LI-TY-KA RE-NU</option>
    </select>
    </form>
    <input type="button" id="szyfruj" value="Szyfruj!" />
    <div id="wynik"></div>
    </div>';
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
        case "gadery":{
            $str = gadery($t, $metoda);
            break;
        }

        case "polityka":{
            $str = gadery($t, $metoda);
            break;
        }
    }

    echo strtoupper($str);


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


function gadery($t, $met)
{
    switch($met){
        case "gadery":{
             $k = array(
                "g" => "a",
                "d" => "e",
                "r" => "y",
                "p" => "o",
                "l" => "u",
                "k" => "i",
                "a" => "g",
                "e" => "d",
                "y" => "r",
                "o" => "p",
                "u" => "l",
                "i" => "k"
                );
            break;
        }

        case "polityka":{
             $k = array(
                "p" => "o",
                "l" => "i",
                "t" => "y",
                "k" => "a",
                "r" => "e",
                "n" => "u",
                "o" => "p",
                "i" => "l",
                "y" => "t",
                "a" => "k",
                "e" => "r",
                "u" => "n"
                );
            break;
        }
    }

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


?>
