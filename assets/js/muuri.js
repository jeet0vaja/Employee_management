jQuery(document).ready(function($) {
	var boardGrid;

	boardGrid = new Muuri('.category-items', {
	  	items: '.item',
	  	layoutDuration: 400,
	  	dragSortInterval: 0,
	  	dragContainer: document.body,
	  	dragReleaseDuration: 400,
	  	dragReleaseEasing: 'ease'
	  });	

	var questionContainers = [].slice.call(document.querySelectorAll('.question'));
	var columnGrids = [];

	// Define the column grids so we can drag those
	// items around.
	questionContainers.forEach(function (container) {

	  // Instantiate column grid.
	  var questiongrid = new Muuri(container, {
	  	items: '.board-item',
	  	layoutDuration: 400,
	  	layoutEasing: 'ease',
	  	dragEnabled: false,
	  	dragSort: function () {
	  		return columnGrids;
	  	},
	  	dragSortInterval: 0,
	  	dragContainer: document.body,
	  	dragReleaseDuration: 400,
	  	dragReleaseEasing: 'ease'
	  })
	  .on('dragStart', function (item) {

	    // Let's set fixed widht/height to the dragged item
	    // so that it does not stretch unwillingly when
	    // it's appended to the document body for the
	    // duration of the drag.
	    item.getElement().style.width = item.getWidth() + 'px';
	    item.getElement().style.height = item.getHeight() + 'px';
	})
	  .on('dragReleaseEnd', function (item) {
	    // Let's remove the fixed width/height from the
	    // dragged item now that it is back in a grid
	    // column and can freely adjust to it's
	    // surroundings.
	    item.getElement().style.width = '';
	    item.getElement().style.height = '';
	    // Just in case, let's refresh the dimensions of all items
	    // in case dragging the item caused some other items to
	    // be different size.
	    columnGrids.forEach(function (questiongrid) {
	    	questiongrid.refreshItems();
	    });
	    /*console.log(item.getElement().innerText);*/
	})
	  .on('layoutStart', function () {
	    // Let's keep the board grid up to date with the
	    // dimensions changes of column grids.
	    boardGrid.refreshItems().layout();
	});
	  /*console.log(questiongrid.getElement().innerText);*/
		  // Add the column grid reference to the column grids
		  // array, so we can access it later on.
		  columnGrids.push(questiongrid);
		});

	var tf_reviewsystemquestionContainers = [].slice.call(document.querySelectorAll('.tf_reviewsystem-question'));
	

	// Define the column grids so we can drag those
	// items around.
	tf_reviewsystemquestionContainers.forEach(function (container) {

	  // Instantiate column grid.
	  var tf_reviewsystemquestiongrid = new Muuri(container, {
	  	items: '.board-item',
	  	layoutDuration: 400,
	  	layoutEasing: 'ease',
	  	dragEnabled: true,
	  	dragSort: function () {
	  		return columnGrids;
	  	},
	  	dragSortInterval: 0,
	  	dragContainer: document.body,
	  	dragReleaseDuration: 400,
	  	dragReleaseEasing: 'ease'
	  })
	  .on('dragStart', function (item) {

	    // Let's set fixed widht/height to the dragged item
	    // so that it does not stretch unwillingly when
	    // it's appended to the document body for the
	    // duration of the drag.
	    item.getElement().style.width = item.getWidth() + 'px';
	    item.getElement().style.height = item.getHeight() + 'px';
	})
	  .on('dragReleaseEnd', function (item) {
	    // Let's remove the fixed width/height from the
	    // dragged item now that it is back in a grid
	    // column and can freely adjust to it's
	    // surroundings.
	    item.getElement().style.width = '';
	    item.getElement().style.height = '';
	    // Just in case, let's refresh the dimensions of all items
	    // in case dragging the item caused some other items to
	    // be different size.
	    columnGrids.forEach(function (tf_reviewsystemquestiongrid) {
	    	tf_reviewsystemquestiongrid.refreshItems();
	    });
	    /* console.log(item.getElement().innerText);*/
	    tf_reviewsystemquestiongrid.filter('.foo');
	    console.log(tf_reviewsystemquestiongrid.getElement().innerText);

	})
	  .on('layoutStart', function () {
		    // Let's keep the board grid up to date with the
		    // dimensions changes of column grids.
		    boardGrid.refreshItems().layout();
		});

		  // Add the column grid reference to the column grids
		  // array, so we can access it later on.
		  columnGrids.push(tf_reviewsystemquestiongrid);
		});

	// Instantiate the board grid so we can drag those
	// columns around.
	boardGrid = new Muuri('.board', {
		layoutDuration: 400,
		layoutEasing: 'ease',
		dragEnabled: true,
		dragSortInterval: 0,
		dragStartPredicate: {
			handle: '.board-column-header'
		},
		dragReleaseDuration: 400,
		dragReleaseEasing: 'ease'
	});
});		