<?php
/**
 * @package		MiwoEvents
 * @copyright	2009-2014 Miwisoft LLC, miwisoft.com
 * @license		GNU/GPL http://www.gnu.org/copyleft/gpl.html
 */
# No Permission
defined('MIWI') or die ;

if (MFactory::getUri()->isSSL() == true) { $http = "https://"; } else { $http = "http://"; }
$getDirectionLink = $http.'maps.google.com/maps?f=d&daddr='.$this->item->coordinates.'('.addslashes($this->item->address).')';

$mapText = "<div style='width:300px;'>";
$mapText .= "<strong>".addslashes($this->item->title)."</strong><br />";
$mapText .= addslashes($this->item->address);
$mapText .= "<br /><a href='{$getDirectionLink}' target='_blank'>".MText::_('COM_MIWOEVENTS_GET_DIRECTION')."</a>";
$mapText .= "</div>";
?>
<div id="map_canvas" style="height:300px; width:100%;"></div>

<script src="<?php echo $http; ?>maps.google.com/maps/api/js?sensor=false" type="text/javascript"></script>
<script type="text/javascript">
function initialize() {
	geocoder = new google.maps.Geocoder();
	var mapDiv = document.getElementById('map_canvas');
	var openInfoWindow = true;

	map = new google.maps.Map(mapDiv, {
		center: new google.maps.LatLng(<?php echo $this->item->coordinates; ?>),
		zoom: 11,
		mapTypeId: google.maps.MapTypeId.ROADMAP,
		scrollwheel: false,
		streetViewControl: false
		
	});

	marker = new google.maps.Marker({
		map: map,
		title: "<?php echo $this->item->title; ?>",
		position: new google.maps.LatLng(<?php echo $this->item->coordinates; ?>),
		draggable: false
	});

	var infowindow = new google.maps.InfoWindow({
		content: "<?php echo $mapText; ?>",
		position:new google.maps.LatLng(<?php echo $this->item->coordinates; ?>)
	});


	<?php
	if ($this->MiwoeventsConfig->show_map_info == 1) { ?>

	infowindow.open(map,marker);
	openInfoWindow = false;
	
	google.maps.event.addListener(marker, 'click', function() {
		if (openInfoWindow == true ) {
			infowindow.open(map,marker);
			openInfoWindow = false;
		} else {
			infowindow.close();
			openInfoWindow = true;
		}
	});
		
	<?php } else { ?>
	
	google.maps.event.addListener(marker, 'click', function() {
		if (openInfoWindow == true ) {
			infowindow.open(map,marker);
			openInfoWindow = false;
		} else {
			infowindow.close();
			openInfoWindow = true;
		}
	});
	
	<?php } ?>
}
initialize();
</script>