/**
 * @author Pumpkin'
 * @modified by Wang, Qing
 * @version 1.0
 * @date 2010-05-28
 */
var EntryController = Class.create({
	initialize:function(){
		this.entries = $$('#overview div.entry');
		this.entries.each(function(entry){
			entry.observe('mouseover', this.handleMouseover);
			entry.observe('mouseout', this.handleMouseout);
		}, this);
	},
	
	handleMouseover:function(event){
		event.stop();
		this.addClassName('highlighted');
	},
	
	handleMouseout:function(event){
		event.stop();
		this.removeClassName('highlighted');
	},
});

document.observe('dom:loaded', function(){
	var fuck = new EntryController();
});