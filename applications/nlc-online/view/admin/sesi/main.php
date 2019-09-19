<?php

use NLC\Base\Questions;
use NLC\Base\NLCUser;
use NLC\Sesi\SesiPrivate;
use NLC\Sesi\SesiSelfJoin;
use NLC\Sesi\SesiTerbuka;

?>

<link href="https://unpkg.com/tabulator-tables@4.3.0/dist/css/semantic-ui/tabulator_semantic-ui.min.css" rel="stylesheet">
<script type="text/javascript" src="https://unpkg.com/tabulator-tables@4.3.0/dist/js/tabulator.min.js"></script>
<script type="text/javascript" src="https://cdnjs.cloudflare.com/ajax/libs/moment.js/2.22.2/moment.min.js"></script>

<h3>Manage Sesi</h3>
<div>
    <div id="data"></div>
</div>
<br>
<div>
    <button class="btn btn-primary" data-toggle="modal" data-target="#new-sesi">Tambah Sesi</button>
</div>

<div class="modal fade" id="new-sesi">
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
                            <legend class="col-form-label col-sm-2 pt-0">Tipe Sesi</legend>
                            <div class="col-sm-10">
                                <div class="form-check">
                                    <input class="form-check-input" type="radio" name="type" id="gridRadios1" value="public" checked>
                                    <label class="form-check-label" for="gridRadios1">
                                        Publik
                                    </label>
                                </div>
                                <div class="form-check">
                                    <input class="form-check-input" type="radio" name="type" id="gridRadios2" value="private">
                                    <label class="form-check-label" for="gridRadios2">
                                        Whitelist
                                    </label>
                                </div>
                                <div class="form-check">
                                    <input class="form-check-input" type="radio" name="type" id="gridRadios3" value="selfjoin">
                                    <label class="form-check-label" for="gridRadios3">
                                        Publik dengan Kuota
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

<div class="modal fade" id="whitelist-modal">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Whitelist</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <div>
                    <div id="white-list-tab"></div>
                </div>
            </div>
            <div class="modal-footer">
                <button type="submit" id="setwhitelist-btn" class="btn btn-primary">Set Whitelist</button>
            </div>
        </div>
    </div>
</div>

<?php ob_start() ?>
<script>
    (function() {
        $("#new-sesi-frm").submit(e => {
            e.preventDefault();
            let f = $(e.target).serialize();
            f += `&act=new_modal&_token=<?php echo (session_id()) ?>`;
            $.post("/nlc/sesi", f, d => {
                showMessage("Sesi terimpan", "success");
                location.reload();
            }).fail(e => {
                showMessage("Gagal Membuat", "danger");
            })
        });
        let ulist = <?php j(NLCUser::getList()) ?>;
        let Slist = <?php j(Questions::list()) ?>;
        var dateEditor = function(cell, onRendered, success, cancel, editorParams) {
            //cell - the cell component for the editable cell
            //onRendered - function to call when the editor has been rendered
            //success - function to call to pass the successfuly updated value to Tabulator
            //cancel - function to call to abort the edit and return to a normal cell
            //editorParams - params object passed into the editorParams column definition property

            //create and style editor
            var editor = document.createElement("input");

            editor.setAttribute("type", "datetime-local");

            //create and style input
            editor.style.padding = "3px";
            editor.style.width = "100%";
            editor.style.boxSizing = "border-box";

            //Set value of editor to the current value of the cell
            editor.value = moment.unix(parseInt(cell.getValue())).zone("+0700").format("YYYY-MM-DDTHH:mm");

            //set focus on the select box when the editor is selected (timeout allows for editor to be added to DOM)
            onRendered(function() {
                editor.focus();
                editor.style.css = "100%";
            });

            //when the value has been set, trigger the cell to update
            function successFunc() {
                success(moment(editor.value).zone("+0700").seconds(0).format("X"));
            }

            // editor.addEventListener("change", successFunc);
            editor.addEventListener("blur", successFunc);

            //return the editor element
            return editor;
        };
        var user_table = new Tabulator(document.getElementById("white-list-tab"), {
            selectable: true,
            selectableRollingSelection: true,
            selectablePersistence: true,
            layoutColumnsOnNewData: true,
            paginationSize: 20,
            pagination: "local",
            resizableRows: false,
            resizableColumns: false,
            columns: [{
                    title: "ID NLC",
                    field: "nlc_id",
                    headerFilter: "input"
                },
                {
                    title: "Email",
                    field: "email",
                    headerFilter: "input"
                },
                {
                    title: "Nama Tim",
                    field: "fullname",
                    headerFilter: "input"
                }
            ]
        });
        user_table.setData(ulist);
        var table = new Tabulator(document.getElementById("data"), {
            ajaxURL: "/nlc/sesi",
            ajaxConfig: "POST",
            resizableRows: false,
            resizableColumns: false,
            layoutColumnsOnNewData: true,
            ajaxParams: {
                act: "fetch",
                _token: <?php j(session_id()) ?>
            },
            columns: [{
                    title: "Nama",
                    field: "name",
                    sorter: "string",
                    headerFilter: "input",
                    editor: "input",
                    mutator: (value, data, type, params, cell) => {
                        if (type == "edit") {
                            $.post("/nlc/sesi", {
                                _token: <?php j(session_id()) ?>,
                                id: cell.getData().id,
                                act: "set_name",
                                name: value
                            }, d => {
                                table.replaceData();
                                table.redraw();
                                showMessage("Data updated", "success");
                            }).fail(e => {
                                table.replaceData();
                                table.redraw();
                                showMessage(e.statusText, "danger");
                            });
                            return value;
                        } else
                            return value;
                    },
                    mutatorParams: null
                },
                {
                    title: "Tipe",
                    headerFilter: "input",
                    field: "type"
                },
                {
                    title: "Paket Soal",
                    field: "questions", //questions | null
                    formatter: function(cell, formatterParams) {
                        let s = document.createElement("select");
                        let o = document.createElement("option");
                        o.innerHTML = "- Tidak Ada Soal -";
                        o.value = "null";
                        s.add(o);
                        Slist.forEach(a => {
                            let o = document.createElement("option");
                            o.innerHTML = a.name;
                            o.value = a.id;
                            if (cell.getValue() != null && cell.getValue().id == o.value) o.selected = true;
                            s.add(o);
                        });
                        s.onchange = () => {
                            $.post("/nlc/sesi", {
                                _token: <?php j(session_id()) ?>,
                                id: cell.getData().id,
                                act: "set_soal",
                                q_id: s.value
                            }, d => {
                                cell.getData().questions = d;
                                table.replaceData();
                                table.redraw();
                                showMessage("Data updated", "success");
                            }).fail(e => {
                                table.replaceData();
                                table.redraw();
                                showMessage(e.statusText, "danger");
                            });
                        }
                        return s;
                    }
                },
                {
                    title: "Start Time",
                    field: "start_time",
                    sorter: "number",
                    editor: dateEditor,
                    formatter: function(cell, formatterParams) {
                        let a = new Date(parseInt(cell.getValue()) * 1000);
                        return moment(a).zone("+07:00").format("LLLL");
                    },
                    mutator: (value, data, type, params, cell) => {
                        if (type == "edit") {
                            $.post("/nlc/sesi", {
                                _token: <?php j(session_id()) ?>,
                                id: cell.getData().id,
                                act: "set_start_time",
                                start_time: value
                            }, d => {
                                cell.getData().start_time = d;
                                table.replaceData();
                                table.redraw();
                                showMessage("Data updated", "success");
                            }).fail(e => {
                                table.replaceData();
                                table.redraw();
                                showMessage(e.statusText, "danger");
                            });
                            return value;
                        } else
                            return value;
                    },
                },
                {
                    title: "End Time",
                    field: "end_time",
                    sorter: "number",
                    editor: dateEditor,
                    formatter: function(cell, formatterParams) {
                        return moment.unix(parseInt(cell.getValue())).format("LLLL");
                    },
                    mutator: (value, data, type, params, cell) => {
                        if (type == "edit") {
                            $.post("/nlc/sesi", {
                                _token: <?php j(session_id()) ?>,
                                id: cell.getData().id,
                                act: "set_end_time",
                                end_time: value
                            }, d => {
                                cell.getData().end_time = d;
                                table.replaceData();
                                table.redraw();
                                showMessage("Data updated", "success");
                            }).fail(e => {
                                table.replaceData();
                                table.redraw();
                                showMessage(e.statusText, "danger");
                            });
                            return value;
                        } else
                            return value;
                    },
                },
                {
                    title: "Enabled",
                    field: "enabled",
                    formatter: function(cell, formatterParams) {
                        return `<div style="text-align:center"><input type="checkbox" value="1" ${cell.getValue() ? "checked" : ""}></div>`;
                    },
                    cellClick: (e, cell) => {
                        let v = cell.getValue();
                        let f = () => {
                            $.post("/nlc/sesi", {
                                _token: <?php j(session_id()) ?>,
                                id: cell.getData().id,
                                act: "en_toggle"
                            }, d => {
                                cell.getData().enabled = d;
                                table.replaceData();
                                table.redraw();
                                showMessage("Data updated", "success");
                            }).fail(e => {
                                table.replaceData();
                                table.redraw();
                                showMessage(e.statusText, "danger");
                            });
                        };
                        if (v) {
                            if (confirm("DANGER!\nMatikan sesi ini? Semua data jawaban peserta akan dihapus!!")) {
                                if(confirm("HAPUS SEMUA JAWABAN PESERTA?")) f();
                            } else {
                                table.replaceData();
                                table.redraw();
                            }
                        } else f();
                    }
                },
                {
                    title: "Whitelist",
                    formatter: function(cell, formatterParams) {
                        switch (cell.getData().type) {
                            case <?php j(SesiTerbuka::class) ?>:
                                return "-";
                            case <?php j(SesiSelfJoin::class) ?>:
                            case <?php j(SesiPrivate::class) ?>:
                                let a = document.createElement("div");
                                a.style.display = "grid";
                                a.append($(cell.getValue() ? "<span>Sesi publik</span>" : `<span style="color:var(--danger)">NO</span>`)[0]);
                                if (!cell.getValue()) {
                                    let l = $("<a>Set Whitelist.</a>")[0];
                                    l.href = "#";
                                    l.onclick = () => {
                                        let d = [];
                                        let m = $("#whitelist-modal").modal('show');
                                        let b = $("#setwhitelist-btn")[0].onclick = function() {
                                            m.modal("hide");
                                            $.post("/nlc/sesi", {
                                                _token: <?php j(session_id()) ?>,
                                                id: cell.getData().id,
                                                act: "set_whitelist",
                                                d: JSON.stringify(d)
                                            }, d => {
                                                cell.getData().whitelisted = d;
                                                table.replaceData();
                                                table.redraw();
                                                showMessage("Whitelisted data updated", "success");
                                            }).fail(e => {
                                                table.replaceData();
                                                table.redraw();
                                                showMessage(e.statusText, "danger");
                                            });
                                        };
                                        m.one("shown.bs.modal", () => {
                                            user_table.deselectRow();
                                            table.replaceData();
                                            user_table.redraw();
                                            // let userlist_flag = [];
                                            // cell.getData().whitelisted.forEach(x=>{
                                            //     userlist_flag = true
                                            // });
                                            cell.getData().whitelisted.forEach(a => {
                                                user_table.getRows().every(b => {
                                                    if (b.getData().id == a.id) {
                                                        b.select();
                                                        return false;
                                                    }
                                                    return true;
                                                });
                                            });
                                        });
                                        user_table.options.rowSelectionChanged = function(data, rows) {
                                            d = [];
                                            data.forEach(a => {
                                                d.push(a.id);
                                            });
                                        }
                                    };
                                    a.append(l);
                                }
                                return a;
                        }
                    }
                },
                {
                    title: "Quota",
                    field: "quota",
                    sorter: "number",
                    headerFilter: "input",
                    editor: "number",
                    mutator: (value, data, type, params, cell) => {
                        if (data.type == <?php j(SesiSelfJoin::class) ?>) {
                            if (type == "edit") {
                                $.post("/nlc/sesi", {
                                    _token: <?php j(session_id()) ?>,
                                    id: cell.getData().id,
                                    act: "set_quota",
                                    v: value
                                }, d => {
                                    cell.getData().quota = d;
                                    table.replaceData();
                                    table.redraw();
                                    showMessage("Data updated", "success");
                                }).fail(e => {
                                    table.replaceData();
                                    table.redraw();
                                    showMessage(e.statusText, "danger");
                                });
                                return value;
                            } else
                                return value;
                        }else{
                            return "-";
                        }
                    },
                    mutatorParams: null
                }
            ],
            paginationSize: 20,
            pagination: "local"
        });
    }())
</script>
<?php echo Minifier::outJSMin() ?>