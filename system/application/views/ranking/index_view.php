<?php
	$this->load->view('header');
?>

<div id="content">
	<h2>有feel指数排行榜</h2>
	
	<table class="center" width="100%">
			<tr>
				<th width="80"></th>
				<th>名称</th>
				<th>有feel指数</th>
			</tr>
			<?php foreach( $feel_index_ranking_users as $user ): ?>
			<tr>
				<td>
					<a href="<?=site_url('user/'.$user['id']);?>">
						<img src="<?=$user['profile']['image_url'];?>" width="80" />
					</a>
				</td>
				<td>
					<a href="<?=site_url('user/'.$user['id']);?>">
						<?=$user['profile']['nickname'];?>
					</a>
				</td>
				<td>
					<?=$user['profile']['feel_index'];?>
				</td>
			</tr>
			<?php endforeach; ?>
	</table>
	
</div>


<?php
	$this->load->view('footer');
?>