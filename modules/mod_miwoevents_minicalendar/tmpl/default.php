<?php
/**
 * @package		MiwoEvents
 * @copyright	2009-2014 Miwisoft LLC, miwisoft.com
 * @license		GNU/GPL http://www.gnu.org/copyleft/gpl.html
 */
# No Permission
defined('MIWI') or die('Restricted access');
?>

<script language="javascript" type="text/javascript" >
	function navminicalLoaded(elem){
		var myspan = document.getElementById("miwoevents_minicalendar");
		var modbody = myspan.parentNode;
		
		modbody.innerHTML = elem.innerHTML;
	}
	
	function miwoeventsNavigation(link){
		var mybody = document.getElementsByTagName('body')[0];
		
		if (!document.getElementById('calmininav')){
			iframe = document.createElement('iframe');
			iframe.setAttribute("name","calmininav");
			iframe.setAttribute("id","calmininav");
			iframe.style.display = "none";
		}
		
		mybody.appendChild(iframe);
		
		iframe.setAttribute("src", link);
	}
</script>

<div style="margin: 0px; padding: 0px; width:100%">
	<span style="display: none;" id="miwoevents_minicalendar"></span>
	<div id="extcal_minical997" class="extcal_minical">
		<table class="extcal_minical" style="background-color: #<?php echo $params->get('background_color', '214865') ?>" cellspacing="1" cellpadding="0" border="0" align="center" width="100%">
			<tr>
				<td valign="top">
					<?php
						// calculate for next and previous year
						$previousYear = $year - 1;
						$nextYear = $year + 1;
						$link_pre_year = MUri::root()."index.php?option=com_miwoevents&view=calendar&task=changeMinicalendar&month=$month&year=$previousYear&module_title=$module_title&format=raw&tmpl=component";
						$link_nex_year = MUri::root()."index.php?option=com_miwoevents&view=calendar&task=changeMinicalendar&month=$month&year=$nextYear&module_title=$module_title&format=raw&tmpl=component";
						
						//Calculate next and previous month, year
						if ($month == 12) {
							$nextMonth = 1;
							$nextYear = $year + 1;
							$previousMonth = 11;
							$previousYear = $year;
						}
						elseif ($month == 1) {
							$nextMonth = 2;
							$nextYear = $year;
							$previousMonth = 12;
							$previousYear = $year - 1;
						}
						else {
							$nextMonth = $month + 1;
							$nextYear = $year ;
							$previousMonth = $month - 1;
							$previousYear = $year;
						}
						
						$link_pre_month = MUri::root()."index.php?option=com_miwoevents&view=calendar&task=changeMinicalendar&month=$previousMonth&year=$previousYear&module_title=$module_title&format=raw&tmpl=component";
						$link_nex_month = MUri::root()."index.php?option=com_miwoevents&view=calendar&task=changeMinicalendar&month=$nextMonth&year=$nextYear&module_title=$module_title&format=raw&tmpl=component";

                        $Itemid = MiwoEvents::get('utility')->getItemid(array('view' => 'calendar', 'month' => $month), null, true);
						$link = MRoute::_('index.php?option=com_miwoevents&view=calendar&month='.$month.$Itemid);
					?>
					<table class="extcal_navbar" border="0" width="100%">
						<tr>			
							<td>
								<div class="mod_miwoevents_minicalendar_link"><a href="javascript:miwoeventsNavigation('<?php echo $link_pre_year;?>');">&laquo;</a></div>
							</td>
							<td>
								<div class="mod_miwoevents_minicalendar_link"><a href="javascript:miwoeventsNavigation('<?php echo $link_pre_month;?>');">&lt;</a></div>
							</td>		
							<td nowrap="nowrap" height="18" align="center" width="98%" valign="middle" class="extcal_month_label">
								<a class="mod_miwoevents_minicalendar_link" href="<?php echo $link;?>">
									<?php echo $listmonth[$month-1]; ?>&nbsp;
								</a>
								<a class="mod_miwoevents_minicalendar_link" href="<?php echo $link;?>">
									<?php echo $year; ?>
								</a>
							</td>		
							<td>
								<div class="mod_miwoevents_minicalendar_link"><a href="javascript:miwoeventsNavigation('<?php echo $link_nex_month;?>');">&gt;</a></div>
							</td>
							<td>
								<div class="mod_miwoevents_minicalendar_link"><a href="javascript:miwoeventsNavigation('<?php echo $link_nex_year;?>');">&raquo;</a></div>
							</td>
						</tr>
					</table>
					<table class="mod_miwoevents_mincalendar_table" cellpadding="0" cellspacing="0" border="0"  width="100%">
						<tbody>
							<tr class="mod_miwoevents_mincalendar_dayname">
								<?php
								foreach ($data["daynames"] as $dayname) { ?>
									<td class="mod_miwoevents_mincalendar_td_dayname">
										<?php echo $dayname;?>
									</td>
								<?php
								}
								?>
							</tr>
							
							<?php
							$datacount = count($data["dates"]);
							$dn = 0;
							for ($w = 0; $w <6 && $dn < $datacount; $w++){
							?>
							<tr>
								<?php
								for ($d = 0; $d < 7 && $dn < $datacount; $d++){
									$currentDay = $data["dates"][$dn];
									
									switch ($currentDay["monthType"]){
										case "prior":
										case "following":
											?>
											<td>
											&nbsp;
											</td>
											<?php
										break;
										case "current":
											if ($currentDay["today"]){
												$class_today = "mod_miwoevents_mincalendar_today";	              				
											}else{
												$class_today = "mod_miwoevents_mincalendar_not_today";
											}
											$numberEvents = count($currentDay["events"]) ;
											if ($numberEvents > 0){
												$class_event = "mod_miwoevents_mincalendar_event";
												$val = $currentDay["events"][count($currentDay["events"])-1];
											}else{
												$class_event = "mod_miwoevents_mincalendar_no_event";              				
											}
											
											$dayos = $currentDay['d'];
											if ($currentDay['d'] < 10) $dayos = "0".$currentDay['d'];

                                            $Itemid = MiwoEvents::get('utility')->getItemid(array('view' => 'calendar', 'layout' => 'daily'), null, true);
											$link = MRoute::_("index.php?option=com_miwoevents&view=calendar&layout=daily&day=".$year."-".$month."-".$dayos . $Itemid);
											?>
											<td class="<?php echo $class_event.' '.$class_today?>">
												<?php 
												if ($d == 0){
													$class = "sunday";
												}
												else if($d == 6){
													$class = "saturday";
												}
												else{
													$class = "nomarl";
												}
												
												if ($class_event == 'mod_miwoevents_mincalendar_event'){		                    		
												?>	
													<a class="miwi_minical_link" href="<?php echo $link; ?>" title="<?php echo  ($numberEvents > 1 ? $numberEvents.MText::_('EB_EVENTS') :  $val->title) ; ?>">
														<span class="<?php echo $class?>"><?php echo $currentDay['d'];?></span> 
													</a>
												<?php 
													}
													else {
												?>
													<span class="<?php echo $class?>"><?php echo $currentDay['d'];?></span> 
												<?php 
												}
											?>
											</td>
											<?php
										break;
									}
									
									$dn++;
								}
							?>
							</tr>
							<?php
							}
							?>	
						</tbody>
					</table>
				</td>
			</tr>
		</table>
	</div>
</div>