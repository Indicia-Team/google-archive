<script type="text/javascript">
	jQuery(document).ready(function () {
		jQuery.each(map.controls, function(i, control) {
			if (control instanceof OpenLayers.Control.Navigation) {
				control.disableZoomWheel();
			}
		});
	});
</script>