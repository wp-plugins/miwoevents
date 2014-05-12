<?php
/**
 * @package		MiwoEvents
 * @copyright	2009-2014 Miwisoft LLC, miwisoft.com
 * @license		GNU/GPL http://www.gnu.org/copyleft/gpl.html
 */
# No Permission
defined('MIWI') or die ;
$editor = MFactory::getEditor();
if (MFactory::getUri()->isSSL() == true) { $http = "https://"; } else { $http = "http://"; }
?>

<script src="<?php echo $http; ?>maps.google.com/maps/api/js?sensor=false" type="text/javascript"></script>

<script type="text/javascript">
    var map;
   	var geocoder;
   	var marker;

    Miwi.submitbutton = function(pressbutton) {
   		var form = document.adminForm;
   		if (pressbutton == 'cancel') {
   			Miwi.submitform( pressbutton );
   			return;
   		} else {
   			//Should validate the information here
   			if (form.title.value == "") {
   				alert("<?php echo MText::_( 'COM_MIWOEVENTS_ENTER_LOCATION'); ?>");
   				form.name.focus();
   				return ;
   			}
   			Miwi.submitform( pressbutton );
   		}
   	}

	function initialize() {
		geocoder = new google.maps.Geocoder();
		var miwoevents_map = document.getElementById('miwoevents_map');

		// Create the map object
		map = new google.maps.Map(miwoevents_map, {
				center: new google.maps.LatLng(<?php  if(!empty($this->item->coordinates)){ echo $this->item->coordinates; } else { echo "40.992954,29.042092"; }?>),
				zoom: 10,
				mapTypeId: google.maps.MapTypeId.ROADMAP,
				streetViewControl: false
		});

		// Create the default marker icon
		marker = new google.maps.Marker({
			map: map,
			position: new google.maps.LatLng(<?php  if(!empty($this->item->coordinates)){ echo $this->item->coordinates; } else { echo "40.992954,29.042092"; }?>),
			draggable: true
		});

		// Add event to the marker
		google.maps.event.addListener(marker, 'drag', function() {
			geocoder.geocode({'latLng': marker.getPosition()}, function(results, status) {
				if (status == google.maps.GeocoderStatus.OK) {
					if (results[0]) {
						document.getElementById('address').value = results[0].formatted_address;
						document.getElementById('coordinates').value = marker.getPosition().toUrlValue();
					}
				}
			});
		});
	}

	function getLocationFromAddress() {
		var address = document.getElementById('address').value;
		geocoder.geocode( { 'address': address}, function(results, status) {
			if (status == google.maps.GeocoderStatus.OK) {
				map.setCenter(results[0].geometry.location);
				marker.setPosition(results[0].geometry.location);
				$('coordinates').value = results[0].geometry.location.lat().toFixed(7) + ',' + results[0].geometry.location.lng().toFixed(7);
			} else {
				alert('We\'re sorry but your location was not found.');
			}
		});
	}

	// Initialize google map
	google.maps.event.addDomListener(window, 'load', initialize);

	// Search for addresses
	function getLocations(term) {
		var content = $('eventmaps_results');
		address = $('address').getSize();

		$('eventmaps_results').setStyle('width', address.x - 21);
		$('eventmaps_results').style.display = 'none';
		$$('#eventmaps_results li').each(function(el) {
			el.dispose();
		});

		if (term != '') {
			geocoder.geocode( {'address': term }, function(results, status) {
				if (status == 'OK') {
					results.each(function(item) {

						theli = new Element('li');
						thea = new Element('a', {
							href: 'javascript:void(0)',
							'text': item.formatted_address
						});

						thea.addEvent('click', function() {
							$('address').value = item.formatted_address;
							$('coordinates').value = item.geometry.location.lat().toFixed(7) + ',' + item.geometry.location.lng().toFixed(7);
							var location = new google.maps.LatLng(item.geometry.location.lat().toFixed(7), item.geometry.location.lng().toFixed(7));
							marker.setPosition(location);
							map.setCenter(location);
							$('eventmaps_results').style.display = 'none';
						});

						thea.inject(theli);
						theli.inject(content);
					});

					$('eventmaps_results').style.display = '';
				}
			});
		}
	}

	function clearLocations() {
		setTimeout( function () {
			$('eventmaps_results').style.display = 'none';
		},1000);
	}
</script>

<form action="<?php echo MRoute::getActiveUrl(); ?>" method="post" name="adminForm" id="adminForm">
    <?php if ($this->_mainframe->isSite()) { ?>
    <div style="float: left; width: 99%; margin-left: 10px;">
        <button class="button btn-success" onclick="Miwi.submitbutton('apply')"><span class="icon-apply icon-white"></span> <?php echo MText::_('COM_MIWOEVENTS_SAVE'); ?></button>
        <button class="button" onclick="Miwi.submitbutton('save')"><span class="icon-save"></span> <?php echo MText::_('COM_MIWOEVENTS_SAVE_CLOSE'); ?></button>
        <button class="button" onclick="Miwi.submitbutton('save2new')"><span class="icon-save-new"></span> <?php echo MText::_('COM_MIWOEVENTS_SAVE_NEW'); ?></button>
        <button class="button" onclick="Miwi.submitbutton('cancel')"><span class="icon-cancel"></span> <?php echo MText::_('COM_MIWOEVENTS_CANCEL'); ?></button>
    </div>
    <br/>
    <br/>
    <?php } ?>
	<div class="width-60 fltlft">
		<fieldset class="adminform">
			<?php if (!MiwoEvents::is30()) { ?>
            <legend><?php echo MText::_('COM_MIWOEVENTS_DETAILS'); ?></legend>
            <?php } ?>
            <table class="admintable">
            <tr>
                <td class="key">
                    <?php echo MText::_( 'COM_MIWOEVENTS_TITLE'); ?>
                </td>
                <td>
                    <input type="text" name="title" value="<?php echo $this->item->title; ?>" class="inputbox required" style="font-size: 1.364em;" size="65" aria-required="true" required="required" aria-invalid="false"/>
                </td>
            </tr>
            
            <tr>
                <td class="key">
                    <?php echo MText::_('COM_MIWOEVENTS_ALIAS'); ?>
                </td>
                <td>
                    <input class="text_area" type="text" name="alias" id="alias" size="45" maxlength="250" value="<?php echo $this->item->alias;?>" />
                </td>
            </tr>
            
            <tr>
                <td class="key">
                    <?php echo MText::_('COM_MIWOEVENTS_ADDRESS'); ?>
                </td>
                <td>
                    <input type="text" name="address" id="address" autocomplete="off" onkeyup="getLocations(this.value)" onblur="clearLocations();" value="<?php echo $this->item->address;?>" class="inputbox required" style="font-size: 1.2em;" size="78" aria-required="true" required="required" aria-invalid="false" />
					<ul id="eventmaps_results" style="display:none;"></ul>
                </td>
            </tr>
            
            <tr>
                <td class="key">
                    <span class="editlinktip hasTip" title="<?php echo MText::_('COM_MIWOEVENTS_COORDINATES'); ?>::<?php echo MText::_('COM_MIWOEVENTS_COORDINATES_EXPLAIN'); ?>"><?php echo MText::_('COM_MIWOEVENTS_COORDINATES'); ?></span>
                </td>
                <td>
                    <input class="text_area" type="text" name="coordinates" id="coordinates" size="30" maxlength="250" value="<?php echo $this->item->coordinates;?>" />
                </td>
            </tr>
            
            <tr>
                <td class="key">
                    <?php echo MText::_('COM_MIWOEVENTS_DESCRIPTION'); ?>
                </td>
                <td>
                    <?php echo $editor->display('description',  $this->item->description , '100%', '250', '75', '10' ) ; ?>
                </td>
            </tr>
        </table>
		</fieldset>
	</div>
	
	<div class="width-40 fltrt">
	<?php echo MHtml::_('sliders.start', 'miwoeventsright', array('useCookie'=>1)); ?>
	<!-- Google Maps -->
	<?php echo MHtml::_('sliders.panel', MText::_('COM_MIWOEVENTS_GOOGLEMAPS_OPTIONS'), 'publishing'); ?>
	<div style="margin-left: 25px;">
        <br/>
		<input type="button" onclick="getLocationFromAddress();" class="button btn button-primary" value="<?php echo MText::_('COM_MIWOEVENTS_PINPOINT'); ?> &raquo;" />
        <br/><br/>
		<div id="miwoevents_map" style="width: 95%; height: 400px"></div>
        <br/>
	</div>
	<div class="clearfix"></div>
	
	<!-- Publishing Options -->
	<?php echo MHtml::_('sliders.panel', MText::_('COM_MIWOEVENTS_PUBLISHING_OPTIONS'), 'publishing'); ?>
	<table class="admintable" width="100%">
	<tr>
		<td class="key">
			<?php echo MText::_( 'COM_MIWOEVENTS_PUBLISHED') ; ?>
		</td>
		<td>
			<?php echo $this->lists['published']; ?>
		</td>
	</tr>
	
	<tr>
		<td class="key">
			<?php echo MText::_( 'COM_MIWOEVENTS_LANGUAGE'); ?>
		</td>
		<td>
			<?php echo $this->lists['language'] ; ?>
		</td>
		<td>
		&nbsp;
		</td>
	</tr>
	
	<tr>
		<td class="key">
			<?php echo MText::_('COM_MIWOEVENTS_CREATED_BY'); ?>
		</td>
		<td>
			<?php echo MiwoEvents::get('utility')->getUserInputbox($this->item->user_id) ; ?>
		</td>
		<td>
			&nbsp;
		</td>
	</tr>
	
	</table>
	<!-- Meta Settings -->
	<?php echo MHtml::_('sliders.panel', MText::_('COM_MIWOEVENTS_META_OPTIONS'), 'publishing'); ?>
	<table class="admintable" width="100%">
	<tr>
    	<td width="100" class="key">
        	<?php echo MText::_('COM_MIWOEVENTS_META_DESC'); ?>
		</td>
		<td>
			<textarea name="meta_desc" id="meta_desc" cols="40" rows="3" class="" aria-invalid="false"><?php echo $this->item->meta_desc;?></textarea>
		</td>
	</tr>
	<tr>
		<td width="100" class="key">
			<?php echo MText::_('COM_MIWOEVENTS_META_KEYWORDS'); ?>
		</td>
		<td>
			<textarea name="meta_key" id="meta_key" cols="40" rows="3" class="" aria-invalid="false"><?php echo $this->item->meta_key;?></textarea>
		</td>
	</tr>
	<tr>
		<td width="100" class="key">
			<?php echo MText::_('COM_MIWOEVENTS_META_AUTHOR'); ?>
		</td>
		<td>
			<input class="text_area" type="text" name="meta_author" id="meta_author" size="55" maxlength="250" value="<?php echo $this->item->meta_author;?>" />
		</td>
	</tr>
	</table>
	<?php echo MHtml::_('sliders.end'); ?>
	</div>

    <div class="clearfix"></div>

	<input type="hidden" name="option" value="com_miwoevents" />

    <?php if ($this->_mainframe->isSite()) { ?>
    <input type="hidden" name="view" value="location" />
    <input type="hidden" name="Itemid" value="<?php echo MiwoEvents::getInput()->getInt('Itemid', 0); ?>" />
    <?php } else { ?>
    <input type="hidden" name="view" value="locations" />
    <?php } ?>

	<input type="hidden" name="cid[]" value="<?php echo $this->item->id; ?>" />
	<input type="hidden" name="task" value="" />
	<?php echo MHtml::_( 'form.token' ); ?>		
</form>