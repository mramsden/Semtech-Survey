$(function() {
	$('a[title]').qtip({
		position: {
		  target: 'mouse',
		  corner: {
  		  target: 'bottomMiddle',
  		  tooltip: 'topMiddle'
  		},
  		adjust: {
  		  screen: true
  		}
		},
		style: {
			name: 'blue',
			tip: true
		}
	});
});