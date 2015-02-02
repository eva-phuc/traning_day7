<div class="contact_list">
  <section>
    <ul>
      <?php foreach($query_list as $key => $value): ?>
        <li><a href="query/<?php echo $key;?>?_disfa=<?php echo $_disfa;?>"><?php echo $value;?></a></li>
      <?php endforeach;?>
    </ul>
  </section>
</div>