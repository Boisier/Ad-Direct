<html>
	<head>
		<title><?php echo $this->title; ?></title>
	</head>
	<body style="background-color: #FFF;
                 background-image: url(https://ad-direct.ca/assets/images/topography.png);
                 background-attachment: fixed;
				 margin: 0px;
				 font-family: Raleway, Helvetica, Arial, sans-serif;
				 width:800px;
				 max-width: 100%;
				 min-width: 600px;
				 font-size:20px;
				 color: #000;">
		<section style="margin: 0 auto;
						background-color: #FFF;
						padding: 70px;
						margin:25px;">
			<div style="width: 100%;
						text-align: center;
						margin-bottom: 70px;">
				<img src="http://<?php echo $_SERVER['SERVER_NAME']; ?>/assets/images/logo.png"
					 style="width: 250px">
			</div>
			<?php echo $this->content; ?>
			<div style="width: 100%;">
				<a href="https://<?php echo $_SERVER['SERVER_NAME']; ?>/"
				   style="display:block;
						  width: 340px;
						  height: 57px;
						  margin: 70px auto 0;
						  color: #FFF;
						  text-decoration: none;">
					<div style="width:100%;
								height: 100%;
								line-height: 57px;
								text-align: center;
								background-color:#17ABE2;
								font-weight:lighter;">
						<?php echo \__("accessAdDirect"); ?>
					</div>
				</a>
			</div>
		</section>
	</body>
</html>