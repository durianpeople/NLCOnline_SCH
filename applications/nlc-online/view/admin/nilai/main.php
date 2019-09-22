<link href="https://unpkg.com/tabulator-tables@4.3.0/dist/css/semantic-ui/tabulator_semantic-ui.min.css" rel="stylesheet">
<script type="text/javascript" src="https://unpkg.com/tabulator-tables@4.3.0/dist/js/tabulator.min.js"></script>
<div style="padding: 50px;background: white; margin: auto; border-radius: 25px;line-height: 35px;">
    <h1>Hasil Warm-up</h1>
    <div id="score-tab"></div>
</div>

<?php ob_start() ?>
<script>
    (function() {
        let score = <?php j($score) ?>;
        var user_table = new Tabulator(document.getElementById("score-tab"), {
            paginationSize: 20,
            pagination: "local",
            resizableRows: false,
            resizableColumns: false,
            columns: [{
                    title: "Nama Sesi",
                    field: "nama_sesi",
                    headerFilter: "input"
                },
                {
                    title: "ID NLC",
                    field: "nlc_id",
                    headerFilter: "input"
                },
                {
                    title: "Jumlah soal benar",
                    field: "benar",
                },
                {
                    title: "Jumlah soal salah",
                    field: "salah",
                },
                {
                    title: "Score",
                    field: "score",
                },
            ]
        });
        user_table.setData(score);
    })();
</script>
<?php echo Minifier::outJSMin() ?>