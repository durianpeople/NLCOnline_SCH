<?php

use NLC\Base\Announcement;

?>

<?php ob_start(); ?>
<style>
    #announce_list {
        background: #fff;
        color: #000;
        border-radius: 7px;
        overflow: hidden;
        z-index: 1;
    }

    .announce_item {
        display: grid;
        grid-template-columns: max-content auto max-content;
        align-items: center;
        grid-gap: 25px;
        padding: 30px;
        cursor: pointer;
    }

    .announce_item:not(:last-of-type) {
        border-bottom: 1px solid #ececec;
    }

    .announce_item .fa-scroll {
        font-size: 32px;
        color: #ffb321;
        text-shadow: 2px 2px #ececec;
    }

    .announce_item .announce_judul {
        font-size: 20px;
        font-weight: bold;
        margin-bottom: 5px;
    }

    .announce_item .rtime {
        font-weight: bold;
    }

    .tmpl {
        display: none !important;
    }

    .deactivated {
        color: #6b6b6b;
    }
</style>
<?php echo Minifier::outCSSMin() ?>

<h1>Pengumuman</h1><br>
<div style="max-width:600px;">
    <div id="announce_list">
        <div class="announce_item tmpl">
            <div clas="announce_detail">
                <div class="announce_judul">Warmup 4</div>
            </div>
        </div>
    </div>
</div>

<div class="modal fade" tabindex="-1" role="dialog" id="announcement">
    <div class="modal-dialog modal-lg" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="title" style="font-weight: bold">Pengumuman</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">

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
        let s = <?php j(Announcement::list()) ?>;
        let t = $(".announce_item.tmpl");
        let parent_list = $("#announce_list");

        function markAsRead(id) {
            $.post("/nlc/pengumuman", {
                _token: <?php j(session_id()) ?>,
                act: "mark",
                id: id,
            });
        }

        function refetch() {
            $.post("/nlc/pengumuman", {
                _token: <?php j(session_id()) ?>,
                act: "fetch",
            }, d => {
                s = d;
            }).fail(e => {
                showMessage(e.responseJSON.error, "danger");
            });
        }

        function draw() {
            parent_list.html("");
            s.forEach(i => {
                let el = t.clone();
                el.removeClass("tmpl");
                el.find(".announce_judul").html(i.title);
                if (i.is_read) {
                    el.addClass("deactivated");
                }
                el.click(function() {
                    $("#announcement").find("#title").html(i.title);
                    $("#announcement").find(".modal-body").html(i.content);
                    $("#announcement").modal();
                    markAsRead(i.id);
                    el.addClass("deactivated");
                });
                el.appendTo(parent_list);
            });
        };
        refetch();
        draw();
        let evt = new EventSource("/nlc/a");
        evt.addEventListener("notified", function(event) {
            $("#p-dot-p").removeClass("tmpl");
        });
    }())
</script>
<?php echo Minifier::outJSMin() ?>