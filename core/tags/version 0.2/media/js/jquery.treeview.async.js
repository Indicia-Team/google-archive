/*
 * Async Treeview 0.1 - Lazy-loading extension for Treeview
 * 
 * http://bassistance.de/jquery-plugins/jquery-plugin-treeview/
 *
 * Copyright (c) 2007 Jörn Zaefferer
 *
 * Dual licensed under the MIT and GPL licenses:
 *   http://www.opensource.org/licenses/mit-license.php
 *   http://www.gnu.org/licenses/gpl.html
 *
 * Revision: $Id$
 *
 */

;(function($) {
	var CLASSES = $.treeview.classes;

function load(settings, qfield, qvalue, qnullfield, child, container) {
	var parent=child.parent();
	var hitarea = parent.find("div." + CLASSES.hitarea);
	var filter = "&qfield="+qfield+"&q="+qvalue+((qnullfield == null) ? "" : "&"+qnullfield+"=NULL");
	$.getJSON(settings.url+"?mode=json&callback=?&view="+settings.extraParams.view+filter,
			null,
		function(response) {
		  function createNode(parent) {
			var parsed = settings.parse(this);
			var current = $("<li/>").attr("id", parsed.value || "").html("<span>" + parsed.caption + "</span>").appendTo(parent);
			if (this.classes) {
				current.children("span").addClass(this.classes);
			}
			if (! this.nochildren ) { 
				var branch = $("<ul/>").appendTo(current);
				current.addClass("hasChildren");
				createNode.call({
					classes: "placeholder",
					caption: "&nbsp;",
					nochildren:[]
				}, branch);
			} 
		  }
		child.empty();
		$.each(response, createNode, [child]);
		if (child.children().length)
			$(container).treeview({add: child}); 
		else {
			/* There are no children for this node, so remove the hitarea (which holds the expand/collapse image)
			 * for the node ie the <LI> parent of the <UL> identified by child. Also remove/alter the classes
			 * for the parent, and remove the now used and forever empty <UL> identified by child.
			 * Have to do this here due to async nature.
			 */ 
			if (hitarea.length)
				hitarea.remove();
			parent
				.replaceClass(CLASSES.lastCollapsable, CLASSES.last)
				.removeClass(CLASSES.collapsable);
			child.remove();
		}
	});
}

var proxied = $.fn.treeview;
$.fn.treeview = function(settings) {
	if (!settings.url) {
		return proxied.apply(this, arguments);
	}
	var container = this;
	if (!container.children().size())
		load(settings, settings.topField, settings.topValue, settings.parentField, this, container);
	var userToggle = settings.toggle;
	return proxied.call(this, $.extend({}, settings, {
		collapsed: true,
		toggle: function() {
			var $this = $(this);
			elemlist = container;
			do
			{ 
				elemlist.removeClass(CLASSES.selected);
				elemlist = elemlist.children();
			} 
			while (elemlist.length)
			$this.children("span").addClass(CLASSES.selected);

			if (settings.valueControl)
				$("input#"+settings.valueControl).val($this.attr("id"));
			if ($this.hasClass("hasChildren")) {
				var childList = $this.removeClass("hasChildren").find("ul");
				load(settings, settings.parentField, this.id, null, childList, container);
			}
			if (userToggle) {
				userToggle.apply(this, arguments);
			}
		}
	}));
};

})(jQuery);