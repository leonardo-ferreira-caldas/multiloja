var Slide = {
	slideSelector: "#slider",
	nextSelector: "#slider_next",
	prevSelector: "#slider_prev",
	effectSpeed: 'fast',
	slideDuration: 4000,
	actualElement: null,
	actualIndex: -1,
	slideSize: 0,
	intervalFunction: null,
	init: function() {
		var $li = $(Slide.slideSelector).find("li");
		Slide.actualElement = $li.eq(0);
		Slide.slideSize = $li.size();
		
		Slide.next();
		
		Slide.intervalFunction = setInterval(Slide.next, Slide.slideDuration);
		
		$(Slide.nextSelector).click(function() {
			clearInterval(Slide.intervalFunction);
			Slide.next();
			Slide.intervalFunction = setInterval(Slide.next, Slide.slideDuration);
		});
		
		$(Slide.prevSelector).click(function() {
			clearInterval(Slide.intervalFunction);
			Slide.prev();
			Slide.intervalFunction = setInterval(Slide.next, Slide.slideDuration);
		});
		
	},
	next: function() {
		
		var nextEq = Slide.actualIndex + 1;
		
		if (nextEq >= Slide.slideSize) {
			nextEq = 0;
		}
		
		var $li = $(Slide.slideSelector).find("li");
		$li.eq(Slide.actualIndex).fadeOut(Slide.effectSpeed);
		$li.eq(nextEq).fadeIn(Slide.effectSpeed);
		
		Slide.actualIndex = nextEq;
		
	},
	prev: function() {
		
		var prevEq = Slide.actualIndex - 1;
		
		if (prevEq < 0) {
			prevEq = Slide.slideSize - 1;
		}
		
		var $li = $(Slide.slideSelector).find("li");
		$li.eq(Slide.actualIndex).fadeOut(Slide.effectSpeed);
		$li.eq(prevEq).fadeIn(Slide.effectSpeed);
		
		Slide.actualIndex = prevEq;
		
	}
};

$(document).ready(function() {
	Slide.init();
});
