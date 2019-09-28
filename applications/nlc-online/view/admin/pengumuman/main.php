<?php

use NLC\Base\Announcement;

$tinymce = new Application("tinymce");
$tinymce->run();

?>

<link href="https://unpkg.com/tabulator-tables@4.3.0/dist/css/semantic-ui/tabulator_semantic-ui.min.css" rel="stylesheet">
<script type="text/javascript" src="https://unpkg.com/tabulator-tables@4.3.0/dist/js/tabulator.min.js"></script>
<div style="padding: 50px;background: white; margin: auto; border-radius: 25px;line-height: 35px;">
    <h1>Pengumuman</h1>
    <div id="ann-tab"></div>
    <div>
        <button class="btn btn-primary" data-toggle="modal" data-target="#announce">Buat Pengumuman</button>
    </div>
</div>

<div class="modal fade" id="announce">
    <div class="modal-dialog modal-lg" role="document">
        <form method="POST" id="announce-frm">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Buat Pengumuman</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body">
                    <div class="form-group row">
                        <label for="title" class="col-sm-3 col-form-label">Judul</label>
                        <div class="col-sm-9">
                            <input type="text" class="form-control" name="title" placeholder="">
                        </div>
                    </div>
                    <div class="form-group row">
                        <label for="title" class="col-sm-3 col-form-label">Konten</label>
                        <div class="col-sm-9">
                            <textarea name="content" id="txt_content" class="tinymce"></textarea>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="submit" class="btn btn-primary">Buat</button>
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">Batal</button>
                </div>
            </div>
        </form>
    </div>
</div>


<?php ob_start() ?>
<script>
    (function() {
        let anns = <?php j(Announcement::list()) ?>;
        var user_table = new Tabulator(document.getElementById("ann-tab"), {
            paginationSize: 20,
            pagination: "local",
            resizableRows: false,
            resizableColumns: true,
            columns: [{
                    title: "ID",
                    field: "id",
                },
                {
                    title: "Judul",
                    field: "title",
                },
                {
                    title: "Konten",
                    formatter: function(cell, formatterParams) {
                        return cell.getData().content;
                    },
                },
            ],
        });
        user_table.setData(anns);

        $("#announce-frm").submit(e=>{
            e.preventDefault();
            $.post("/nlc/pengumuman",{
                _token: '<?php echo (session_id()) ?>',
                act: "announce",
                title: $(e.target).find("input[name=title]").val(),
                content: tinyMCE.get('txt_content').getContent(),
            },d=>{
                showMessage("Pengumuman berhasil dibuat" , "success");
                location.reload();
            }).fail(e=>{
                showMessage("Gagal Membuat" , "danger");
            })
        });
    })();
</script>
<?php echo Minifier::outJSMin() ?>