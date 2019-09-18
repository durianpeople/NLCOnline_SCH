<!DOCTYPE html>
<html>

<head>
    <meta charset="utf-8" />
    <?php $tmpl->dumpHeaders(); ?>
    <title><?php if ($tmpl->title != "") echo $tmpl->title . " - "; ?><?php echo __SITENAME; ?></title>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="<?php echo $tmpl->url ?>/css/app.css" />
    <style>
        .loader {
            position: fixed;
            left: 0;
            top: 0;
            width: 100%;
            height: 100%;
            background-color: #f5f8fa;
            z-index: 9998;
            text-align: center;
        }

        .plane-container {
            position: absolute;
            top: 50%;
            left: 50%;
        }

        #login_bar .btn-primary{
            color:#000!important;
        }
    </style>
</head>

<body class="light">
    <div id="login_bar" style="float:right;position: fixed;top: 0;right: 20px;z-index: 999;color:#000!important">
        <?php $tmpl->navigation->loadView("login_bar"); ?>
    </div>
    <div id="app">
        <aside class="main-sidebar fixed offcanvas shadow" data-toggle="offcanvas">
            <section class="sidebar">
                <div style="padding:15px">
                    <img src="<?php echo $tmpl->url ?>/img/schematics-min.png" style="height:120px;object-fit: contain;">
                </div>
                <ul class="sidebar-menu">
                    <li class="header"><strong>MENU</strong></li>
                    <li class="treeview">
                        <?php $tmpl->navigation->loadView("nlc_menu") ?>
                    </li>
                </ul>
            </section>
        </aside>
        <div class="has-sidebar-left">
            <div class="sticky">
                <div class="navbar navbar-expand navbar-dark d-flex justify-content-between bd-navbar blue accent-3">
                    <div class="relative">

                    </div>
                </div>
            </div>
        </div>
        <div class="page has-sidebar-left">
            <div class="container" style="padding:35px 15px;">
                <?php if ($tmpl->http_code == 200) $tmpl->app->loadMainView() ?>
            </div>
        </div>
    </div>
    <?php echo $tmpl->postBody; ?>
    <?php Prompt::printPrompt() ?>
</body>

</html>