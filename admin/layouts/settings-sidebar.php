<!-- Start settings sidebar -->

<div class="col-xl-3 col-lg-4">
	<div class="card settings-card">
		<div class="card-header">
			<h6 class="mb-0">Settings</h6>
		</div>
		<div class="card-body">
			<div class="sidebars settings-sidebar">
				<div class="sidebar-inner">
					<div class="sidebar-menu p-0">
						<ul>
							<li class="submenu-open">
								<ul>
									<li class="submenu">
										<a href="javascript:void(0);" class="<?php echo ($page =='account-settings.php' || $page == 'security-settings.php' || $page == 'company-settings.php'|| $page == 'localization-settings.php' || $page =='integrations-settings.php') ? 'active subdrop' : '' ;?>">
											<i class="isax isax-setting-2 fs-18"></i>
											<span class="fs-14 fw-medium ms-2">Profile Settings</span>
											<span class="isax isax-arrow-down-1 arrow-menu ms-auto"></span>
										</a>
										<ul>
											<li><a href="account-settings.php" class="<?php echo ($page =='account-settings.php') ? 'active' : '' ;?>">Profile </a></li>
											<li><a href="security-settings.php" class="<?php echo ($page =='security-settings.php') ? 'active' : '' ;?>">Change Password</a></li>
											<li><a href="company-settings.php" class="<?php echo ($page =='company-settings.php') ? 'active' : '' ;?>">Company Profile</a></li>
											<!-- <li><a href="localization-settings.php" class="<?php echo ($page =='localization-settings.php') ? 'active' : '' ;?>">Localization</a></li> -->
										</ul>
									</li>
									
								
								</ul>
							</li>
						</ul>
					</div>
				</div>
			</div>
		</div><!-- end card body -->
	</div><!-- end card -->
</div><!-- end col -->

<!-- End settings sidebar -->