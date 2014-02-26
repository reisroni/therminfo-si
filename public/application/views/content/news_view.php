<?php
$data = array(
		'page' => 'news_menu',
		'title' => 'News &amp; Contributions!',
		'css_files' => array('public/css/pages/news.css'),
		'js_files' => array());

$this->load->view('layout/header', $data);
?>
<div id="pageContentMain">
	<div id="pageContentTextPanel" class="center bodyText">
        <table class="center news-Table" cellspacing="15">
			<caption><h2 class="orangeText">News</h2></caption>
			<tbody>
                <?php if ($news): ?>
                    <?php foreach ($news as $n): ?>
                    <tr>
                        <td class="textCenter"><strong><?php echo $n->date. ', ' .$n->year; ?></strong></td>
                        <td>
                            <h4><?php echo $n->title; ?></h4>
                            <?php echo $n->content; ?>
                        </td>
                    </tr>
                    <?php endforeach; ?>
                <?php else: ?>
                    <tr>
                        <td class="textCenter"><strong><?php echo date('F'). ', ' .date('Y'); ?></strong></td>
                        <td>
                            <h4>No News</h4>
                            <p>Without news loaded.</p>
                        </td>
                    </tr>
                <?php endif; ?>
			</tbody>
		</table>
		<table id="contributionsTable" class="center news-Table" cellspacing="15">
			<caption><h2 class="orangeText">Top 5 Contributions</h2></caption>
			<tbody>
				<?php echo $contributions ?>
			</tbody>
		</table>
	</div>
</div>
<?php $this->load->view('layout/footer'); ?>