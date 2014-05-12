<?php
/**
 * @package		MiwoEvents
 * @copyright	2009-2014 Miwisoft LLC, miwisoft.com
 * @license		GNU/GPL http://www.gnu.org/copyleft/gpl.html
 */
# No Permission
defined('MIWI') or die ;

$page_title = $this->params->get('page_title', '');
if (($this->params->get('show_page_heading', '0') == '1') && !empty($page_title)) { $page_title; }

$events = count($this->items);
?>

<script type="text/javascript">
	var map_view;
	var geocoder_view;

	function getMiwoeventsMap() {
        var miwoevents_map = document.getElementById('miwoevents_map');

		geocoder_view = new google.maps.Geocoder();

		map_view = new google.maps.Map(miwoevents_map, {
		    center: new google.maps.LatLng(<?php echo $this->params->get('map_center', '40.992954,29.042092'); ?>),
		    zoom: <?php echo $this->params->get('map_zoom', 10); ?>,
		    mapTypeId: google.maps.MapTypeId.ROADMAP
		});

		var latlngbounds_view = new google.maps.LatLngBounds();

		<?php
        if (!empty($this->items)) {
            foreach ($this->items as $item) {
                if (empty($item)) {
                    continue;
                }

                if (empty($item->coordinates) and empty($item->address)) {
                    continue;
                }

                if (!empty($item->coordinates)) {
                ?>
                    var coordinates_view = '<?php echo $item->coordinates; ?>';
                    coordinates_view = coordinates_view.split(',');

                    var lat = parseFloat(coordinates_view[0]);
                    var lon = parseFloat(coordinates_view[1]);
                    var marker_view<?php echo $item->id; ?> = getMarkerView(new google.maps.LatLng(lat, lon));

                    latlngbounds_view.extend(new google.maps.LatLng(lat, lon));
                <?php
                }
                else {
                ?>
                    var marker_view<?php echo $item->id; ?> = getMarkerView();
                    getAddressView('<?php echo $item->address; ?>', marker_view<?php echo $item->id; ?>);
                <?php } ?>

                if (marker_view<?php echo $item->id; ?> != false) {
					var content<?php echo $item->id; ?> = '<a target="_blank" href="<?php echo MRoute::_('index.php?option=com_miwoevents&view=event&event_id='.$item->id.$this->Itemid); ?>"><?php echo addslashes($item->title); ?></a><br /><?php echo MText::_('COM_MIWOEVENTS_EVENT_DATE', true); ?>: <?php echo MHtml::_('date', $item->event_date, $this->MiwoeventsConfig->event_date_format, null); ?><br /><?php echo MText::_('COM_MIWOEVENTS_EVENT_END_DATE', true); ?>: <?php echo MHtml::_('date', $item->event_end_date, $this->MiwoeventsConfig->event_date_format, null); ?><br /><?php echo MText::_('COM_MIWOEVENTS_LOCATION', true); ?>: <a target="_blank" href="<?php echo MRoute::_('index.php?option=com_miwoevents&view=location&location_id='.$item->location_id.$this->Itemid); ?>"><?php echo addslashes($item->location_title); ?></a> ';

                    var info_window<?php echo $item->id; ?> = new google.maps.InfoWindow({
                        content: content<?php echo $item->id; ?>
                    });

                    google.maps.event.addListener(marker_view<?php echo $item->id; ?>, 'click', function() {
                        info_window<?php echo $item->id; ?>.open(map_view, marker_view<?php echo $item->id; ?>);
                    });

                    google.maps.event.addListener(map_view, 'click', function() {
                        info_window<?php echo $item->id; ?>.close();
                    });
                }
		    <?php
            }
		}
        ?>

		<?php if ($events >= 1) { ?>
            map_view.setCenter(latlngbounds_view.getCenter());

		    <?php if ($events != 1) { ?>
                map_view.fitBounds(latlngbounds_view);
            <?php } ?>
		<?php } ?>

		google.maps.event.addListener(map_view, 'click', function(e) {
            panToCenter(e.latLng, map_view);
        });

	}

	function panToCenter(position, map) {
		var currentzoom = map.getZoom();

		if (currentzoom == 2) {
			map.panTo(position);
			map.setZoom(5);
		}
	}

	function getMarkerView(point) {
		new_marker = new google.maps.Marker({
		  map: map_view,
		  position: point,
		  draggable: false
		});

		return new_marker;
	}

	function getAddressView(address, themarker) {
        geocoder_view.geocode( { 'address': address}, function(results, status) {
			if (status == google.maps.GeocoderStatus.OK) {
				var lat = parseFloat(results[0].geometry.location.lat().toFixed(7));
				var lon = parseFloat(results[0].geometry.location.lng().toFixed(7));
				themarker.setPosition(new google.maps.LatLng(lat,lon));
			}
		});
	}

	google.maps.event.addDomListener(window, 'load', getMiwoeventsMap);
</script>

<div class="miwoevents_box">
	<div class="miwoevents_box_heading">
		<h1 class="miwoevents_box_h1"><?php echo $page_title; ?></h1>
	</div>
	
	<div class="miwoevents_box_content">
	<!-- content -->
		<form method="post" name="adminForm" id="adminForm" action="<?php echo MRoute::_('index.php?option=com_miwoevents&view=map'.$this->Itemid); ?>">
            <div id="miwoevents_map" style="width: 100%; height: 400px"></div>
		
		    <input type="hidden" name="option" value="com_miwoevents" />
		    <input type="hidden" name="view" value="locations" />
		    <input type="hidden" name="task" value="" />
		    <input type="hidden" name="filter_order" value="<?php echo $this->lists['order']; ?>" />
		    <input type="hidden" name="filter_order_Dir" value="<?php echo $this->lists['order_Dir']; ?>" />
		    <?php echo MHtml::_('form.token'); ?>
		</form>
	<!-- content // -->
	</div>
	<div class="clr"></div>
</div>