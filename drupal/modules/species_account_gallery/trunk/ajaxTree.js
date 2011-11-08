function loadNode() {
  var tid=this.id.split('-')[1], span=this;
  $.get(
    'http://localhost/bwars/species_gallery/ajax', 
    {tid:tid},
    function(data) {
      $(span).after(data);
      $(span).removeClass('not-loaded');
      $(span).addClass('expanded');
      // attach image popup plugins to the new content
      if (typeof hs !== "undefined") {
        $($(span).next().find('a.highslide')).click(function() {
          return hs.expand(this);
        });
      }
      if (typeof $.fancybox !== "undefined") {
        $($(span).next().find('a.fancybox')).fancybox();
      }
    },
    'html'
  );
}

function collapseNode() {
  $(this).next().hide();
  $(this).removeClass('expanded');
  $(this).addClass('collapsed');
}

function expandNode() {
  $(this).next().show();
  $(this).removeClass('collapsed');
  $(this).addClass('expanded');
}
  
$(document).ready(function() {
  $('ul.species-gallery-list li span.not-loaded').live('click', loadNode);
  $('ul.species-gallery-list li span.expanded').live('click', collapseNode);
  $('ul.species-gallery-list li span.collapsed').live('click', expandNode);
});


