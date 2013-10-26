<div class="panel panel-default">
<div class="panel-heading">

<?php if (!empty($name)): ?>
  <strong><span class="icon-user"></span>&nbsp;<?=$name?></strong><br/>
<?php endif; ?>
<?php if (!empty($feedback)): ?>
  <a href="mailto:<?=$feedback?>"><span class="icon-envelope"></span>&nbsp;<?=$feedback?></a><br/>
<?php endif; ?>

<span class="icon-calendar"></span>&nbsp;<?=$thread_date?>&nbsp;
<span class="icon-time"></span>&nbsp;<?=$thread_time?>
<div class="twitter">
  <a id="twitter_<?=$thread_id?>" href="http://twitter.com/intent/tweet?text=<?=$direct_link?>" title="<?php echo _("Share via Twitter")?>" target="_blank"></a>
</div>
<div class="vkontakte">
  <a id="vkontakte_<?=$thread_id?>" href="http://vk.com/share.php?url=<?=$direct_link?>" title="<?php echo _("Share via VK")?>" onclick="window.open(this.href, 'Опубликовать ссылку во Вконтакте', 'width=800,height=300'); return false"></a>
</div>
<div class="facebook">
  <a id="facebook_<?=$thread_id?>" href="https://www.facebook.com/sharer/sharer.php?u=<?=$direct_link?>" title="<?php echo _("Share via Facebook")?>" onclick="window.open(this.href, 'Опубликовать ссылку в Facebook', 'width=640,height=436,toolbar=0,status=0'); return false"></a>
</div>
</div>
<div class="panel-body">
<form class="form-inline" method="post">
<?php if ($admin): ?>
  <button class="btn btn-xs btn-danger" type="button" id="remove_button_<?=$thread_id?>" name="remove_button" onclick="remove_thread('<?=$thread_id?>', '<?=$admin_key?>'); return false;"><span class="icon-trash icon-white"></span></button>&nbsp;
  <button class="btn btn-xs btn-default" type="buton" id="reset_button_<?=$thread_id?>" name="karma_reset_button" onclick="karma_reset('<?=$thread_id?>', '<?=$admin_key?>'); return false;"><span class="icon-hand-down"></span></button>&nbsp;
  <button class="btn btn-xs btn-info" id="info_popover_<?=$thread_id?>" data-trigger="click" rel="popover" data-content="<?=$user_agent?>" data-original-title="<?=$ip?>" onclick="return false;"><span class="icon-info-sign"></span></button>&nbsp;
  <script>$(function () {$('#info_popover_<?=$thread_id?>').popover();});</script>
<?php endif; ?>
<div class="btn-group">
  <button class="btn btn-xs btn-success" type="submit" id="like_button_<?=$thread_id?>" onclick="karma_update('<?=$thread_id?>', 1); return false;"><span class="icon-thumbs-up-alt"></span></button>
  <button class="btn btn-xs btn-warning" type="submit" id="fire_button_<?=$thread_id?>" onclick="karma_update('<?=$thread_id?>', -1); return false;"><span class="icon-thumbs-down-alt"></span></button>
</div>
&nbsp;<span class="label label-<?=$label_type?>" id="karma_counter_<?=$thread_id?>"><?=$karma?></span>&nbsp;<?=$message?></form>
<hr>
<form class="form-inline" onsubmit="post_reply(<?=thread_id?>, <?=message?>, <?=reply_button?>); return false;" method="post">
  <input type="hidden" name="<?=thread_id?>" value="<?=$thread_id?>"/>
  <div class="input-group">
    <input class="form-control" type="text" maxlength="<?=$reply_length?>" id="reply_message_<?=$thread_id?>" name="message" placeholder="<?php echo _("Your reply here")?>"/>
    <span class="input-group-btn">
      <button type="submit" class="btn btn-primary" id="reply_button_<?=$thread_id?>" name="reply_button"><?php echo _("Reply")?></button>
    </span>
  </div>
</form>
<br>
<div id="<?=$thread_id?>" class="col-md-10">
<?php foreach ($reply_list as $reply): ?>
  <div id="<?=$reply['reply_id']?>"><p>
  <?php if ($admin): ?>
    <button class="btn btn-xs btn-info" id="info_popover_<?=$reply['reply_id']?>" data-trigger="click" rel="popover" data-content="<?=$reply['user_agent']?>" data-original-title="<?=$reply['ip']?>" onclick="return false;"><span class="icon-info-sign"></span></button>
    <script>$(function () {$('#info_popover_<?=$reply['reply_id']?>').popover();});</script>
    <button class="btn btn-xs btn-danger" type="submit" id="reply_remove_button_<?=$reply['reply_id']?>" onclick="remove_reply('<?=$reply['reply_id']?>', '<?=$admin_key?>'); return false;"><span class="icon-trash"></span></button>&nbsp;
  <?php endif; ?>
  <span class="icon-comment"></span>&nbsp;<?=$reply['message']?></p>
  </div>
  <?php endforeach; ?>
</div>
</div>

<div class="panel-footer" style="text-align: right;">
<?php if ($os): ?>
  <img src="./images/os/<?=$os[1]?>.png" alt="<?=$os[0]?>">&nbsp;<?=$os[0]?>&nbsp;
<?php endif; ?>
<?php if ($browser): ?>
  <img src="./images/browser/<?=$browser[1]?>.png" alt="<?=$browser[0]?>">&nbsp;<?=$browser[0]?>
<?php endif; ?>
</div>
</div>