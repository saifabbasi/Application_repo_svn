this.tooltip = function(){
	yOffset = 20;
	xOffset = 10;
	$("a.tooltip").hover(function(e){
		this.t = this.title;
		this.title = "";
		$("body").append("<p id='tooltip'>"+ this.t +"</p>");
		$("#tooltip")
			.css("top",(e.pageY - xOffset) + "px")
			.css("left",(e.pageX + yOffset) + "px")
			.css("z-index", '5000')
			.fadeIn("fast");
	 		},function(){
				this.title = this.t;
				$("#tooltip").remove();
			}
		);
	$("a.tooltip").mousemove(
			function(e){
				$("#tooltip")
				.css("top",(e.pageY - xOffset) + "px")
				.css("left",(e.pageX + yOffset) + "px");
			}
		);
};
	
$(document).ready(function(){
	 tooltip();
});