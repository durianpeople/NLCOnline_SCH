<?php ob_start() ?>
<style>
    .radio-inline {
        padding-right: 15px;
    }

    .ask {
        padding-top: 40px;
    }
</style>
<?php echo Minifier::outCSSMin() ?>

<div style="padding: 50px;background: white; margin: auto; border-radius: 25px;line-height: 35px;">

    <?php if ($eligible) : ?>

        <h1>Kuisioner Kepuasan Schematics NLC 2019</h1>
        <form method="POST" id="survey-frm">
            <div class="ask">
                <h4>Kualitas soal-soal babak penyisihan:</h4>
                <label class="radio-inline">
                    <input type="radio" name="kualitas" value="1">1
                </label>
                <label class="radio-inline">
                    <input type="radio" name="kualitas" value="2">2
                </label>
                <label class="radio-inline">
                    <input type="radio" name="kualitas" value="3">3
                </label>
                <label class="radio-inline">
                    <input type="radio" name="kualitas" value="4">4
                </label>
            </div>
            <div class="ask">
                <h4>Pelayanan panitia:</h4>
                <label class="radio-inline">
                    <input type="radio" name="pelayanan" value="1">1
                </label>
                <label class="radio-inline">
                    <input type="radio" name="pelayanan" value="2">2
                </label>
                <label class="radio-inline">
                    <input type="radio" name="pelayanan" value="3">3
                </label>
                <label class="radio-inline">
                    <input type="radio" name="pelayanan" value="4">4
                </label>
            </div>
            <div class="ask">
                <h4>Tingkat kepuasan secara keseluruhan:</h4>
                <label class="radio-inline">
                    <input type="radio" name="kepuasan" value="1">1
                </label>
                <label class="radio-inline">
                    <input type="radio" name="kepuasan" value="2">2
                </label>
                <label class="radio-inline">
                    <input type="radio" name="kepuasan" value="3">3
                </label>
                <label class="radio-inline">
                    <input type="radio" name="kepuasan" value="4">4
                </label>
            </div>
            <button type="submit" class="btn btn-primary">Kirim</button>
        </form>

    <?php else : ?>

        <h1>Terima kasih atas responnya</h1>
        Sampai jumpa di Schematics berikutnya :D

    <?php endif; ?>

</div>

<?php ob_start() ?>
<script>
    (function() {
        $("#survey-frm").submit(function(e) {
            e.preventDefault();
            let f = $(e.target).serialize();
            f += `&act=submit&_token=<?php echo (session_id()) ?>`;
            console.log(f);
            $.post("/nlc/survey", f, d => {
                location.reload();
            }).fail(e => {
                showMessage("Terjadi kesalahan", "danger");
            })
        });
    })();
</script>
<?php echo Minifier::outJSMin() ?>