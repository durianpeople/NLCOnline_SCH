<?php

use NLC\Base\Questions;
?>

<link href="https://unpkg.com/tabulator-tables@4.3.0/dist/css/semantic-ui/tabulator_semantic-ui.min.css" rel="stylesheet">
<script type="text/javascript" src="https://unpkg.com/tabulator-tables@4.3.0/dist/js/tabulator.min.js"></script>
<script type="text/javascript" src="https://cdnjs.cloudflare.com/ajax/libs/moment.js/2.22.2/moment.min.js"></script>

<h3>Manage Paket Soal</h3>
<div>
    <div id="data"></div>
</div>
<br>
<div>
    <button class="btn btn-primary" data-toggle="modal" data-target="#addquestions">Tambah Soal</button>
</div>

<div class="modal" id="new-sesi">
    <div class="modal-dialog" role="document">
        <form method="POST" id="new-sesi-frm">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Tambah Sesi</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body">
                    <div class="form-group row">
                        <label for="name" class="col-sm-2 col-form-label">Nama Sesi</label>
                        <div class="col-sm-10">
                            <input type="text" class="form-control" name="name" placeholder="Nama Sesi">
                        </div>
                    </div>
                    <div class="form-group row">
                        <label for="name" class="col-sm-2 col-form-label">Waktu Mulai</label>
                        <div class="col-sm-10">
                            <input type="datetime-local" class="form-control" name="start_time">
                        </div>
                    </div>
                    <div class="form-group row">
                        <label for="name" class="col-sm-2 col-form-label">Waktu Selesai</label>
                        <div class="col-sm-10">
                            <input type="datetime-local" class="form-control" name="end_time">
                        </div>
                    </div>
                    <fieldset class="form-group">
                        <div class="row">
                            <legend class="col-form-label col-sm-2 pt-0">Sesi Publik</legend>
                            <div class="col-sm-10">
                                <div class="form-check">
                                    <input class="form-check-input" type="radio" name="is-public" id="gridRadios1" value="1" checked>
                                    <label class="form-check-label" for="gridRadios1">
                                        Ya
                                    </label>
                                </div>
                                <div class="form-check">
                                    <input class="form-check-input" type="radio" name="is-public" id="gridRadios2" value="0">
                                    <label class="form-check-label" for="gridRadios2">
                                        Tidak
                                    </label>
                                </div>
                            </div>
                        </div>
                    </fieldset>
                </div>
                <div class="modal-footer">
                    <button type="submit" class="btn btn-primary">Buat</button>
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">Batal</button>
                </div>
            </div>
        </form>
    </div>
</div>

<div class="modal fade" id="answerkey">
    <div class="modal-dialog" role="document">
        <form method="POST" id="new-sesi-frm">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Kunci Jawaban</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body">
                    <table class="table">
                        <thead>
                            <tr>
                                <th>Nomor</th>
                                <th>Jawaban</th>
                            </tr>
                        </thead>
                        <tbody></tbody>
                    </table>
                </div>
            </div>
        </form>
    </div>
</div>

<div class="modal fade" id="addquestions">
    <div class="modal-dialog" role="document">
        <form method="POST" id="new-questions-frm">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Tambah Paket Soal</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body">
                    <div class="form-group row">
                        <label for="name" class="col-sm-2 col-form-label">Nama Paket Soal</label>
                        <div class="col-sm-10">
                            <input type="text" class="form-control" name="name" placeholder="Nama Paket Soal">
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
        $("#new-questions-frm").submit(e=>{
            e.preventDefault();
            let f = $(e.target).serialize();
            f += `&act=new_modal&_token=<?php echo (session_id()) ?>`;
            $.post("/nlc/soal",f,d=>{
                showMessage("Sesi terimpan" , "success");
                location.reload();
            }).fail(e=>{
                showMessage("Gagal Membuat" , "danger");
            })
        });
        var table = new Tabulator(document.getElementById("data"), {
            ajaxURL: "/nlc/soal",
            ajaxConfig: "POST",
            resizableRows: false,
            resizableColumns: false,
            layoutColumnsOnNewData: true,
            ajaxParams: {
                act: "fetch",
                _token: <?php j(session_id()) ?>
            },
            columns: [{
                    title: "ID",
                    field: "id",
                    sorter: "number"
                },
                {
                    title: "Nama",
                    field: "name",
                    sorter: "string",
                    headerFilter: "input",
                    editor: "input",
                    mutator: (value, data, type, params, cell) => {
                        if (type == "edit") {
                            $.post("/nlc/soal", {
                                _token: <?php j(session_id()) ?>,
                                id: cell.getData().id,
                                act: "set_name",
                                name: value
                            }, d => {
                                cell.getData().name = value;
                                table.replaceData();
                                showMessage("Data updated", "success");
                            }).fail(e => {
                                table.replaceData();
                                showMessage(e.statusText, "danger");
                            });
                            return value;
                        } else
                            return value;
                    },
                    mutatorParams: null
                },
                {
                    title: "Upload Soal",
                    formatter: function(cell, formatterParams) {
                        if (cell.getData().hasPDF) {
                            return `<a href="/nlc/q?q=${cell.getData().id}"><button class="btn btn-sm btn-primary">DOWNLOAD SOAL</button></a>`;
                        } else {
                            let a = document.createElement("input");
                            a.type = "file";
                            a.accept = ".pdf";
                            a.onchange = function() {
                                if (confirm("LAST CHANCE!\nUpload this file?")) {
                                    var fd = new FormData();
                                    fd.append("act", "upload_soal");
                                    fd.append("_token", <?php j(session_id()) ?>);
                                    fd.append("id", cell.getData().id);
                                    var files = a.files[0];
                                    fd.append('file', files);

                                    $.ajax({
                                        url: '/nlc/soal',
                                        type: 'post',
                                        data: fd,
                                        contentType: false,
                                        processData: false,
                                        success: function(response) {
                                            cell.getData().hasPDF = response;
                                            table.replaceData();
                                        }
                                    }).fail(e => {
                                        table.replaceData();
                                        showMessage(e.statusText, "danger");
                                    });
                                }
                            }
                            return a;
                        }
                    }
                },
                {
                    title: "Upload Kunci (CSV)",
                    formatter: function(cell, formatterParams) {
                        let a = document.createElement("input");
                        a.accept = ".csv";
                        a.type = "file";
                        a.onchange = function() {
                            if (confirm("LAST CHANCE!\nUpload this file?")) {
                                var fd = new FormData();
                                fd.append("act", "upload_kunci");
                                fd.append("_token", <?php j(session_id()) ?>);
                                fd.append("id", cell.getData().id);
                                var files = a.files[0];
                                fd.append('file', files);

                                $.ajax({
                                    url: '/nlc/soal',
                                    type: 'post',
                                    data: fd,
                                    contentType: false,
                                    processData: false,
                                    success: function(response) {
                                        showMessage("Key uploaded", "success");
                                        cell.getData().answer_key = response;
                                        table.replaceData();
                                    }
                                }).fail(e => {
                                    table.replaceData();
                                    showMessage(e.statusText, "danger");
                                });
                            }
                        }
                        let b = $("<div></div>");
                        b.append(a);
                        b.append("<br>");
                        let c = document.createElement("a");
                        c.href = "#";
                        c.innerHTML = "Lihat Kunci";
                        c.onclick = function() {
                            let a = $("#answerkey").modal('show');
                            let b = a.find("tbody");
                            b.children().remove();
                            for (const k in cell.getData().answer_key) {
                                if (cell.getData().answer_key.hasOwnProperty(k)) {
                                    const e = cell.getData().answer_key[k];
                                    b.append(`<tr><td>${k}</td><td>${["A","B","C","D","E"][e]}</td></tr>`);
                                }
                            }
                        };
                        b.append(c);
                        return b[0];
                    }
                }
            ],
            paginationSize: 20,
            pagination: "local"
        });
    }())
</script>
<?php echo Minifier::outJSMin() ?>