<?php if (PuzzleUser::isAccess(USER_AUTH_EMPLOYEE)) : ?>
    <a href="/nlc/soal">
        <i class="icon icon-documents3 blue-text s-18"></i>
        <span>Paket Soal</span>
        <i class="icon s-18 pull-right"></i>
    </a>
    <a href="/nlc/sesi">
        <i class="icon icon-th-list purple-text s-18"></i>
        <span>Sesi</span>
        <i class="icon s-18 pull-right"></i>
    </a>
    <a href="/nlc/nilai">
        <i class="icon icon-documents3 blue-text s-18"></i>
        <span>Nilai</span>
        <i class="icon s-18 pull-right"></i>
    </a>
    <?php if (PuzzleUser::isAccess(USER_AUTH_SU)) : ?>
        <a href="/admin">
            <i class="fas fa-user-shield" style="padding: 0 15px;width: 50px;text-align: center;"></i>
            <span>Administrator</span>
            <i class="icon s-18 pull-right"></i>
        </a>
    <?php endif; ?>
<?php elseif (PuzzleUser::isAccess(USER_AUTH_REGISTERED)) : ?>
    <a href="/nlc/nilai">
        <i class="icon icon-file s-18"></i>
        <span>Hasil Warm-up</span>
        <i class="icon s-18 pull-right"></i>
    </a>
    <a href="/nlc/sesi">
        <i class="icon icon-th-list purple-text s-18"></i>
        <span>Sesi</span>
        <i class="icon s-18 pull-right"></i>
    </a>
<?php endif ?>