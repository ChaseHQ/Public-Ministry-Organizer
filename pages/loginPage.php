<?php
    require_once ('./lib/Security.php');
    Security::SecurePage();
?>
<!DOCTYPE html>
<html>
    <head>
        <meta http-equiv="Content-Type" content="text/html; charset=UTF-8">
        <title>Public Ministry Organizer :: Please Log-In</title>
        <link rel="icon" href="favicon.ico" type="image/x-icon"> 
        <link rel="shortcut icon" href="favicon.ico" type="image/x-icon"> 
        <link rel="apple-touch-icon" href="/images/pmo.png"/>
        <link rel="stylesheet" href="pmo.css" />
        <script type="text/javascript" src="/lib/jquery-1.11.0.min.js"></script>
        <link rel="stylesheet" href="/lib/ff/fancyfields.css" />
        <link rel="stylesheet" href="/lib/jquery-ui-1.10.4.custom.min.css" />
        <script type="text/javascript" src="/lib/ff/fancyfields-1.2.min.js"></script>
        <script type="text/javascript" src="/lib/jquery-ui-1.10.4.custom.min.js"></script>
        <script type="text/javascript" src="/lib/ff/fancyfields.csb.min.js"></script>
        <script type="text/javascript" src="/lib/loginPage.js"></script>
    </head>
    <body>
        <div id="top_container">
            <div id="top_image"></div>
            <div id="right_image"></div>
        </div>
        <div id="main_page">
            <div id="login_box" class="ui-corner-all">
                Please Select Your Congregation <br />
                <div id="left_justify_login">
                    <select id="cong"> 
                        <option selected disabled value="0">Select Congregation</option>
                        <?php
                            $db = Database::getDB();
                            foreach ($db->getCongregations() as $cong) {
                                ?>
                        <option value="<?=$cong['id']?>"><?=$cong['congName']?></option>
                        <?php
                            }
                        ?>
                    </select><br />
                </div>
                <div id="hidden">
                        <br />Select Your Name<br />
                        <div id="left_justify_login">
                        <select id="publisher"></select><br />
                        </div>
                        <span id="loginError">The PIN # Is Incorrect, Please Try Again</span><br />
                        PIN # <input id="pubPassword" type="password" /></br></br>
                        <input type="button" value="Log In" id="loginBtn" onclick="loginPress()" />
                </div>
            </div>
            <div class="little_loader ui-corner-all" id="congLoader"></div>
        </div>
    </body>
</html>
