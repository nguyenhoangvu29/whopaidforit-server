
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
 				<?php echo $this->headTitle()?>
                <?php echo $this->headMeta()?>
                 <!--[if IE 7]>	  <link rel="stylesheet" type="text/css" href="style/ie7-style.css" />	<![endif]-->
                <?php echo $this->headLink()?>
                <?php echo $this->headScript()?>
   
    

      <style>
#stats {
	display: block;
}
</style>

</head>
<body>

	<div class="wrapper">


		<!-- START HEADER -->
		<div id="header">


			<!-- logo -->
			<div class="logo">
				<a href="#">
				<!-- <img src="<?php echo  $this->imgUrl;?>/logo.png" width="112" height="35"
					alt="logo" /> -->
					WHOPAIDFOR
					</a>
			</div>


			<!-- notifications -->
			<div id="notifications">
				
				<div class="clear"></div>
			</div>


			<!-- quick menu -->
			<div id="quickmenu">
				<a href="#" class="qbutton-left tips" title="Add a new post"><img
					src="<?php echo  $this->imgUrl;?>/icons/header/newpost.png" width="18" height="14"
					alt="new post" /></a> <a id="open-stats" href="#"
					class="qbutton-right tips" title="Statistics"><img
					src="<?php echo  $this->imgUrl;?>/icons/header/graph.png" width="17" height="15" alt="graph" /></a>
				<div class="clear"></div>
			</div>


			<!-- profile box -->
			<div id="profilebox">
				<a href="#" class="display"> <img src="<?php echo  $this->imgUrl;?>/simple-profile-img.jpg"
					width="33" height="33" alt="profile" /> <b>Logged in as</b> <span>Administrator</span>
				</a>

				<div class="profilemenu">
					<ul>
						<li><a href="#">Account Settings</a></li>
						<li><a href="<?php echo $this->baseUrl('/admin/admin-user/exits');?>">Logout</a></li>
					</ul>
				</div>

			</div>


			<div class="clear"></div>
		</div>
		<!-- END HEADER -->









		<!-- START MAIN -->
		<div id="main">





			<!-- START SIDEBAR -->
			<div id="sidebar">

				<!-- start searchbox -->
				<div id="searchbox">
					<div class="in">
						<form id="form1" name="form1" method="post" action="">
							<input name="textfield" type="text" class="input" id="textfield"
								onfocus="$(this).attr('class','input-hover')"
								onblur="$(this).attr('class','input')" />
						</form>
					</div>
				</div>
				<!-- end searchbox -->

				<!-- start sidemenu -->
				<div id="sidemenu">
					<ul>
            		<?php $controller = Zend_Controller_Front::getInstance()->getRequest()->getControllerName();?>
                	<li
							<?php echo $controller=='admin-user'? 'class="active"':'' ?>><a
							href="<?php echo $this->baseUrl('/admin/admin-user/index');?>"><img
								src="<?php echo  $this->imgUrl;?>/icons/sidemenu/user.png" width="16" height="16"
								alt="icon" />Users</a></li>
						<li <?php echo $controller=='admin-event'? 'class="active"':'' ?>><a
							href="<?php echo $this->baseUrl('/admin/admin-event');?>"><img
								src="<?php echo  $this->imgUrl;?>/icons/sidemenu/laptop.png" width="16" height="16"
								alt="icon" />Events</a></li>
						<li
							<?php echo $controller=='admin-expenses'? 'class="active"':'' ?>><a
							href="<?php echo $this->baseUrl('/admin/admin-expenses');?>"><img
								src="<?php echo  $this->imgUrl;?>/icons/sidemenu/copy.png" width="16" height="16"
								alt="icon" />Expenses</a></li>
						<li
							<?php echo $controller=='admin-participants'? 'class="active"':'' ?>><a
							href="<?php echo $this->baseUrl('/admin/admin-participants');?>"><img
								src="<?php echo  $this->imgUrl;?>/icons/sidemenu/lock.png" width="16" height="16"
								alt="icon" />Participants</a></li>
						<li
							<?php echo $controller=='admin-checkout'? 'class="active"':'' ?>><a
							href="<?php echo $this->baseUrl('/admin/admin-checkout');?>"><img
								src="<?php echo  $this->imgUrl;?>/icons/sidemenu/file_edit.png" width="16" height="16"
								alt="icon" />Checkout</a></li>

					</ul>
				</div>
				<!-- end sidemenu -->

			</div>
			<!-- END SIDEBAR -->


			<!-- START PAGE -->
			<div id="page">

				<!-- start stats -->
				<div id="stats">
					<?php //echo $this->layout()->user;?>
					<div class="column">
						<b>10</b> Active users
					</div>
					<div class="column">
						<b>5</b> Active events
					</div>
					<div class="column">
						<b>23</b> Active expenses
					</div>
				
					<!-- this is last column -->
					<div class="column last">
						<b class="up">$3.928</b> Total amount
					</div>
					<a href="#" title="Close Stats" class="close tips">close</a> <img
						src="<?php echo  $this->imgUrl;?>/icons/mini/stats-arrow-top.png" width="17" height="9"
						alt="arrow" class="arrow" />
				</div>
				<!-- end stats -->

				<!-- start page title -->
				<div class="page-title">
					<div class="in">
						<div class="titlebar">
							<h2>USERS</h2>
							<p>This is a quick overview of some features</p>
						</div>

						<div class="shortcuts-icons">
							
						</div>

						<div class="clear"></div>
					</div>
				</div>
				<!-- end page title -->
				<!-- START CONTENT -->
				<div class="content">
					<div class="clear"></div>