<?php if ($random || $thread_id || empty($threads_list)): ?>
  <div class="row" style="padding-bottom: 15px;">
  <div class="col-lg-3"></div>
  <div class="col-lg-6">
  <?php if ($random): ?>
    <form class="form-inline" action="./random.php" method="post">
    <button class="btn btn-lg btn-info btn-block" type="submit" id="big_green_button"> <?= _("One more random") ?> </button>
    </form>
  <? elseif ($thread_id || empty($threads_list)): ?>
    <form class="form-inline" action="./" method="post">
    <button class="btn btn-lg btn-success btn-block" type="submit" id="big_green_button"><?= _("To other threads") ?></button>
    </form>
  <? endif; ?>
  </div>
  </div>
<? endif; ?>