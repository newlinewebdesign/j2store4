<?php
/**
 * @package J2Store
 * @copyright Copyright (c)2014-17 Ramesh Elamathi / J2Store.org
 * @license GNU GPL v3 or later
 */

defined('_JEXEC') or die;
use Joomla\CMS\Factory;
use Joomla\CMS\Language\Text;
use Joomla\Database\DatabaseDriver;
use Joomla\CMS\Uri\Uri;

if (!defined('F0F_INCLUDED'))
{
	include_once JPATH_LIBRARIES . '/f0f/include.php';
}

$platform = J2Store::platform();
$app = $platform->application();
$wa  = Joomla\CMS\Factory::getApplication()->getDocument()->getWebAssetManager();
$waState = $wa->getManagerState();
if ($wa->assetExists('style', 'fontawesome')) {
	if($waState['activeAssets']['style']['j2store-font-awesome-css']){
		$wa->disableStyle('j2store-font-awesome-css');
	}
} else {
	J2Store::platform()->addStyle('j2store-font-awesome-css','/media/j2store/css/font-awesome.min.css');
}
$script="document.addEventListener('DOMContentLoaded',function(){const e=document.querySelectorAll('.submenu-dropdown-toggle'),t=window.screen.width;t<992&&e.forEach(e=>{e.addEventListener('click',function(t){new bootstrap.Collapse('#navbarScroll',{toggle:!0})})})});";
$platform->addInlineScript($script);

$menus = array (
	array (
		'name' => 'Dashboard',
		'icon' => 'fa fa-th-large',
		'active' => 1
	),
	array (
		'name' => Text::_ ( 'COM_J2STORE_MAINMENU_CATALOG' ),
		'icon' => 'fa fa-tags',
		'submenu' => array (
			'products' => 'fa fa-tags',
			'inventories' => 'fa fa-database',
			'options' => 'fa fa-list-ol',
			'vendors' => 'fa fa-male',
			'manufacturers' => 'fa fa-user',
			'filtergroups' => 'fa fa-filter'

		)
	),
	array (
		'name' => Text::_ ( 'COM_J2STORE_MAINMENU_SALES' ),
		'icon' => 'fa fa-money',
		'submenu' => array (
			'orders' => 'fa fa-list-alt',
			'customers' => 'fa fa-users',
			'coupons' => 'fa fa-scissors',
			'vouchers' => 'fa fa-gift'
		)
	),
	array (
		'name' => Text::_ ( 'COM_J2STORE_MAINMENU_LOCALISATION' ),
		'icon' => 'fa fa-globe fa-lg',
		'submenu' => array (
			'countries' => 'fa fa-globe',
			'zones' => 'fa fa-flag',
			'geozones' => 'fa fa-pie-chart',
			'taxrates' => 'fa fa-calculator',
			'taxprofiles' => 'fa fa-sitemap',
			'lengths' => 'fa fa-arrows-v',
			'weights' => 'fa fa-arrows-h',
			'orderstatuses' => 'fa fa-check-square'
		)
	),
	array (
		'name' => Text::_ ( 'COM_J2STORE_MAINMENU_DESIGN' ),
		'icon' => 'fa fa-paint-brush',
		'submenu' => array (
			'emailtemplates' => 'fa fa-envelope',
			'invoicetemplates' => 'fa fa-print'
		)
	),

	array (
		'name' => Text::_ ( 'COM_J2STORE_MAINMENU_SETUP' ),
		'icon' => 'fa fa-cogs',
		'submenu' => array (
			'configuration' => 'fa fa-cogs',
			'currencies' => 'fa fa-dollar',
			'payments' => 'fa fa-credit-card',
			'shippings' => 'fa fa-truck',
			'shippingtroubles' => 'fa fa-bug',
			'customfields' => 'fa fa-th-list',
//						'eupdates' => 'fa fa-refresh',
		)
	),
	array (
		'name' => 'Apps',
		'icon' => 'fa fa-wrench',
		'active' => 0
	),
	array (
		'name' => 'Reporting',
		'icon' => 'fa fa-signal',
		'submenu' => array (
			'Reports' => 'fa fa-signal'
		)
	)
);
$j2StorePlugin = \J2Store::plugin();
$j2StorePlugin->event('AddDashboardMenuInJ2Store',array(&$menus));
// Get installed version
$db    = Factory::getContainer()->get(DatabaseDriver::class);
$query = $db->getQuery(true);
$query->select($db->quoteName('manifest_cache'))->from($db->quoteName('#__extensions'))->where($db->quoteName('element').' = '.$db->quote('com_j2store'));
$query->where('type ='.$db->q('component'));
$db->setQuery($query);
$row = json_decode($db->loadResult());

//check updates if logged in as administrator
$user = Factory::getUser();
$fof_helper = J2Store::fof();
$isroot = $user->authorise('core.admin');
$updateInfo = array();
if($isroot) {
//refresh the update sites first
	$fof_helper->getModel('Updates', 'J2StoreModel')->refreshUpdateSite();
//now get update
	$updateInfo =  $fof_helper->getModel('Updates', 'J2StoreModel')->getUpdates();
}
$view = $app->input->getString('view','cpanels');
?>
    <div id="j2store-navbar">
		<?php  if (version_compare(JVERSION, '3.99.99', 'ge')) : ?>
            <div class="d-none d-lg-flex align-items-center mb-3">
                <div class="j2store-social-share">
                    <a class="btn btn-primary px-2" href="https://www.facebook.com/j2store" onclick="return ! window.open(this.href);">
                        <i class="fa-brands fa-facebook-f fa-fw"></i>
                    </a>
                    <a class="btn btn-primary px-2" href="https://twitter.com/j2store_joomla" onclick="return ! window.open(this.href);">
                        <i class="fa-brands fa-twitter fa-fw"></i>
                    </a>
                </div>
                <h3 class="text-black mb-0 ms-2 fw-bolder">v <?php echo isset($row->version) ? $row->version : J2STORE_VERSION; ?>
					<?php if(J2Store::isPro() == 1): ?>
						<?php echo 'PRO'; ?>
					<?php else: ?>
						<?php echo 'CORE'; ?>
					<?php endif; ?>
                </h3>
            </div>
            <nav class="navbar navbar-expand-lg bg-primary" role="navigation" data-bs-theme="dark">
                <div class="container-fluid">
                    <div class="navbar-brand d-flex align-items-center">
                        <img src="<?php echo Uri::root();?>media/j2store/images/dashboard-logo.png" class="img-circle" alt="j2store logo" />
                        <h5 class="mb-0 ms-2 fw-bolder d-block d-lg-none text-white-50">v <?php echo isset($row->version) ? $row->version : J2STORE_VERSION; ?>
							<?php if(J2Store::isPro() == 1): ?>
								<?php echo 'PRO'; ?>
							<?php else: ?>
								<?php echo 'CORE'; ?>
							<?php endif; ?>
                        </h5>
                    </div>

                    <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarScroll" aria-controls="navbarScroll" aria-expanded="false" aria-label="Toggle Navigation">
                        <span class="navbar-toggler-icon"></span>
                    </button>
                    <div class="collapse navbar-collapse" id="navbarScroll">
                        <ul class="navbar-nav me-auto justify-content-center mb-2 mb-lg-0">
							<?php $view = $app->input->getString('view');
							foreach($menus as $key => $value): ?>
								<?php if(isset($value['submenu']) && count($value['submenu'])):?>
                                    <li class="nav-item dropdown">
                                        <a id="dropdown<?php echo $key;?>" class="nav-link dropdown-toggle submenu-dropdown-toggle" role="button" data-bs-toggle="dropdown" aria-expanded="false">
                                            <i class="<?php echo isset($value['icon']) ? $value['icon'] : '';?>"></i>
                                            <span class="submenu-title"><?php echo $value['name'];?></span>
                                        </a>
                                        <ul class="dropdown-menu">
											<?php foreach($value['submenu'] as $sub_key => $sub_value):?>
												<?php
												if(is_array ( $sub_value )): ?>
													<?php $class =  '';
													$appTask = $app->input->get('appTask','');
													if($view == 'apps' && $appTask == $sub_key){
														$class =  'active';
														$collapse = 'in';
													}
													$link_url = isset( $sub_value['link'] ) ? $sub_value['link']: 'index.php?option=com_j2store&view='.strtolower($sub_key);
													$sub_menu_span_class = isset( $sub_value['icon'] ) ? $sub_value['icon']:'';
													?>
												<?php else: ?>
													<?php
													$class =  '';
													if($view == $sub_key){
														$class =  'active';
														$collapse = 'in';
													}
													$link_url = 'index.php?option=com_j2store&view='.strtolower($sub_key);
													$sub_menu_span_class = isset( $sub_value ) ? $sub_value:'';
													?>
												<?php endif;?>
                                                <li>
                                                    <a class="dropdown-item <?php echo $class ?>" href="<?php echo $link_url;?>">
                                                        <i class="<?php echo $sub_menu_span_class;?>"></i>
                                                        <span><?php echo Text::_('COM_J2STORE_TITLE_'.strtoupper($sub_key));?></span>
                                                    </a>
                                                </li>
											<?php endforeach; ?>
                                        </ul>
                                    </li>
								<?php else:
									$active_class ='';
									if(isset($value['active']) && $value['active'] && $view =='cpanels'){
										$active_class ='active';
									}
									?>
                                    <li class="nav-item <?php echo $active_class;?>">
										<?php if(isset($value['link']) && $value['link'] != ''):?>
                                        <a class="nav-link" aria-current="page" href="<?php echo $value['link'];?>">
											<?php else :
											if($value['name']=='Dashboard'):?>
                                            <a class="nav-link <?php echo $active_class;?>" aria-current="page"  href="<?php echo 'index.php?option=com_j2store&view=cpanels';?>">
												<?php elseif($value['name']=='Apps'): ?>
                                                <a class="nav-link <?php echo $active_class;?>" aria-current="page"  href="<?php echo 'index.php?option=com_j2store&view=apps';?>">
													<?php elseif($value['name']=='AppStore'): ?>
                                                    <a class="nav-link <?php echo $active_class;?>" aria-current="page"  href="<?php echo 'index.php?option=com_j2store&view=appstores';?>">
														<?php else:?>
                                                        <a class="nav-link <?php echo $active_class;?>" aria-current="page"  href="javascript:void(0);">
															<?php endif;?>
															<?php endif;?>
                                                            <span class=""><i class="<?php echo isset($value['icon']) ? $value['icon'] : '';?> icon"></i><?php echo Text::_('COM_J2STORE_MAINMENU_'.strtoupper($value['name']));?></span>
                                                        </a>
                                    </li>
								<?php endif; ?>
							<?php endforeach; ?>
                        </ul>

                    </div>
                </div>
            </nav>
		<?php else :?>
            <div class="navbar  navbar-inverse  navbar-collapse">
                <div class="navbar-inner " >
                    <img
                            src="<?php echo Uri::root();?>media/j2store/images/dashboard-logo.png"
                            class="img-circle" alt="j2store logo" />
                    <div class="btn-group">
                        <div class="social-share">
                            <a class="btn btn-primary"
                               href="https://www.facebook.com/j2store" onclick="return ! window.open(this.href);"> <i
                                        class="fa fa-facebook"></i>
                            </a> <a class="btn btn-primary"
                                    href="https://twitter.com/j2store_joomla" onclick="return ! window.open(this.href);"> <i
                                        class="fa fa-twitter"></i>
                            </a>
                        </div>
                    </div>
                    <div class="btn-group ">
                        <h3>v <?php echo isset($row->version) ? $row->version : J2STORE_VERSION; ?>
							<?php if(J2Store::isPro() == 1): ?>
								<?php echo 'PRO'; ?>
							<?php else: ?>
								<?php echo 'CORE'; ?>
							<?php endif; ?>
                        </h3>
                    </div>
                    <a href="#" class="btn btn-navbar collapsed" data-toggle="collapse" data-target="#navbarSupportedContent">
                        <span class="icon-bar"></span>
                        <span class="icon-bar"></span>
                        <span class="icon-bar"></span>
                    </a>
                    <div class="navbarContent collapse" id="navbarSupportedContent">
                        <div class="dropdown">
                            <ul id="sidemenu" class="menu-content nav navbar-nav mr-auto justify-content-center">
								<?php
								foreach($menus as $key => $value):
									// $emptyClass = empty($value['active']) ? 'parent' : '';
									?>
									<?php if(isset($value['submenu']) && count($value['submenu'])):?>
                                    <li class="j2store_inn_nav dropdown"><a class="dropdown-toggle" data-toggle="dropdown" href="" data-target="#dropdown-<?php echo str_replace(" ","-", $value['name']);?>" > <i
                                                    class="<?php echo isset($value['icon']) ? $value['icon'] : '';?>"></i>
                                            <span class="submenu-title"><?php echo $value['name'];?></span>
                                            <span class=""> <i class="fa fa-angle-down"></i>
				</span>
                                        </a>
										<?php $collapse = 'out';?>
                                        <ul class="dropdown-menu submenu-list navbar-nav mr-auto "
                                            id="dropdown-<?php echo str_replace(" ", "-", $value['name']);?>">
											<?php foreach($value['submenu'] as $key => $value):?>
												<?php
												if(is_array ( $value )): ?>
													<?php $class =  '';
													$appTask = $app->input->get('appTask','');
													if($view == 'apps' && $appTask == $key){
														$class =  'active';
														$collapse = 'in';
													}
													$link_url = isset( $value['link'] ) ? $value['link']: 'index.php?option=com_j2store&view='.strtolower($key);
													$sub_menu_span_class = isset( $value['icon'] ) ? $value['icon']:'';
													?>
												<?php else: ?>
													<?php
													$class =  '';
													if($view == $key){
														$class =  'active';
														$collapse = 'in';
													}
													$link_url = 'index.php?option=com_j2store&view='.strtolower($key);
													$sub_menu_span_class = isset( $value ) ? $value:'';
													?>
												<?php endif;?>
                                                <li class="<?php echo $class?> "><a
                                                            href="<?php echo $link_url;?>">
							<span class="<?php echo $sub_menu_span_class;?>"> <span><?php echo Text::_('COM_J2STORE_TITLE_'.strtoupper($key));?></span>
						</span>
                                                    </a></li>
											<?php endforeach;?>
                                        </ul></li>
								<?php else:?>
									<?php
									$active_class ='';
									if(isset($value['active']) && $value['active'] && $view =='cpanels'){
										$active_class ='active';
									}
									?>
                                    <li class=" <?php echo $active_class; ?> content"><i
                                                class="<?php echo isset($value['icon']) ? $value['icon'] : '';?>"></i>
										<?php
										if(isset($value['link']) && $value['link'] != ''):
										?>
                                        <a href="<?php echo $value['link'];?>">
											<?php
											else :
											if($value['name']=='Dashboard'):?>
                                            <a   href="<?php echo 'index.php?option=com_j2store&view=cpanels';?>">
												<?php elseif($value['name']=='Apps'): ?>
                                                <a  href="<?php echo 'index.php?option=com_j2store&view=apps';?>">
													<?php elseif($value['name']=='AppStore'): ?>
                                                    <a  href="<?php echo 'index.php?option=com_j2store&view=appstores';?>">
														<?php else:?>
                                                        <a href="javascript:void(0);">
															<?php endif;?>
															<?php endif;?>

															<?php echo Text::_('COM_J2STORE_MAINMENU_'.strtoupper($value['name']));?>
                                                        </a></li>
								<?php endif;?>
								<?php endforeach;?>
                            </ul>
                        </div>
                    </div>
                </div>
            </div>
		<?php endif; ?>

    </div>
<?php
$row_class = 'row';
$col_class = 'col-md-';
if (version_compare(JVERSION, '3.99.99', 'lt')) {
	$row_class = 'row-fluid';
	$col_class = 'span';
}
$platform->addInlineScript('
    document.addEventListener("DOMContentLoaded", function() {
        document.querySelector("#j-main-container").className = "' . $col_class . '12";
        document.querySelector("#j-sidebar-container").className = "' . $col_class . '12";
    });
');

