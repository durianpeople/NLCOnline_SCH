<!DOCTYPE html>
<html>

<head>
    <meta charset="utf-8" />
    <?php $tmpl->dumpHeaders(); ?>
    <title>Yeay! Found our 404 page!</title>
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
        <main class="parallel">
            <div class="row grid">
                <div class="col-md-6 white">
                    <div class="p-5">
                        <div class="p-5">
                            <div class="text-center p-t-100">
                                <p class="s-128 bolder p-t-b-100">Opps!</p>
                                <p class="s-18">oh dear! you are lost don't try to hard.</p>
                                <div class="p-t-b-20">
                                    <a href="/" class="btn  btn-outline-primary btn-lg">
                                        <i class="icon icon-arrow_back"></i> Go Back To Home
                                    </a>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="col-md-6 height-full" data-bg-repeat="false" data-bg-possition="center" style="background: url('<?php echo $tmpl->url?>/img/dummy/cs3.gif') #FFEFE4"></div>
            </div>
        </main>
    </div>
    <!--/#app -->
    <script src="<?php echo $tmpl->url?>/js/app.js"></script>
</body>

</html>