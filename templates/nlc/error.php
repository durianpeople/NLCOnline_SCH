<!DOCTYPE html>
<html>

<head>
    <meta charset="utf-8" />
    <?php $tmpl->dumpHeaders(); ?>
    <title>Yeay! Found our 404 page!</title>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="<?php echo $tmpl->url ?>/css/app2.css" />
</head>

<body class="light">
    <div id="app">
        <main class="parallel">
            <div class="row grid">
                <div class="col-md-6 white">
                    <div class="text-center p-t-100">
                        <p class="s-128 bolder p-t-b-100">Opps!</p>
                        <p class="s-18">oh dear! you are lost don't try to hard.</p>
                        <div class="p-t-b-20">
                            <a href="/" class="btn btn-outline-primary btn-lg">
                                <i class="icon icon-arrow_back"></i> Go Back To Home
                            </a>
                        </div>
                    </div>
                </div>
                <div class="col-md-6 height-full" data-bg-repeat="false" data-bg-possition="center" style="background: url('<?php echo $tmpl->url ?>/img/dummy/cs3.gif') #FFEFE4;object-fit: contain;background-repeat: no-repeat;"></div>
            </div>
        </main>
    </div>
</body>

</html>