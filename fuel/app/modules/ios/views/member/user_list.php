<div class="contact_list">
  <section>
    <ul>
<?php foreach(\Config::get('user.mappings.user') as $label => $href): ?>
    <?php if('premium_link' == $label): //iOS版は非表示  ?>
    <?php else: ?>
    <li><a href="/ios<?php echo $href ?>"><?php echo $label ?></a></li>
    <?php endif; ?>
<?php endforeach; ?>
    </ul>
  </section>
</div>