<?php ob_start()?>
<style>
    #sesi_list{
        background:#fff;
        color:#000;
        border-radius:7px;
        overflow:hidden;
    }

    .sesi_item:hover{
        background:#f0f0f0;
    }

    .sesi_item{
        display:grid;
        grid-template-columns: max-content auto max-content;
        align-items: center;
        grid-gap: 25px;
        padding:15px;
        cursor:pointer;
    }

    .sesi_item:not(:last-of-type){
        border-bottom:1px solid #ececec;
    }

    .sesi_item .fa-scroll{
        font-size: 32px;
        color: #ffb321;
        text-shadow: 2px 2px #ececec;
    }

    .sesi_item .sesi_judul{
        font-size:20px;
        font-weight:bold;
        margin-bottom:5px;
    }

    .sesi_item .rtime{
        font-weight:bold;
    }
</style>
<?php echo Minifier::outCSSMin()?>

<h1>Pilih Sesi</h1><br>

<div style="max-width:600px;">
    <div id="sesi_list">
        <div class="sesi_item">
            <div>
                <i class="fas fa-scroll"></i>
            </div>
            <div clas="sesi_detail">
                <div class="sesi_judul">Warmup 4</div>
                <div style="color:var(--success)"><b>SUDAH DIMULAI</b></div>
                <div>50 menit.</div>
            </div>
            <div>
                <i class="fas fa-chevron-right"></i>
            </div>
        </div>
        <div class="sesi_item">
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
        </div>
    </div>
</div>