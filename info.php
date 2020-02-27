<?php
// comment line bellow to show this page, uncomment to hide it
//exit;

use \phpbb\db\tools\tools;

ini_set('display_errors', '1');

$page_title = $_SERVER['HTTP_HOST'].' Server Info ['.date('Y-m-d His').']';

if (!defined('PHP_VERSION_ID')) {
    $server_info['PHP Info'][''][] = [
        'key' => 'Version too low',
        'value' => 'Sorry, dude! Your PHP is ridiculously outdated, like lower than 5.2.7!',
    ];

} else {
    $script_root_path = $_SERVER['DOCUMENT_ROOT'];
    $sql_variables_query = 'SHOW VARIABLES LIKE "%version%";';
    $bootstrap_files = [
        'PrestaShop' => $script_root_path.'/config/config.inc.php',
        'Wordpress'  => $script_root_path.'/wp-load.php',
        'phpBB'      => $script_root_path.'/common.php',
        'PHP'        => pathinfo($_SERVER['DOCUMENT_ROOT'], PATHINFO_DIRNAME).'/backup/.db.php',
    ];

    $script = $bootstrap_file = false;
    foreach ($bootstrap_files as $k => $v) {
        if (is_file($v) && is_readable($v)) {
            $script = $k;
            $bootstrap_file = $v;
            break;
        }
    }
    unset($bootstrap_files, $k, $v);

    $dbinfo = [];
    switch ($script) {
        case 'PrestaShop':
            include_once $bootstrap_file;
            $dbinfo = DB::getInstance()->executeS($query);
            break;
        case 'Wordpress':
            define('SHORTINIT', true);
            include_once $bootstrap_file;
            $wpdb = new wpdb(DB_USER, DB_PASSWORD, DB_NAME, DB_HOST);
            // https://codex.wordpress.org/Class_Reference/wpdb#SELECT_Generic_Results
            $dbinfo = $wpdb->get_results($query, ARRAY_A);
            break;
        case 'phpBB':
            define('IN_PHPBB', true);
            $phpbb_root_path = $script_root_path.'/';
            $phpEx = 'php';
            include_once $bootstrap_file;
            $result = $db->sql_query($sql_variables_query);
            $dbinfo = $db->sql_fetchrowset($result);
            $db->sql_freeresult($result);
            break;
        case 'PHP':
            include_once $bootstrap_file;
            if (extension_loaded('pdo_mysql')) {
                try {
                    $pdo = new \PDO("mysql:host=$server;dbname=$db", $user, $pass);
                    $stm = $pdo->query($query);
                    $stm->setFetchMode(PDO::FETCH_ASSOC);
                    $dbinfo = $stm->fetchAll();
                } catch (Exception $e) {
                    echo '<div class="container"><h4>PDO Error:</h4><pre><code>'.$e->getMessage().'</code></pre></div>';
                }
            } elseif (extension_loaded('mysqli')) {
                try {
                    $dbh = new mysqli($server, $user, $pass, $db);
                    $dbinfo = $dbh->query($query);
                    $rows->free();
                    $dbh->close();
                } catch (Exception $e) {
                    echo '<div class="container"><h4>Mysqli Error:</h4><pre><code>'.$e->getMessage().'</code></pre></div>';
                }
            }
            break;
    }

    $server_info = [];
    if ($dbinfo) {
        foreach ($dbinfo as $row) {
            $server_info['Database Server Info'][''][] = [
                'key' => $row['Variable_name'],
                'value' => $row['Value'],
            ];
        }
    }
    unset($dbinfo);

    $css_nonce = version_compare(PHP_VERSION, '7.0.0', '>=')
        ? bin2hex(random_bytes(8))
        : function_exists('openssl_random_pseudo_bytes')
            ? bin2hex(openssl_random_pseudo_bytes(8))
            : '8624038bae6e0b3e';
}

?><!doctype html>

<html lang="ro">

<head>
  <meta charset="utf-8">

  <title><?=$page_title?></title>
  <meta name="description" content="">
  <meta name="author" content="">
  <meta http-equiv="Content-Security-Policy" content="style-src 'nonce-<?=$css_nonce?>'">
  <style nonce="<?=$css_nonce?>">
    /*!
    * Milligram v1.3.0
    * https://milligram.github.io
    *
    * Copyright (c) 2017 CJ Patoilo
    * Licensed under the MIT license
    */
    *,*:after,*:before{box-sizing:inherit}html{box-sizing:border-box;font-size:62.5%}body{color:#606c76;font-family:'Roboto', 'Helvetica Neue', 'Helvetica', 'Arial', sans-serif;font-size:1.6em;font-weight:300;letter-spacing:.01em;line-height:1.6}blockquote{border-left:0.3rem solid #d1d1d1;margin-left:0;margin-right:0;padding:1rem 1.5rem}blockquote *:last-child{margin-bottom:0}.button,button,input[type='button'],input[type='reset'],input[type='submit']{background-color:#9b4dca;border:0.1rem solid #9b4dca;border-radius:.4rem;color:#fff;cursor:pointer;display:inline-block;font-size:1.1rem;font-weight:700;height:3.8rem;letter-spacing:.1rem;line-height:3.8rem;padding:0 3.0rem;text-align:center;text-decoration:none;text-transform:uppercase;white-space:nowrap}.button:focus,.button:hover,button:focus,button:hover,input[type='button']:focus,input[type='button']:hover,input[type='reset']:focus,input[type='reset']:hover,input[type='submit']:focus,input[type='submit']:hover{background-color:#606c76;border-color:#606c76;color:#fff;outline:0}.button[disabled],button[disabled],input[type='button'][disabled],input[type='reset'][disabled],input[type='submit'][disabled]{cursor:default;opacity:.5}.button[disabled]:focus,.button[disabled]:hover,button[disabled]:focus,button[disabled]:hover,input[type='button'][disabled]:focus,input[type='button'][disabled]:hover,input[type='reset'][disabled]:focus,input[type='reset'][disabled]:hover,input[type='submit'][disabled]:focus,input[type='submit'][disabled]:hover{background-color:#9b4dca;border-color:#9b4dca}.button.button-outline,button.button-outline,input[type='button'].button-outline,input[type='reset'].button-outline,input[type='submit'].button-outline{background-color:transparent;color:#9b4dca}.button.button-outline:focus,.button.button-outline:hover,button.button-outline:focus,button.button-outline:hover,input[type='button'].button-outline:focus,input[type='button'].button-outline:hover,input[type='reset'].button-outline:focus,input[type='reset'].button-outline:hover,input[type='submit'].button-outline:focus,input[type='submit'].button-outline:hover{background-color:transparent;border-color:#606c76;color:#606c76}.button.button-outline[disabled]:focus,.button.button-outline[disabled]:hover,button.button-outline[disabled]:focus,button.button-outline[disabled]:hover,input[type='button'].button-outline[disabled]:focus,input[type='button'].button-outline[disabled]:hover,input[type='reset'].button-outline[disabled]:focus,input[type='reset'].button-outline[disabled]:hover,input[type='submit'].button-outline[disabled]:focus,input[type='submit'].button-outline[disabled]:hover{border-color:inherit;color:#9b4dca}.button.button-clear,button.button-clear,input[type='button'].button-clear,input[type='reset'].button-clear,input[type='submit'].button-clear{background-color:transparent;border-color:transparent;color:#9b4dca}.button.button-clear:focus,.button.button-clear:hover,button.button-clear:focus,button.button-clear:hover,input[type='button'].button-clear:focus,input[type='button'].button-clear:hover,input[type='reset'].button-clear:focus,input[type='reset'].button-clear:hover,input[type='submit'].button-clear:focus,input[type='submit'].button-clear:hover{background-color:transparent;border-color:transparent;color:#606c76}.button.button-clear[disabled]:focus,.button.button-clear[disabled]:hover,button.button-clear[disabled]:focus,button.button-clear[disabled]:hover,input[type='button'].button-clear[disabled]:focus,input[type='button'].button-clear[disabled]:hover,input[type='reset'].button-clear[disabled]:focus,input[type='reset'].button-clear[disabled]:hover,input[type='submit'].button-clear[disabled]:focus,input[type='submit'].button-clear[disabled]:hover{color:#9b4dca}code{background:#f4f5f6;border-radius:.4rem;font-size:86%;margin:0 .2rem;padding:.2rem .5rem;white-space:nowrap}pre{background:#f4f5f6;border-left:0.3rem solid #9b4dca;overflow-y:hidden}pre>code{border-radius:0;display:block;padding:1rem 1.5rem;white-space:pre}hr{border:0;border-top:0.1rem solid #f4f5f6;margin:3.0rem 0}input[type='email'],input[type='number'],input[type='password'],input[type='search'],input[type='tel'],input[type='text'],input[type='url'],textarea,select{-webkit-appearance:none;-moz-appearance:none;appearance:none;background-color:transparent;border:0.1rem solid #d1d1d1;border-radius:.4rem;box-shadow:none;box-sizing:inherit;height:3.8rem;padding:.6rem 1.0rem;width:100%}input[type='email']:focus,input[type='number']:focus,input[type='password']:focus,input[type='search']:focus,input[type='tel']:focus,input[type='text']:focus,input[type='url']:focus,textarea:focus,select:focus{border-color:#9b4dca;outline:0}select{background:url('data:image/svg+xml;utf8,<svg xmlns="http://www.w3.org/2000/svg" height="14" viewBox="0 0 29 14" width="29"><path fill="#d1d1d1" d="M9.37727 3.625l5.08154 6.93523L19.54036 3.625"/></svg>') center right no-repeat;padding-right:3.0rem}select:focus{background-image:url('data:image/svg+xml;utf8,<svg xmlns="http://www.w3.org/2000/svg" height="14" viewBox="0 0 29 14" width="29"><path fill="#9b4dca" d="M9.37727 3.625l5.08154 6.93523L19.54036 3.625"/></svg>')}textarea{min-height:6.5rem}label,legend{display:block;font-size:1.6rem;font-weight:700;margin-bottom:.5rem}fieldset{border-width:0;padding:0}input[type='checkbox'],input[type='radio']{display:inline}.label-inline{display:inline-block;font-weight:normal;margin-left:.5rem}.container{margin:0 auto;max-width:112.0rem;padding:0 2.0rem;position:relative;width:100%}.row{display:flex;flex-direction:column;padding:0;width:100%}.row.row-no-padding{padding:0}.row.row-no-padding>.column{padding:0}.row.row-wrap{flex-wrap:wrap}.row.row-top{align-items:flex-start}.row.row-bottom{align-items:flex-end}.row.row-center{align-items:center}.row.row-stretch{align-items:stretch}.row.row-baseline{align-items:baseline}.row .column{display:block;flex:1 1 auto;margin-left:0;max-width:100%;width:100%}.row .column.column-offset-10{margin-left:10%}.row .column.column-offset-20{margin-left:20%}.row .column.column-offset-25{margin-left:25%}.row .column.column-offset-33,.row .column.column-offset-34{margin-left:33.3333%}.row .column.column-offset-50{margin-left:50%}.row .column.column-offset-66,.row .column.column-offset-67{margin-left:66.6666%}.row .column.column-offset-75{margin-left:75%}.row .column.column-offset-80{margin-left:80%}.row .column.column-offset-90{margin-left:90%}.row .column.column-10{flex:0 0 10%;max-width:10%}.row .column.column-20{flex:0 0 20%;max-width:20%}.row .column.column-25{flex:0 0 25%;max-width:25%}.row .column.column-33,.row .column.column-34{flex:0 0 33.3333%;max-width:33.3333%}.row .column.column-40{flex:0 0 40%;max-width:40%}.row .column.column-50{flex:0 0 50%;max-width:50%}.row .column.column-60{flex:0 0 60%;max-width:60%}.row .column.column-66,.row .column.column-67{flex:0 0 66.6666%;max-width:66.6666%}.row .column.column-75{flex:0 0 75%;max-width:75%}.row .column.column-80{flex:0 0 80%;max-width:80%}.row .column.column-90{flex:0 0 90%;max-width:90%}.row .column .column-top{align-self:flex-start}.row .column .column-bottom{align-self:flex-end}.row .column .column-center{-ms-grid-row-align:center;align-self:center}@media (min-width: 40rem){.row{flex-direction:row;margin-left:-1.0rem;width:calc(100% + 2.0rem)}.row .column{margin-bottom:inherit;padding:0 1.0rem}}a{color:#9b4dca;text-decoration:none}a:focus,a:hover{color:#606c76}dl,ol,ul{list-style:none;margin-top:0;padding-left:0}dl dl,dl ol,dl ul,ol dl,ol ol,ol ul,ul dl,ul ol,ul ul{font-size:90%;margin:1.5rem 0 1.5rem 3.0rem}ol{list-style:decimal inside}ul{list-style:circle inside}.button,button,dd,dt,li{margin-bottom:1.0rem}fieldset,input,select,textarea{margin-bottom:1.5rem}blockquote,dl,figure,form,ol,p,pre,table,ul{margin-bottom:2.5rem}table{border-spacing:0;width:100%}td,th{border-bottom:0.1rem solid #e1e1e1;padding:1.2rem 1.5rem;text-align:left}td:first-child,th:first-child{padding-left:0}td:last-child,th:last-child{padding-right:0}b,strong{font-weight:bold}p{margin-top:0}h1,h2,h3,h4,h5,h6{font-weight:300;letter-spacing:-.1rem;margin-bottom:2.0rem;margin-top:0}h1{font-size:4.6rem;line-height:1.2}h2{font-size:3.6rem;line-height:1.25}h3{font-size:2.8rem;line-height:1.3}h4{font-size:2.2rem;letter-spacing:-.08rem;line-height:1.35}h5{font-size:1.8rem;letter-spacing:-.05rem;line-height:1.5}h6{font-size:1.6rem;letter-spacing:0;line-height:1.4}img{max-width:100%}.clearfix:after{clear:both;content:' ';display:table}.float-left{float:left}.float-right{float:right}
    caption{background:#eee;font-size:1.6em;padding:.7em}
    td{max-width:32rem;overflow-wrap:break-word}
    td > a{float:right}
    .container:first-child{margin-top:2.5rem}
  </style>

</head>

<body>

    <?php foreach ($server_info as $info_category => $sections) { ?>

    <div class="container">

        <h1><?=$info_category?></h1>

        <?php foreach ($sections as $section => $params) { ?>

        <table>
            <caption><?=$section?></caption>

            <?php foreach ($params as $param) { ?>

            <tr>
                <td><?=$param['key']?></td>
                <td><?=$param['value']?></td>
            </tr>

            <?php } ?>

        </table>

        <?php } ?>

    </div>

    <?php } ?>

    <div class="container">

        <?=phpinfoHtml()?>

    </div>

</body>

</html><?php

// INFO_GENERAL         1  The configuration line, php.ini location, build date, Web Server, System and more.
// INFO_CREDITS         2  PHP Credits. See also phpcredits().
// INFO_CONFIGURATION   4  Current Local and Master values for PHP directives. See also ini_get().
// INFO_MODULES         8  Loaded modules and their respective settings. See also get_loaded_extensions().
// INFO_ENVIRONMENT    16  Environment Variable information that's also available in $_ENV.
// INFO_VARIABLES      32  Shows all predefined variables from EGPCS (Environment, GET, POST, Cookie, Server).
// INFO_LICENSE        64  PHP License information. See also the » license FAQ.
// INFO_ALL            -1  Shows all of the above.

/**
 * https://www.php.net/manual/en/function.phpinfo.php#117961
 * https://stackoverflow.com/questions/11254619/get-contents-of-body-without-doctype-html-head-and-body-tags
 * @return [type] [description]
 */
function phpinfoHtml()
{
    ob_start();
    phpinfo(INFO_ALL);
    $raw = new DOMDocument;
    $tmp = new DOMDocument;
    $raw->loadHTML(ob_get_clean());
    $body = $raw->getElementsByTagName('body')->item(0);
    foreach ($body->childNodes as $child){
        $tmp->appendChild($tmp->importNode($child, true));
    }

    return $tmp->saveHTML();
}
