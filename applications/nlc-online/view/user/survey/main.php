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
        <div class="ask">
            <h4>Kualitas soal-soal babak penyisihan:</h4>
            <label class="radio-inline">
                <input type="radio" name="optradio" checked>1
            </label>
            <label class="radio-inline">
                <input type="radio" name="optradio">2
            </label>
            <label class="radio-inline">
                <input type="radio" name="optradio">3
            </label>
            <label class="radio-inline">
                <input type="radio" name="optradio">4
            </label>
        </div>
        <div class="ask">
            <h4>Pelayanan panitia:</h4>
            <label class="radio-inline">
                <input type="radio" name="optradio" checked>1
            </label>
            <label class="radio-inline">
                <input type="radio" name="optradio">2
            </label>
            <label class="radio-inline">
                <input type="radio" name="optradio">3
            </label>
            <label class="radio-inline">
                <input type="radio" name="optradio">4
            </label>
        </div>
        <div class="ask">
            <h4>Tingkat kepuasan secara keseluruhan:</h4>
            <label class="radio-inline">
                <input type="radio" name="optradio" checked>1
            </label>
            <label class="radio-inline">
                <input type="radio" name="optradio">2
            </label>
            <label class="radio-inline">
                <input type="radio" name="optradio">3
            </label>
            <label class="radio-inline">
                <input type="radio" name="optradio">4
            </label>
        </div>
        
        <br>
        <a href="/nlc/sesi" class="btn btn-primary">Masuk</a>

    <?php else : ?>

        <h1>Terima kasih atas responnya</h1>
        Sampai jumpa di Schematics berikutnya :D

    <?php endif; ?>

</div>