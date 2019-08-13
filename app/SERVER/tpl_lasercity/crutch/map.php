<script>
  plugins.ajaxJsonLoad({
    url: '/?option=com_lasercity&task=getMainMapInfo',
    callback: function (obj) {
      plugins.googleMap.init({
        selector: '.map__iframe',
          center: {
              lat: <?= $current_city_alias->lat ?>,
              lng: <?= $current_city_alias->lng ?>
          },
        obj: obj,
        callBackDraw: drawMarker
      });
    }
  });
</script>