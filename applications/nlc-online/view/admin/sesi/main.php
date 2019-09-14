<?php

use NLC\Base\Questions;
?>

<link href="https://unpkg.com/tabulator-tables@4.3.0/dist/css/semantic-ui/tabulator_semantic-ui.min.css" rel="stylesheet">
<script type="text/javascript" src="https://unpkg.com/tabulator-tables@4.3.0/dist/js/tabulator.min.js"></script>
<script type="text/javascript" src="https://cdnjs.cloudflare.com/ajax/libs/moment.js/2.22.2/moment.min.js"></script>

<h3>Manage Sesi</h3>
<div>
    <div id="data"></div>
</div>

<?php ob_start() ?>
<script>
    (function() {
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
                                showMessage("Data updated", "success");
                            }).fail(e => {
                                table.replaceData();
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
                                showMessage("Data updated", "success");
                            }).fail(e => {
                                table.replaceData();
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
                                showMessage("Data updated", "success");
                            }).fail(e => {
                                table.replaceData();
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
                                showMessage("Data updated", "success");
                            }).fail(e => {
                                table.replaceData();
                                showMessage(e.statusText, "danger");
                            });
                        };
                        if (v) {
                            if (confirm("DANGER!\nMatikan sesi ini? Semua data jawaban peserta akan dihapus!!")) {
                                f();
                            } else {
                                table.replaceData();
                            }
                        } else f();
                    }
                },
                {
                    title: "For Public?",
                    field: "is_public",
                    formatter: function(cell, formatterParams) {
                        return cell.getValue() ? "Yes" : `<span style="var(--danger)">NO</span>`
                    }
                },
                // nama, waktu mulai, waktu selesai, public?, enabled?
            ],
            paginationSize: 20,
            pagination: "local"
        });
    }())
</script>
<?php echo Minifier::outJSMin() ?>