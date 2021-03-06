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
        z-index: 1;
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
    <img src="<?php h(IO::publish(my_dir("view/assets"))) ?>/object1.png" style="position: absolute;bottom: 100px; right: 50px; width: 100px;z-index: -1" />
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
                2Mohon untuk membaca pengumuman dengan menekan tombol di bawah ini
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
        moment.locale("id");
        let timediff = moment().unix() - Number(<?php echo time() ?>);
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
                showMessage(e.responseJSON.error, "danger");
            });
        }

        function register_timer(timer_el, time_target, label, onrunoutoftime) {
            return setInterval(() => {
                let d = moment(time_target) - moment().unix() + timediff;
                let h = String(Math.floor((d) / (60 * 60))).padStart(2, '0');
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
                                if (confirm("Daftar ke sesi ini?")) {
                                    jb[0].disabled = true;
                                    $.post("/nlc/sesi", {
                                        _token: <?php j(session_id()) ?>,
                                        act: "join",
                                        id: i.id,
                                    }, d => {
                                        showMessage("Berhasil terdaftar!", "success");
                                        s = d;
                                        draw();
                                    }).fail(e => {
                                        jb[0].disabled = false;
                                        showMessage(e.responseJSON.error, "danger");
                                    });
                                }
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