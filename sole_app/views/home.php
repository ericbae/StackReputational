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
						<div id="header_search">
							<input type="text" id="display_name" placeholder="Type username and press enter!" />
						</div>
					</div>
					<div class="fourcol last">
						<div id="header_types">
							<!--<a href="<?php echo base_url(); ?>" class="btn medium_btn green" id="people_button">People</a>
							<a href="<?php echo site_url('main/country'); ?>" class="btn medium_btn orange" id="country_button">Country</a>
							<a href="<?php echo site_url('main/state'); ?>" class="btn medium_btn orange" id="state_button">State</a>
							<a href="<?php echo site_url('main/city'); ?>" class="btn medium_btn orange" id="city_button">City</a>-->
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
							<div class="search" id="location">
								<div class="title">Location</div>
								<div class="options">
									<div class="option" id="country">
										<div class="label">Country</div>
										<select name="country" id="country"></select>
									</div>
									<div class="option" id="state">
										<div class="label">State</div>
										<select name="state" id="state"></select>
									</div>
									<div class="option" id="city">
										<div class="label">City</div>
										<select name="city" id="city"></select>
									</div>
								</div>
							</div>
						
							<div class="search" id="location">
								<div class="title">Age</div>
								<div class="options">
									<div class="option">
										<input type="text" class="age" id="age_from" placeholder="From" />
										<input type="text" class="age no_margin" id="age_to" placeholder="To" />
										<button class="btn blue small_btn age_search">Search</button>
									</div>
									<div class="option option_space">
										<div class="label_full">Or try pre-defined age groups</div>
										<div class="age_group" id="group0" data-from="" data-to="">All ages</div>
										<div class="age_group" id="group1" data-from="1" data-to="10">1 to 10</div>
										<div class="age_group" id="group2" data-from="11" data-to="20">11 to 20</div>
										<div class="age_group" id="group3" data-from="21" data-to="30">21 to 30</div>
										<div class="age_group" id="group4" data-from="31" data-to="40">31 to 40</div>
										<div class="age_group" id="group5" data-from="41" data-to="50">41 to 50</div>
										<div class="age_group" id="group6" data-from="51" data-to="60">51 to 60</div>
										<div class="age_group" id="group7" data-from="61" data-to="70">61 to 70</div>
										<div class="age_group" id="group8" data-from="71" data-to="80">71 to 80</div>
										<div class="age_group" id="group9" data-from="81" data-to="90">81 to 90</div>
										<div class="age_group" id="group10" data-from="91" data-to="100">91 to 100</div>
										<div class="age_group" id="group11" data-from="-1" data-to="-1">Unknown</div>
									</div>
								</div>
							</div>
							
							<div class="search" id="location">
								<div class="title">Reputation gain/loss</div>
								<div class="options">
									<div class="option">
										<div class="repute" id="reputation">all time</div>
										<div class="repute" id="reputation_change_day">today</div>
										<div class="repute" id="reputation_change_week">week</div>
										<div class="repute" id="reputation_change_month">month</div>
										<div class="repute" id="reputation_change_quarter">quarter</div>
										<div class="repute" id="reputation_change_year">year</div>
									</div>
								</div>
							</div>
							
							<div class="search" id="tag">
								<div class="title">Topics</div>
								<div class="options">
									<div class="option">
										<div class="tag" id="">All</div>
								<?php
									foreach($tags as $tag) {
								?>
										<div class="tag" id="<?php echo $tag->tag; ?>"><?php echo $tag->tag; ?></div>
								<?php		
									}
								?>
									</div>
								</div>
							</div>
														
							<div class="notice">
								Welcome to <b>Stack Reputational</b>. This site allows you to view the Stack Overflow leaderboard
								by users' locations (country, state, city), age groups and many other parameters. Define your
								search criteria above and click on each user to view their detail.
							</div>
							<div class="notice">
								<b>Click on each user to view more about each user!</b>
							</div>
						</div>						
					</div>
					<div class="ninecol last">
						<div id="param_box">
							USER RANKING : 
							<span class="size"></span>
							<span class="country param"></span>
							<span class="state param"></span>
							<span class="city param"></span>
							<span class="age param"></span>
							<span class="tag param"></span>
							<span class="order_by param"></span>
						</div>
						<div id="user_box"></div>
						<div id="pagination"></div>
					</div>
				</div>
			</div>		
		</div>
		<div id="footer"><?php $this->load->view('partials/footer'); ?></div>
		<div id="main_loading">Loading users..<img src="images/main_loader.gif" /></div>
		<script type="text/coffeescript" src="js/sole/Sole.coffee"></script>
		
		<!-- template for each user -->
		<script id="user_hb" type="text/x-handlebars-template">
			<div class="user">
				<div class="rank">{{rank}}</div>
				<img class="profile" src="{{profile_image}}" />
				<div class="profile_box">
					<div class="name">
						{{display_name}}
						<span class="reputation">{{format_num main_rep}}</span>
					</div>
					<div class="info">
						{{#if age}} 
							{{age}} yrs old
						{{else}}
							age unknown 
						{{/if}} <br/>
						from 
						{{#if formatted_address}} 
							{{formatted_address}}
						{{else}}
							unknown 
						{{/if}}<br/>
						member since {{convert_time creation_date}}
					</div>
					<div class="more_box">
						<div class="more_info">
							<div class="title">Reputation gain/loss</div>
							<div class="sub_info repute">today {{format_num reputation_change_day}}</div>
							<div class="sub_info repute">week {{format_num reputation_change_week}}</div>
							<div class="sub_info repute">month {{format_num reputation_change_month}}</div>
							<div class="sub_info repute">quarter {{format_num reputation_change_quarter}}</div>
							<div class="sub_info repute">year {{format_num reputation_change_year}}</div>
						</div>
						<div class="more_info">
							<div class="title">More on {{display_name}}</div>
							<div class="sub_info other"><a href="{{link}}" target="_blank">Stack profile</a></div>
							{{#if website_url}}<div class="sub_info other"><a href="{{website_url}}" target="_blank">User blog</a></div>{{/if}}
							{{#if accept_rate}}<div class="sub_info other">Accept rate {{accept_rate}}%</div>{{/if}}
						</div>
					{{#if tags}}
						<div class="more_info">
							<div class="title">Favourite topics</div>
							<div class="sub_info tag">{{tags}}</div>
						</div>
					{{/if}}						
						<div class="more_info">
							<div class="title">{{display_name}}'s claim to fame <img class="loading" src="images/ajax-loader.gif" width="16" height="16" /></div>
							<div class="sub_info fame overall"></div>
							<div class="sub_info fame country"></div>
							<div class="sub_info fame state"></div>
							<div class="sub_info fame city"></div>
							<div class="sub_info fame age"></div>
							<div class="sub_info fame age_group"></div>
						</div>
					</div>
				</div>
			</div>
		</script>
	</body>
</html>