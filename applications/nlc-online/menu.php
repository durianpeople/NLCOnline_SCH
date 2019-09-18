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
    <a href="/administrator">
        <i class="fas fa-user-shield" style="padding: 0 15px;width: 50px;text-align: center;"></i>
        <span>Administrator</span>
        <i class="icon s-18 pull-right"></i>
    </a>
<?php elseif (PuzzleUser::isAccess(USER_AUTH_REGISTERED)) : ?>
    <a href="/nlc/sesi">
        <i class="icon icon-th-list purple-text s-18"></i>
        <span>Sesi</span>
        <i class="icon s-18 pull-right"></i>
    </a>
<?php endif ?>