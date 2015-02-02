<div class="shoplist">
  <?php foreach ($shopList as $index): ?>
    <section>
      <h1 class="h1_gray"><?php echo $index['initial'];?></h1>
      <ul>
        <?php foreach ($index['shop'] as $value): ?>
          <li class="cf">
            <a href="<?php echo $value['link_url'];?>"><span class="shop_title"><?php echo $value['name'];?></span><span class="shop_url"><?php echo $value['top_url'];?></span></a>
          </li>
        <?php endforeach;?>
      </ul>
    </section>
  <?php endforeach;?>
  <p><a class="btn_shoplist tap_white" href="/ios/inquiry/home/query/alliance">あなたのお気に入りのショップを<br>教えてください</a></p>
</div>