<!DOCTYPE html>
<html lang="en">
	<head>
		<meta charset="utf-8">
		<title>Stack Reputational 2012</title>
		<?php $this->load->view('partials/css'); ?>
		<?php $this->load->view('partials/js'); ?>
	</head>
	<body>			
		<div id="header">
			<div class="container">
				<div class="row">
					<div class="threecol">
						<div id="logo_box">
							<a href="<?php echo base_url(); ?>" id="home">Happenic</a>
						</div>
					</div>
					<div class="fivecol">
						
					</div>
					<div class="fourcol last">
						<div id="header_types">
							<a href="<?php echo base_url(); ?>" class="btn medium_btn orange" id="people_button">People</a>
							<a href="<?php echo site_url('main/country'); ?>" class="btn medium_btn orange" id="country_button">Country</a>
							<a href="<?php echo site_url('main/state'); ?>" class="btn medium_btn orange" id="state_button">State</a>
							<a href="<?php echo site_url('main/city'); ?>" class="btn medium_btn orange" id="city_button">City</a>
						</div>
					</div>
				</div>
			</div>			
		</div>
		<div id="content">
			<div class="container">
				<div class="row">
					<div class="threecol">
						<div id="search_box">
							
						</div>						
					</div>
					<div class="ninecol last">
						<div class="about">
							Stack Reputational was developed using
							CodeIgniter framework with REST library. The backend is served via MySQL.
							On the front end side, I used Backbonejs, CoffeeScript, LESS CSS, 1140px Grid layout.
						</div>
						
						<div class="about">
							This project is my entry to Stack Exchange API 2.0 contest.
						</div>
						
						<div class="about">
							Currently, user ranking is refreshed about once a day. If you have any questions in regards
							to the project, feel free to contact me via my blog 
							<?php echo anchor('http://ericbae.com', 'ericbae.com'); ?> or follow me on Twitter 
							<?php echo anchor('http://www.twitter.com/eric_bae', '@eric_bae'); ?>
							Thank you!
						</div>
					</div>
				</div>
			</div>		
		</div>
		<div id="footer"><?php $this->load->view('partials/footer'); ?></div>
	</body>
</html>