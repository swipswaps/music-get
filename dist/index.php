<!--This software is licensed under the GNU GPL v3 -->
<!--Written by William Seymour-->

<?php
	$server_name = $_SERVER['SERVER_NAME'];
	$client_ip = $_SERVER['REMOTE_ADDR'];
?>

<!DOCTYPE html>
<html lang="en">
	<head>
		<meta charset="utf-8">
		<title>music-get</title>
		<meta name="description" content="So you can play music at LAN!"/>
		<meta name="viewport" content="width=1000, initial-scale=1.0, maximum-scale=1.0">

		<!-- Loading Bootstrap -->
		<link href="dist/css/vendor/bootstrap/css/bootstrap.min.css" rel="stylesheet">

		<!-- Loading Flat UI -->
		<link href="dist/css/flat-ui.css" rel="stylesheet">
		<link href="docs/assets/css/demo.css" rel="stylesheet">
		<link href="dist/css/music-get.css" rel="stylesheet">
		<link rel="shortcut icon" href="img/favicon.ico">

		<!-- HTML5 shim, for IE6-8 support of HTML5 elements. All other JS at the end of file. -->
		<!--[if lt IE 9]>
			<script src="dist/js/vendor/html5shiv.js"></script>
	  		<script src="dist/js/vendor/respond.min.js"></script>
		<![endif]-->
	</head>
	<body onload="init()">
		<div class="container">
			<div class="row demo-row">
				<div class="col-xs-12">
					<nav class="navbar navbar-inverse navbar-embossed" role="navigation">
						<div class="navbar-header">
							<button type="button" class="navbar-toggle" data-toggle="collapse" data-target="#navbar-collapse-01">
								<span class="sr-only">Toggle navigation</span>
							</button>
			  				<a class="navbar-brand" href="index.php">music-get</a>
						</div>
						<div class="collapse navbar-collapse" id="navbar-collapse-01">
							<ul class="nav navbar-nav navbar-left">
								<li><a href="index.php">Queue</a></li>
								<li><a href="about.html">About</a></li>
							</ul>
						</div><!-- /.navbar-collapse -->
					</nav><!-- /navbar -->
				</div>
			</div> <!-- /row -->

			<div class="demo-type-example">
				<h3>Bringing you LAN classics since 2015</h3>
			</div>

			<div class="login-form">
				<div class="alert alert-info" id"error">
					Make queueing your favourite memes easier with the new <a target="blank" href="https://chrome.google.com/webstore/detail/music-get-autoqueue/iomcfpdngiolnefjdehdaoanlilbjiml">Chrome</a> and <a target="blank" href="https://addons.mozilla.org/en-GB/firefox/addon/music-get-autoqueue/">Firefox</a> extensions!
				</div>
				<div class="alert alert-danger" id="error" style="display:none">
				</div>
				<form action="Javascript:sendFile()" method="post" enctype="multipart/form-data" id="fileForm">
					<div class="row">
						<div class="col-xs-6">
			  				<div class="form-group">
								<input type="file" class="form-control" name="file" id="file"/>
							</div>
						</div>
						<div class="col-xs-6">
							<div class="form-group">
								<input type="submit" class="btn btn-primary btn-lg btn-block" value="Upload File">
							</div>
						</div>
					</div>
				</form>
				<form action="Javascript:sendURL()" method="post" enctype="multipart/form-data">
					<div class="row">
						<div class="col-xs-6">
							<div class="form-group">
								<input type="text" class="form-control" name="url" id="url" placeholder="Youtube/Vimeo etc. URL"/>
							</div>
						</div>
						<div class="col-xs-6">
							<div class="form-group">
								<input type="submit" class="btn btn-primary btn-lg btn-block" value="Upload from URL">
							</div>
						</div>
					</div>
				</form>

				<div class="form-group">
					<div id="downloading">
					</div>
					<h6 id="current"><?=file_get_contents("http://music.lan/current.php")?></h6>
					<div id="playing">
						<?=file_get_contents("http://music.lan/playing.php")?>
					</div>
				</div>

				<?php if (file_get_contents("http://music.lan/api/alias?ip=" . rawurlencode($client_ip)) == "canalias") {?>
				<div class="form-row" id="aliasPane">
					<h6>Set an alias for yourself (once per LAN)</h6>
					<form action="Javascript:setAlias()" method="post" enctype="multipart/form-data">
						<div class="row">
							<div class="col-xs-6">
								<div class="form-group">
									<input type="text" class="form-control" name="alias" id="alias" placeholder="What would you like to be called?"/>
								</div>
							</div>
							<div class="col-xs-6">
								<div class="form-group">
									<input type="submit" class="btn btn-primary btn-lg btn-block" value="Set alias">
								</div>
							</div>
						</div>
					</form>
				</div>
				<?php } ?>

	  		</div>
		</div> <!-- /container -->

		<script src="dist/js/vendor/jquery.min.js"></script>
		<script src="dist/js/flat-ui.min.js"></script>
		<script src="docs/assets/js/application.js"></script>

		<script>
		function remove_item(arg) {
			$.ajax({url: 'http://<?=$server_name?>/api/remove',
				method: 'POST', data: {'guid': arg}})
		}
		function init(){
			document.cookie = "music-get-client=<?=$client_ip?>";
			window.setInterval(function(){
				$.ajax({url: 'http://<?=$server_name?>/current.php', method: 'POST'})
					.done(function(data){document.getElementById("current").innerHTML = data;});
				$.ajax({url: 'http://<?=$server_name?>/playing.php', method: 'POST'})
					.done(function(data){document.getElementById("playing").innerHTML = data;});
				$.ajax({url: 'http://<?=$server_name?>/downloading.php', method: 'POST'})
					.done(function(data){document.getElementById("downloading").innerHTML = data ;});
			}, 1000);
		}
		function sendFile(){
			var fileForm = document.getElementById('fileForm');
			var request = new XMLHttpRequest();
			request.onload = function () {
				if (request.status == 200) {
					document.getElementById("file").value = "";
					document.getElementById("error").style.display = "none";
				} else if (request.status == 403) {
					document.getElementById("error").textContent = "You have exceeded the item limit!";
					document.getElementById("error").style.display = "inherit";
				} else {
					document.getElementById("error").textContent = "There was a problem submitting your file!";
					document.getElementById("error").style.display = "inherit";
				}
			}
			formData = new FormData(fileForm);
			request.open('POST', 'http://music.lan/api/add', true);
			request.send(formData);
		}
		function sendURL(){
			var request = new XMLHttpRequest();
			var postData = "url=" + document.getElementById("url").value;
			request.onload = function () {
				if (request.status == 200) {
					document.getElementById("url").value = "";
					document.getElementById("error").style.display = "none";
				} else if (request.status == 403) {
					document.getElementById("error").textContent = "You have exceeded the item limit!";
					document.getElementById("error").style.display = "inherit";
				} else {
					document.getElementById("error").textContent = "There was a problem submitting your URL!";
					document.getElementById("error").style.display = "inherit";
				}
			}
			request.open('POST', 'http://music.lan/api/url', true);
			request.setRequestHeader("Content-Type", "application/x-www-form-urlencoded");
			request.send(postData);
		};
		function setAlias(){
			var request = new XMLHttpRequest();
			var postData = "alias=" + document.getElementById("alias").value;
			
			request.onload = function () {
				if (request.status == 200) {
					document.getElementById("aliasPane").style.display = "none";
					document.getElementById("error").style.display = "none";
				} else {
					document.getElementById("error").textContent = "There was a problem setting your alias!";
					document.getElementById("error").style.display = "inherit";
				}
			}
			request.open('POST', 'http://music.lan/api/alias/add', true);
			request.setRequestHeader("Content-Type", "application/x-www-form-urlencoded");
			request.send(postData);
		}
		</script>
	</body>
</html>
