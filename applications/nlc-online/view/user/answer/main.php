<?php

/** @var \NLC\Base\Sesi $sesi */
/** @var \NLC\Base\Questions $soal */
?>
<?php ob_start() ?>
<style>
    .abcdef {
        display: grid;
        grid-template-columns: 30px auto;
        padding: 5px;
        margin: 10px 0;
        background: #f2d32b14;
        align-items: center;
    }

    .abcdef.ans {
        background: #fff;
    }

    .abcdef label {
        padding-right: 15px;
        margin: 0;
    }

    .abcdef input {
        margin-right: 0 !important;
    }

    .abcdef>div:nth-child(1) {
        font-weight: bold;
    }

    .abcdef>div:nth-child(2) {
        text-align: center;
    }

    .panel-soal {
        padding: 25px;
        border-radius: 7px;
        background: #fff;
        box-shadow: 0 2px 5px 0 rgba(0, 0, 0, .16), 0 2px 10px 0 rgba(0, 0, 0, .12);
    }

    .tmpl {
        display: none !important;
    }

    .panel-kanan-bold {
        font-weight: bold;
        font-size: 20px;
        text-align: center;
    }
</style>
<?php echo Minifier::outCSSMin() ?>
<script type="text/javascript" src="https://cdnjs.cloudflare.com/ajax/libs/moment.js/2.22.2/moment.min.js"></script>

<div style="max-width:1000px;margin:auto;">
    <div class="row">
        <div class="col-md-8">
            <div class="panel-soal">
                <h2>Lembar jawaban</h2>
                <div class="row" style="margin-top:25px;">
                    <div class="col-6 fcol"></div>
                    <div class="col-6 scol"></div>
                </div>
            </div>
        </div>
        <div class="col-md-4">
            <div class="panel-soal">
                <div>
                    <h3>Download Soal</h3>
                    <div>
                        <a href="/nlc/q?s=<?php h($sesi->id) ?>" target="_blank" rel="noref">
                            <button class="btn btn-danger" style="width:100%;">DOWNLOAD PDF</button>
                        </a>
                        <div class="text-muted" style="margin-top:15px;">Download PDF, dan kerjakan bersama-sama dengan tim kalian!</div>
                    </div>
                    <hr>
                    <h3>Sisa Waktu</h3>
                    <div class="panel-kanan-bold panel-waktu">-</div>
                    <hr>
                    <h3>Terjawab</h3>
                    <div class="panel-kanan-bold panel-terjawab">-</div>
                    <!-- <hr>
                    <h3>Kode Soal</h3>
                    <div class="panel-kanan-bold">NLC2009</div> -->
                </div>
                <div style="margin-top:50px;">
                    <button class="btn btn-primary sss" style="width:100%;">Saya sudah selesai</button>
                </div>
            </div>
        </div>
    </div>
</div>

<div class="abcdef tmpl ans">
    <div class="snum"></div>
    <div style="display:flex;">
        <label><input type="radio" value="0"> A</label>
        <label><input type="radio" value="1"> B</label>
        <label><input type="radio" value="2"> C</label>
        <label><input type="radio" value="3"> D</label>
        <label><input type="radio" value="4"> E</label>
    </div>
</div>

<div class="modal fade" tabindex="-1" role="dialog" id="announcement-pop">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="title" style="font-weight: bold">Pengumuman</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                Mohon untuk membaca pengumuman dengan menekan tombol di bawah ini
            </div>
            <div class="modal-footer">
                <a href="/nlc/pengumuman" class="btn btn-primary">Lihat Pengumuman</a>
            </div>
        </div>
    </div>
</div>

<div class="dot tmpl"></div>

<?php ob_start() ?>
<script>
    (function() {
        let sesi = JSON.parse(atob("<?php echo (base64_encode(json_encode($sesi))) ?>"));
        let soal = sesi.questions;
        let abcdefT = $(".abcdef.tmpl");
        let fcol = $(".fcol");
        let scol = $(".scol");
        let panel_terjawab = $(".panel-terjawab");
        let panel_waktu = $(".panel-waktu");
        let timer;

        let timediff = moment().unix() - Number(<?php echo time() ?>);

        function timerTick() {
            timer = setInterval(() => {
                let d = moment(sesi.end_time) - moment().unix() + timediff;
                let h = String(Math.floor((d % (60 * 60 * 24)) / (60 * 60))).padStart(2, '0');
                let s = String(Math.floor((d % (60)))).padStart(2, '0');
                let m = String(Math.floor((d % (60 * 60)) / (60))).padStart(2, '0');
                panel_waktu.text(`${h}:${m}:${s} detik lagi`);
                if (d <= 0) {
                    clearInterval(timer);
                    fcol.children().remove();
                    scol.children().remove();
                    alert("Waktu pengerjaan sudah habis!");
                    location = "/nlc/survey";
                }
            }, 500);
        }

        function calcTerjawab() {
            panel_terjawab.text(`${Object.keys(sesi.old_answer).length} / ${sesi.questions.question_num} soal`);
        }

        function onAnswered() {
            $(".abcdef input[type=radio]").parent().css('font-weight', "");
            $(".abcdef input[type=radio]:checked").parent().css('font-weight', "bold");
            calcTerjawab();
        }

        function drawABCDEF() {
            fcol.children().remove();
            scol.children().remove();

            let n = soal.question_num;
            let n5 = n / 2;
            let na = Math.ceil(n5);
            let nb = Math.floor(n5);
            for (let i = 1; i <= n; i++) {
                let c = abcdefT.clone().removeClass("tmpl");
                c.find(".snum").text(`${i}.`);
                c.find("input[type=radio]").attr("name", `ans${i}`).prop("selected", false).click(e => {
                    $.post("/nlc/answer", {
                        _token: <?php j(session_id()) ?>,
                        i: sesi.id,
                        n: i,
                        a: e.target.value,
                        act: "j"
                    }, d => {
                        if (d) {
                            sesi.old_answer[i] = e.target.value;
                            c.removeClass("ans");
                            onAnswered();
                        } else {
                            showMessage("Tidak bisa mengirimkan jawaban", "danger");
                        }
                    }).fail(e => {
                        alert("Gagal menyimpan jawaban. Silahkan muat ulang");
                        location.reload();
                    });
                });
                c.appendTo(i <= na ? fcol : scol);
                if (sesi.old_answer[i]) {
                    c.removeClass("ans");
                    c.find(`input[type=radio][value=${sesi.old_answer[i]}]`)[0].checked = true;
                }
            }
            $(".abcdef input[type=radio]:checked").parent().css('font-weight', "bold");
        }

        $(".sss").click(() => {
            if (confirm("Sudah selesai mengerjakan?\nKalian dapat mengubah jawaban selama waktunya masih ada.")) {
                clearInterval(timer);
                fcol.children().remove();
                scol.children().remove();
                location = "/";
            }
        });

        timerTick();
        drawABCDEF();
        calcTerjawab();
        let flag_pop = false;
        let evt = new EventSource("/nlc/a");
        evt.addEventListener("notified", function(event) {
            $("#p-dot-p").removeClass("tmpl");
            if (!flag_pop) {
                $("#announcement-pop").modal();
                flag_pop = true;
            }
        });
    }())
</script>
<?php echo Minifier::outJSMin() ?>
