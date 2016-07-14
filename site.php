<?php
include('my.php');
$title = $config['index']['version'] = '设置用户';
$stylearr = array('file/style.css','file/user.css');
include('template/header.html');
?>
<div class="show">
	<h2>用户设置</h2>
	<table><tbody>
		<tr>
			<th>代号</th>
			<th>COOKIE</th>
			<th>状态</th>
			<th>操作</th>
		</tr>
		<tr><th colspan=4 class="show_wait">获取数据中</th></tr>
	</tbody></table>
</div>
<div class="help">
	<h2>定时任务</h2>
	<table>
		<tr>
			<th>次数</th>
			<th>建议时间表</th>
		</tr>
		<tr>
			<td>较少</td>
			<td class="cs">等待数据中</td>
		</tr>
		<tr>
			<td>一般</td>
			<td class="cc">等待数据中</td>
		</tr>
		<tr>
			<td>较多</td>
			<td class="cm">等待数据中</td>
		</tr>
	</table>
	<br>
	执行URL: <a href="sign.php" id="gourl"></a>
	<br>
	<br>
	<a href="record.php"><button>查看记录</button></a>
	<a href="sign.php"><button>测试签到</button></a>
	<a href="login.php?action=logout"><button>登出</button></a>
</div>
</div>
<script src="file/script.js"></script>
</body>
</html>