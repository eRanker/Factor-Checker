<?php ?>
<!DOCTYPE html>
<html lang="en">
    <head>
        <meta charset="utf-8">
        <meta http-equiv="X-UA-Compatible" content="IE=edge">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <meta name="description" content="<?PHP echo $page_title . " - " . $project_name ?>">
        <title><?PHP echo $page_desc . " - " . $project_name ?></title>

        <!-- Theme CSS -->
        <link href="/bower_components/bootstrap/dist/css/bootstrap.min.css" rel="stylesheet">
        <link href="/bower_components/font-awesome/css/font-awesome.min.css" rel="stylesheet" type="text/css">

        <!-- Project Base CSS -->
        <link href='http://fonts.googleapis.com/css?family=Permanent+Marker' rel='stylesheet' type='text/css'>
        <link href='https://fonts.googleapis.com/css?family=Open+Sans:400,700,400italic&subset=latin,latin-ext' rel='stylesheet' type='text/css'>

        <!-- Report Page CSS -->
        <link href="/css/base.css" rel="stylesheet">
        <link href="/css/report.css" rel="stylesheet">
        <?php if (isset($_GET['pdf']) && !empty($_GET['pdf'])) { ?>
            <link href="/css/reportpdf.css" rel="stylesheet">
        <?php } ?>

        <!-- HTML5 Shim and Respond.js IE8 support of HTML5 elements and media queries -->
        <!--[if lt IE 9]>
            <script src="https://oss.maxcdn.com/libs/html5shiv/3.7.0/html5shiv.js"></script>
            <script src="https://oss.maxcdn.com/libs/respond.js/1.4.2/respond.min.js"></script>
        <![endif]-->
    </head>
    <body>

        <div id="wrapper">
            <div class="w1">
                <header id="header">
                    <div class="container">
                        <div class="row">
                            <strong class="logo">
                                <a href="index.php">
                                    <img src="img/logo-white.png" alt="eRanker">
                                </a>
                            </strong>

                        </div>
                    </div>
                </header>
                <section class="visual">
                    <div class="container">
                        <div class="row">
                            <div class="col-xs-12">
                                <div class="holder">
                                    <header class="head">


                                        <?PHP if (!empty($page_title)) { ?>                    
                                            <div class="row">
                                                <div class="col-lg-12">
                                                    <h1 class="page-header"><?PHP echo $page_title ?></h1>
                                                </div>
                                                <!-- /.col-lg-12 -->
                                            </div>
                                        <?PHP } else { ?>
                                            <br /> 
                                        <?PHP } ?>

                                    </header>
                                    <div class="block">
                                        Factor description
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </section>
            </div>

            <div id="page-wrapper">


