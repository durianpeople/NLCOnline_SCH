<!DOCTYPE html>
<html>

<head>
    <meta charset="utf-8" />
    <?php $tmpl->dumpHeaders(); ?>
    <title><?php if ($tmpl->title != "") echo $tmpl->title . " - "; ?><?php echo __SITENAME; ?></title>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="<?php echo $tmpl->url ?>/css/app2.css" />
    <link rel="icon" href="<?php echo $tmpl->url?>/img/schematics.png" />
    <style>
        #loginCtn .helpform {
            display: none;
        }
    </style>

</head>

<body class="light">
    <div id="app">
        <main>
            <div id="primary" class="p-t-b-100 height-full">
                <img src="<?php h($tmpl->url)?>/img/object1.png" style="position: absolute;top: 100px; left: 50px;"/>
                <img src="<?php h($tmpl->url)?>/img/object1.png" style="position: absolute;bottom: 100px; right: 50px;"/>
                <div class="container">
                    <div class="row">
                        <div class="col-lg-4 mx-md-auto paper-card">
                            <div class="text-center" style="margin-bottom:15px;">
                                <img src="<?php h($tmpl->url)?>/img/LogoGram2-min.png" style="width:150px;height:150px;object-fit:contain;">
                            </div>
                            <div>
                                <?php if ($tmpl->http_code == 200) $tmpl->app->loadMainView() ?>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </main>
    </div>
    <?php echo $tmpl->postBody; ?>
    <?php Prompt::printPrompt() ?>
</body>

</html>