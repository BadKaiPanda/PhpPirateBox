<?php 
/******
PhpPiratebox
Copyright 2013 Sergio Monedero
Released under GPLv3, see http://www.gnu.org/licenses/gpl-3.0.html
******/

require("config.php"); 
?>
<!DOCTYPE html>
<html>
    <head>
        <meta http-equiv="Content-Type" content="text/html; charset=UTF-8">
        <title>PhpPiratebox</title>

        <link rel="stylesheet" href="css/bootstrap/bootstrap.css" />
        <link rel="stylesheet" href="css/alertify/alertify.core.css" />
        <link rel="stylesheet" href="css/alertify/alertify.theme.css" />
        <link rel="stylesheet" href="css/phppiratebox.css" />

        <script type="text/javascript" src="js/jquery/jquery.js"></script>
        <script type="text/javascript" src="js/jquery/jquery.ui.widget.js"></script>
        <script type="text/javascript" src="js/jquery/jquery.form.js"></script>
        <script type="text/javascript" src="js/jquery/jquery.iframe-transport.js"></script>
        <script type="text/javascript" src="js/jquery/jquery.fileupload.js"></script>
        <script type="text/javascript" src="js/alertify/alertify.js"></script>
        <script type="text/javascript" src="js/bootstrap/bootstrap.js"></script>
        <script type="text/javascript" src="js/phppiratebox.js"></script>
    </head>
    <body>
        <header>
            <div class="container navbar row-fluid span12">
                <img src="img/piratebox-logo.png" />
                <h1>PHP Pirate Box</h1>
                <nav>
                    <ul id="navbar" class="nav navbar-nav">
                        <li class="active"><a onclick="javascript:showSection($('#files-section'), this);">Files</a></li>
                        <li><a onclick="javascript:showSection($('#wall-section'), this);">Wall</a></li>
                        <li><a onclick="javascript:showSection($('#about-section'), this);">About</a></li>
                    </ul>
                </nav>
            </div>
        </header>
        <div id="frame" class="container row-fluid span12">
            <div id="files-section" class="section panel">
                <div class="panel-heading">
                    <h2 class="panel-title">Files</h2>
                </div>

                <div class="buttons">
                    <button type="button" id="file-upload-btn" class="btn btn-default" onclick="javascript:showUploadFileDialog();">Upload a file</button>
                    <button type="button" id="file-upload-btn" class="btn btn-default" onclick="javascript:getFileList();">Update filelist</button>
                </div>
                <div id="file-list-section" class="span12"></div>
            </div>

            <div id="wall-section" class="section panel">
                <div class="panel-heading">
                    <h2 class="panel-title">Wall</h2>
                </div>

                <div class="buttons">
                    <button type="button" id="file-upload-btn" class="btn btn-default" onclick="javascript:showPostMessageDialog();">Post a message</button>
                    <button type="button" id="file-upload-btn" class="btn btn-default" onclick="javascript:getMessageList();">Update messagelist</button>
                </div>

                <div id="message-list-section" class="span12"></div>
            </div>

            <div id="about-section" class="section panel">
                <div class="panel-heading">
                    <h2 class="panel-title">About PhpPiratebox</h2>
                </div>

                <article class="span12">
                    <p>Inspired by pirate radio and the free culture movement, PirateBox is a self-contained mobile collaboration and file sharing device. PirateBox utilizes Free, Libre and Open Source software (FLOSS) to create mobile wireless file sharing networks where users can anonymously share images, video, audio, documents, and other digital content.</p>
                    <p>PirateBox is designed to be safe and secure. No logins are required and no user data is logged. The system is purposely not connected to the Internet in order to prevent tracking and preserve user privacy. More information at http://daviddarts.com/piratebox/?title=PirateBox.</p>
                    <p>PhpPiratebox project provides a Piratebox written in PHP and built with these open-source technologies:
                    <ul>
                        <li>Php (http://php.net).</li>
                        <li>Jquery (http://jquery.org/) with Jquery forms (http://malsup.com/jquery/form/) and Jquery fileupload (http://blueimp.github.io/jQuery-File-Upload/) plugins.</li>
                        <li>Twitter Bootstrap (http://getbootstrap.com).</li>
                        <li>Alertify.cs (http://fabien-d.github.io/alertify.js/).</li>
                    </ul>
					</p>
					<p>PhpPiratebox was created by Sergio Monedero and is released under GPLv3, see http://www.gnu.org/licenses/gpl-3.0.html. You can download, modify, and host your own PhpPiratebox by downloading the software for free at https://github.com/Branyac/PhpPirateBox.</p>
                    <p>Please note that names and trademarks in this page are only for informational issues, they're not responsible and have no control over the contents of this piratebox. For inquiries about files or wall messages contact with the owner of this PhpPiratebox.</p>
                </article>
            </div>
        </div>
    </body>

    <script type="text/javascript">
                            initialize();
    </script>
</html>
