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

        #loginCtn .helpform {
            display: none;
        }
    </style>

</head>

<body class="light">
    <div id="app">
        <main>
            <div id="primary" class="p-t-b-100 height-full">
                <div class="container">
                    <div class="row">
                        <div class="col-lg-4 mx-md-auto paper-card">
                            <div class="text-center">
                                NLC Online 2019
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