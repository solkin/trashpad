<div id="write_modal" class="modal fade" tabindex="-1" role="dialog" aria-hidden="true">
  <div class="modal-dialog">
    <div class="modal-content">
      <div class="modal-header">
        <button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
        <h4 class="modal-title"><?= _("Post thread") ?></h4>
      </div>
      <form onsubmit="post_thread(name, feedback, message, post_button);
          return false;" method="post" class="form-horizontal" role="form">
        <div class="modal-body">
          <div class="alert alert-success" id="success_alert" style="display:none;">
            <strong><?= _("Congratulations!") ?></strong> <?= _("New thread successfully posted! Redirecting...") ?>
          </div>
          <div class="alert alert-warning" id="warning_alert" style="display:none;">
            <strong><?= _("Heads up!") ?></strong> <?= _("You must fill at least message field.") ?>
          </div>
          <div class="alert alert-danger" id="error_alert" style="display:none;">
            <strong><?= _("Errrm.") ?></strong> <?= _("Something was wrong on the host during post this thread.") ?>
          </div>
          <div class="form-group">
            <label for="inputName" class="col-lg-3 control-label"><?= _("Name") ?></label>
            <div class="col-lg-9">
              <input name="name" type="text" class="form-control" id="inputName" placeholder="<?= _("Name") ?>">
            </div>
          </div>
          <div class="form-group">
            <label for="inputFeedback" class="col-lg-3 control-label"><?= _("Feedback") ?></label>
            <div class="col-lg-9">
              <input name="feedback" type="email" class="form-control" id="inputFeedback" placeholder="<?= _("Feedback") ?>">
            </div>
          </div>
          <div class="form-group">
            <label for="inputFeedback" class="col-lg-3 control-label"><?= _("Message") ?></label>
            <div class="col-lg-9">
              <textarea name="message" rows="3" maxlength="<?= $thread_length ?>" class="form-control" id="inputMessage" placeholder="<?= _("Your message here") ?>"></textarea>
            </div>
          </div>
        </div>
        <div class="modal-footer">
          <button type="button" class="btn btn-default" data-dismiss="modal"><?= _("Close") ?></button>
          <button type="submit" class="btn btn-primary" name="post_button"><?= _("Post thread") ?></button>
        </div>
      </form>
    </div>
  </div>
</div>