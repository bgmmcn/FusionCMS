<?php
defined('BASEPATH') OR exit('No direct script access allowed');
?>

<div style="border:1px solid #990000;padding-left:20px;margin:0 0 10px 0;">

<h4>发生PHP错误</h4>

<p>严重性: <?php echo $severity; ?></p>
<p>消息:  <?php echo $message; ?></p>
<p>文件名: <?php echo $filepath; ?></p>
<p>行号: <?php echo $line; ?></p>

<?php if (defined('SHOW_DEBUG_BACKTRACE') && SHOW_DEBUG_BACKTRACE === TRUE): ?>

	<p>堆栈跟踪:</p>
	<?php foreach (debug_backtrace() as $error): ?>

		<?php if (isset($error['file']) && strpos($error['file'], realpath(BASEPATH)) !== 0): ?>

			<p style="margin-left:10px">
			文件: <?php echo $error['file'] ?><br />
			行号: <?php echo $error['line'] ?><br />
			函数: <?php echo $error['function'] ?>
			</p>

		<?php endif ?>

	<?php endforeach ?>

<?php endif ?>

</div>