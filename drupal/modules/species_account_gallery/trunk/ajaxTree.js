function collapseNode(span) {
  $(span).parent().find('> .species-gallery-list').slideUp('fast');
  $(span).removeClass('expanded');
  $(span).addClass('collapsed');
}

function expandNode(span) {
  $(span).parent().find('> .species-gallery-list').slideDown('fast');
  $(span).removeClass('collapsed');
  $(span).addClass('expanded');
}

function loadNode() {
  var tid=this.id.split('-')[1], span=this;
  $.get(
    Drupal.settings.galleryAjaxPath, 
    {tid:tid},
    function(data) {
      $(span).parent().append(data);
      $(span).removeClass('not-loaded');
      expandNode(span);
      // attach image popup plugins to the new content
      if (typeof hs !== "undefined") {
        $(span).parent().find('> .species-gallery-list a.highslide').click(function() {
          return hs.expand(this);
        });
      }
      if (typeof $.fancybox !== "undefined") {
        $(span).parent().find('> .species-gallery-list a.fancybox').fancybox();
      }
    },
    'html'
  );
}
  
$(document).ready(function() {
  $('ul.species-gallery-list li span.not-loaded').live('click', loadNode);
  $('ul.species-gallery-list li span.expanded').live('click', function() { collapseNode(this); });
  $('ul.species-gallery-list li span.collapsed').live('click', function() { expandNode(this); });
});


