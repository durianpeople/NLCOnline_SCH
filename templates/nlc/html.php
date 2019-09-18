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
    </style>
</head>

<body class="light">
    <!-- Pre loader -->
    <div id="loader" class="loader">
        <div class="plane-container">
            <div class="preloader-wrapper small active">
                <div class="spinner-layer spinner-blue">
                    <div class="circle-clipper left">
                        <div class="circle"></div>
                    </div>
                    <div class="gap-patch">
                        <div class="circle"></div>
                    </div>
                    <div class="circle-clipper right">
                        <div class="circle"></div>
                    </div>
                </div>

                <div class="spinner-layer spinner-red">
                    <div class="circle-clipper left">
                        <div class="circle"></div>
                    </div>
                    <div class="gap-patch">
                        <div class="circle"></div>
                    </div>
                    <div class="circle-clipper right">
                        <div class="circle"></div>
                    </div>
                </div>

                <div class="spinner-layer spinner-yellow">
                    <div class="circle-clipper left">
                        <div class="circle"></div>
                    </div>
                    <div class="gap-patch">
                        <div class="circle"></div>
                    </div>
                    <div class="circle-clipper right">
                        <div class="circle"></div>
                    </div>
                </div>

                <div class="spinner-layer spinner-green">
                    <div class="circle-clipper left">
                        <div class="circle"></div>
                    </div>
                    <div class="gap-patch">
                        <div class="circle"></div>
                    </div>
                    <div class="circle-clipper right">
                        <div class="circle"></div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!--#app -->
    <div id="app">
        <!-- SideBar -->
        <aside class="main-sidebar fixed offcanvas shadow" data-toggle="offcanvas">
            <section class="sidebar">
                <div class="w-80px mt-3 mb-3 ml-3">
                    <!-- <img src="img/basic/logo.png" alt="" /> -->
                    <h1>LOGO</h1>
                </div>
                <div class="relative">
                    <a data-toggle="collapse" href="#userSettingsCollapse" role="button" aria-expanded="false" aria-controls="userSettingsCollapse" class="btn-fab btn-fab-sm absolute fab-right-bottom fab-top btn-primary shadow1 ">
                        <i class="icon icon-cogs"></i>
                    </a>
                    <div class="user-panel p-3 light mb-2">
                        <div>
                            <div class="float-left info">
                                <h6 class="font-weight-light mt-2 mb-1"><?php h(PuzzleUser::active()->fullname)?></h6>
                            </div>
                        </div>
                        <div class="clearfix"></div>
                        <div class="collapse multi-collapse" id="userSettingsCollapse">
                            <div class="list-group mt-3 shadow">
                                <a href="/users/profile#changepass" class="list-group-item list-group-item-action"><i class="mr-2 icon-security text-purple"></i>Change
                                    Password</a>
                                <a href="/users/logout" class="list-group-item list-group-item-action ">
                                    <i class="mr-2 icon-exit_to_app text-danger"></i>Logout
                                </a>
                            </div>
                        </div>
                    </div>
                </div>
                <ul class="sidebar-menu">
                    <li class="header"><strong>MENU</strong></li>
                    <li class="treeview">
                        <?php $tmpl->navigation->loadView("nlc_menu")?>
                    </li>
                </ul>
            </section>
        </aside>
        <!--Sidebar End-->

        <!-- Header -->
        <div class="has-sidebar-left">
            <div class="sticky">
                <div class="navbar navbar-expand navbar-dark d-flex justify-content-between bd-navbar blue accent-3">
                    <div class="relative">
                        <a href="#" data-toggle="push-menu" class="paper-nav-toggle pp-nav-toggle">
                            <i></i>
                        </a>
                    </div>
                </div>
            </div>
        </div>
        <!-- Header End-->

        <div class="page has-sidebar-left">
            <div class="container" style="padding:25px 15px;">
            <?php if ($tmpl->http_code == 200) $tmpl->app->loadMainView() ?>            
            </div>
        </div>
    </div>
    <?php echo $tmpl->postBody; ?>
    <?php Prompt::printPrompt() ?>
    <script src="<?php echo $tmpl->url ?>/js/app.js"></script>
</body>

</html>