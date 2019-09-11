<link href="https://unpkg.com/tabulator-tables@4.3.0/dist/css/semantic-ui/tabulator_semantic-ui.min.css" rel="stylesheet">
<script type="text/javascript" src="https://unpkg.com/tabulator-tables@4.3.0/dist/js/tabulator.min.js"></script>
<script type="text/javascript" src="https://cdnjs.cloudflare.com/ajax/libs/moment.js/2.22.2/moment.min.js"></script>

<h3>Manage Sesi</h3>
<div>
    <div id="data"></div>
</div>

<?php ob_start() ?>
<script>
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
                            showMessage(e.statusText, "danger");
                        });
                        return value;
                    } else
                        return value;
                },
                mutatorParams: null
            },
            {
                title: "Start Time",
                field: "start_time",
                sorter: "number",
                formatter: function(cell, formatterParams) {
                    return moment.unix(parseInt(cell.getValue())).format("LLLL");
                }
            },
            {
                title: "End Time",
                field: "end_time",
                sorter: "number",
                formatter: function(cell, formatterParams) {
                    return moment.unix(parseInt(cell.getValue())).format("LLLL");
                }
            },
            {
                title: "Enabled",
                field: "enabled",
                formatter: function(cell, formatterParams) {
                    return `<div style="text-align:center"><input type="checkbox" value="1" ${cell.getValue() ? "checked" : ""}></div>`;
                },
                cellClick: (e, cell) => {
                    let v = cell.getValue();
                    $.post("/nlc/sesi", {
                        _token: <?php j(session_id()) ?>,
                        id: cell.getData().id,
                        act: "en_toggle"
                    }, d => {
                        cell.getData().enabled = d;
                        table.replaceData();
                        showMessage("Data updated", "success");
                    }).fail(e => {
                        showMessage(e.statusText, "danger");
                    });
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
</script>
<?php echo Minifier::outJSMin() ?>