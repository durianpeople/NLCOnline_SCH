<?php

use NLC\Base\Sesi;
use NLC\Sesi\SesiSelfJoin;
use NLC\Sesi\SesiTerbuka;
use NLC\Sesi\SesiPrivate;
?>
<script type="text/javascript" src="https://cdnjs.cloudflare.com/ajax/libs/moment.js/2.17.1/moment-with-locales.min.js"></script>
<?php ob_start(); ?>
<style>
    #sesi_list {
        background: #fff;
        color: #000;
        border-radius: 7px;
        overflow: hidden;
    }

    .sesi_item {
        display: grid;
        grid-template-columns: max-content auto max-content;
        align-items: center;
        grid-gap: 25px;
        padding: 15px;
    }

    .sesi_item:not(:last-of-type) {
        border-bottom: 1px solid #ececec;
    }

    .sesi_item .fa-scroll {
        font-size: 32px;
        color: #ffb321;
        text-shadow: 2px 2px #ececec;
    }

    .sesi_item .sesi_judul {
        font-size: 20px;
        font-weight: bold;
        margin-bottom: 5px;
    }

    .sesi_item .rtime {
        font-weight: bold;
    }

    .tmpl {
        display: none !important;
    }
</style>
<?php echo Minifier::outCSSMin() ?>

<h1>Pilih Sesi</h1><br>

<div style="max-width:600px;">
    <div id="sesi_list">
        <div class="sesi_item tmpl">
            <div>
                <i class="fas fa-scroll"></i>
            </div>
            <div clas="sesi_detail">
                <div class="sesi_judul">Warmup 4</div>
                <div style="color:var(--success); text-transform: uppercase; font-weight: bold;" class="status"><b></b></div>
                <div class="q_c">Sisa Kuota: <span class="quota"></span></div>
                <div class="time"></div>
            </div>
            <div>
                <button class="btn btn-primary join-btn"><span class="join">Kerjakan Soal</span> <i class="fas fa-chevron-right"></i></button>
            </div>
        </div>
        <!-- <div class="sesi_item">
            <div>
                <i class="fas fa-scroll"></i>
            </div>
            <div clas="sesi_detail">
                <div class="sesi_judul">Warmup 4</div>
                <div>Dimulai pukul 9.00, <span class="rtime">3:05 menit lagi</span>.</div>
                <div>50 menit.</div>
            </div>
            <div>
                <i class="fas fa-chevron-right"></i>
            </div>
        </div> -->
    </div>
</div>

<?php ob_start() ?>
<script>
    (function() {
        moment.locale("id");

        let s = <?php j(Sesi::list(true)) ?>;
        let t = $(".sesi_item.tmpl");
        let parent_list = $("#sesi_list");
        let status_list = [
            ["Belum mulai", "var(--secondary)"],
            ["Sedang berlangsung", "var(--success)"],
            ["Selesai", "var(--danger)"],
            ["Kuota penuh", "var(--danger)"],
            ["Tidak diizinkan", "var(--danger)"],
        ];

        function refetch() {
            $.post("/nlc/sesi", {
                _token: <?php j(session_id()) ?>,
                act: "fetch",
            }, d => {
                s = d;
            }).fail(e => {
                if (e.statusText != null || e.statusText != undefined || e.statusText != "" || e.statusText == "error")
                    showMessage(e.statusText, "danger");
                else showMessage("Gagal mengambil informasi sesi. Silahkan cek koneksi dan refresh");
            });
        }

        function register_timer(timer_el, time_target, label, onrunoutoftime) {
            return setInterval(() => {
                let d = moment(time_target) - moment().unix();
                let h = String(Math.floor((d % (60 * 60 * 24)) / (60 * 60))).padStart(2, '0');
                let s = String(Math.floor((d % (60)))).padStart(2, '0');
                let m = String(Math.floor((d % (60 * 60)) / (60))).padStart(2, '0');
                timer_el.html(`${label} <b>${h}:${m}:${s} detik</b>`);
                if (d <= 0) {
                    onrunoutoftime();
                }
            }, 500);
        }

        function draw() {
            parent_list.html("");
            s.forEach(i => {
                let el = t.clone();
                let timer;
                el.removeClass("tmpl");
                el.find(".sesi_judul").html(i.name);
                el.find(".status").html(status_list[Number(i.status)][0]);
                el.find(".status").css("color", status_list[Number(i.status)][1]);
                switch (Number(i.status)) {
                    case 0:
                        timer = register_timer(el.find(".time"), Number(i.start_time), "Dimulai dalam", function() {
                            clearInterval(timer);
                            i.status = 1;
                            draw();
                        });
                        break;
                    case 1:
                        timer = register_timer(el.find(".time"), Number(i.end_time), "Berakhir dalam", function() {
                            clearInterval(timer);
                            i.status = 2;
                            draw();
                        });
                        break;
                }
                let jb = el.find(".join-btn");
                switch (i.type) {
                    case <?php j(SesiSelfJoin::class) ?>:
                        el.find(".quota").html(i.remaining);
                        if ((i.status != 1 && i.joined) || i.status == 2) jb.remove();
                        else if (!i.joined) {
                            jb.text("Daftar ke Sesi Ini").click(e => {
                                jb[0].disabled = true;
                                $.post("/nlc/sesi", {
                                    _token: <?php j(session_id()) ?>,
                                    act: "join",
                                    id: i.id,
                                }, d => {
                                    s = d;
                                    draw();
                                }).fail(e => {
                                    jb[0].disabled = false;
                                    if (e.statusText != null || e.statusText != undefined || e.statusText != "" || e.statusText == "error")
                                        showMessage(e.statusText, "danger");
                                    else showMessage("Hanya bisa daftar di satu sesi saja");
                                });
                            });
                        } else if (i.status == 1 && i.joined) {
                            jb.text("Kerjakan Soal").click(e => {
                                location.href = `/nlc/answer?s=${i.id}`;
                            });
                        }
                        break;
                    case <?php j(SesiPrivate::class) ?>:
                    case <?php j(SesiTerbuka::class) ?>:
                        el.find(".q_c").remove();
                        if (i.status != 1 || i.status == 2) jb.remove();
                        else if (i.status == 1) {
                            jb.text("Kerjakan Soal").click(e => {
                                location.href = `/nlc/answer?s=${i.id}`;
                            });
                        }
                        break;
                }
                el.appendTo(parent_list);
            });
        };
        refetch();
        draw();
    }())
</script>
<?php echo Minifier::outJSMin() ?>