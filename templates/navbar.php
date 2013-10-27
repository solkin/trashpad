<nav class="navbar navbar-inverse navbar-fixed-top" role="navigation">
  <div class="container">
    <div class="navbar-header">
      <button type="button" class="navbar-toggle" data-toggle="collapse" data-target=".navbar-collapse">
        <span class="icon-bar"></span>
        <span class="icon-bar"></span>
        <span class="icon-bar"></span>
      </button>
      <a href="#write_modal" data-toggle="modal" class="navbar-toggle btn" style="color: white; padding-top: 6px; padding-bottom: 6px;">
        <span class="icon-pencil icon">
        </span>&nbsp;<?= _("Write") ?>
      </a>
      <a class="navbar-brand" href="./"><span class="icon-trash"></span>&nbsp;<?= _("TrashPad") ?></a>
    </div>
    <div class="collapse navbar-collapse">
      <ul class="nav navbar-nav">
        <li <?php if (!$rated && !$random & !$about) echo 'class="active"'; ?>><a href="./">
            <span class="icon-home icon-white"></span> <?= _("Home") ?>
            <span id="fresh_counter" class="badge" style="display:none;">0</span></a>
        </li>
        <li <?php if ($rated) echo 'class="active"'; ?>><a href="./rated.php"><span class="icon-star icon-white"></span> <?= _("Top rated") ?></a></li>
        <li <?php if ($random) echo 'class="active"'; ?>><a href="./random.php"><span class="icon-random icon-white"></span> <?= _("Random") ?></a></li>
        <li class="divider-vertical"></li>
        <li <?php if ($about) echo 'class="active"'; ?>><a href="./about.php"><span class="icon-info-sign icon-white"></span> <?= _("About") ?></a></li>
        <li><a href="#write_modal" data-toggle="modal"><span class="icon-pencil icon-white"></span> <?= _("Post") ?></a></li>
      </ul>
      <form class="navbar-form navbar-right" role="search" action="./query.php">
        <div class="form-group">
          <input type="text" placeholder="<?= _("Search") ?>" class="form-control" name="query" value="<?=$query?>"/>
        </div>
      </form>
    </div>
  </div>
</nav>