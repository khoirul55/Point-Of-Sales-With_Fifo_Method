$(document).ready(function () {
    if($('.datatable-1').length>0){
        $('.datatable-1').dataTable();
        $('.dataTables_paginate').addClass('btn-group datatable-pagination');
        $('.dataTables_paginate > a').wrapInner('<span />');
        $('.dataTables_paginate > a:first-child').append('<i class="icon-chevron-left shaded"></i>');
        $('.dataTables_paginate > a:last-child').append('<i class="icon-chevron-right shaded"></i>');
    
        $( '.slider-range').slider({
			    range: true,
			    min: 0,
			    max: 20000,
			    values: [ 3000, 12000 ],			
			    slide: function(event, ui) {
				    $(this).find('.ui-slider-handle').attr('title', ui.value);
			    },
	    });
	
        $( '#amount' ).val( '$' + $( '.slider-range' ).slider( 'values', 0 ) + ' - $' + $( '.slider-range' ).slider( 'values', 1 ) );
    

    //Graph/Chart index.html

    var d1 = [ [0, 1], [1, 14], [2, 5], [3, 4], [4, 5], [5, 1], [6, 14], [7, 5],  [8, 5] ];
    var d2 = [ [0, 5], [1, 2], [2, 10], [3, 1], [4, 9],  [5, 5], [6, 2], [7, 10], [8, 8] ];

		var plot = $.plot($('#placeholder2'),
			   [ { data: d1, label: 'Profits'}, { data: d2, label: 'Expenses' } ], {
					lines: {
						show: true,
						fill: true, /*SWITCHED*/
						lineWidth: 2
					},
					points: {
						show: true,
						lineWidth: 5
					},
					grid: {
						clickable: true,
						hoverable: true,
						autoHighlight: true,
						mouseActiveRadius: 10,
						aboveData: true,
						backgroundColor: '#fff',
						borderWidth: 0,
						minBorderMargin: 25,
					},
					colors: [ '#55f3c0', '#0db37e',  '#b4fae3', '#e0d1cb'],
					shadowSize: 0
				 });

		function showTooltip(x, y, contents) {
			$('<div id="gridtip">' + contents + '</div>').css( {
				position: 'absolute',
				display: 'none',
				top: y + 5,
				left: x + 5
			}).appendTo('body').fadeIn(300);
		}

		var previousPoint = null;
		$('#placeholder2').bind('plothover', function (event, pos, item) {
			$('#x').text(pos.x.toFixed(2));
			$('#y').text(pos.y.toFixed(2));

			if (item) {
				if (previousPoint != item.dataIndex) {
					previousPoint = item.dataIndex;

					$('#gridtip').remove();
					var x = item.datapoint[0].toFixed(0),
						y = item.datapoint[1].toFixed(0);

					showTooltip(item.pageX, item.pageY,
								'x : ' + x + '&nbsp;&nbsp;&nbsp; y : ' + y);
				}
			}
			else {
				$('#gridtip').remove();
				previousPoint = null;
			}
		});
    }

    else
    {
        var d1 = [ [0, 1], [1, 14], [2, 5], [3, 4], [4, 5], [5, 1], [6, 14], [7, 5],  [8, 5] ];
		var d2 = [ [0, 5], [1, 2], [2, 10], [3, 1], [4, 9],  [5, 5], [6, 2], [7, 10], [8, 8] ];

		var plot = $.plot($("#placeholder"), 
		[ { data: d1, label: "Data A" }, { data: d2, label: "Data B" } ], {
			lines: { 
				show: true, 
				fill: false, 
				lineWidth: 2 
			},
			points: { 
				show: true, 
				lineWidth: 5 
			},
			grid: {
				clickable: true,
				hoverable: true,
				autoHighlight: true,
				mouseActiveRadius: 10,
				aboveData: true,
				backgroundColor: "#fafafa",
				borderWidth: 0,
				minBorderMargin: 25,
			},
			colors: [ "#090", "#099",  "#609", "#900"],
			shadowSize: 0
		});

        var d1 = [ [0, 1], [1, 14], [2, 5], [3, 4], [4, 5], [5, 1], [6, 14], [7, 5],  [8, 5] ];
		var d2 = [ [0, 5], [1, 2], [2, 10], [3, 1], [4, 9],  [5, 5], [6, 2], [7, 10], [8, 8] ];

		var plot = $.plot($("#placeholder2"),
			   [ { data: d1, label: "Data Y"}, { data: d2, label: "Data X" } ], {
					lines: { 
						show: true, 
						fill: true, /*SWITCHED*/
						lineWidth: 2 
					},
					points: { 
						show: true, 
						lineWidth: 5 
					},
					grid: {
						clickable: true,
						hoverable: true,
						autoHighlight: true,
						mouseActiveRadius: 10,
						aboveData: true,
						backgroundColor: "#fafafa",
						borderWidth: 0,
						minBorderMargin: 25,
					},
					colors: [ "#090", "#099",  "#609", "#900"],
					shadowSize: 0
				 });

		function showTooltip(x, y, contents) {
			$('<div id="gridtip">' + contents + '</div>').css( {
				position: 'absolute',
				display: 'none',
				top: y + 5,
				left: x + 5
			}).appendTo("body").fadeIn(300);
		}

		var previousPoint = null;
		$("#placeholder2").bind("plothover", function (event, pos, item) {
			$("#x").text(pos.x.toFixed(2));
			$("#y").text(pos.y.toFixed(2));

			if (item) {
				if (previousPoint != item.dataIndex) {
					previousPoint = item.dataIndex;
					
					$("#gridtip").remove();
					var x = item.datapoint[0].toFixed(0),
						y = item.datapoint[1].toFixed(0);
					
					showTooltip(item.pageX, item.pageY,
								"x : " + x + "&nbsp;&nbsp;&nbsp; y : " + y);
				}
			}
			else {
				$("#gridtip").remove();
				previousPoint = null;            
			}
		});

          // PREDEFINED DATA
        var data = [
						{ label: "Series1", data: [[1, 10]] },
						{ label: "Series2", data: [[1, 30]] },
						{ label: "Series3", data: [[1, 90]] }
					];

        // DEFAULT
        $.plot($("#pie-default"), data,
		{
		    series: {
		        pie: {
		            show: true
		        }
		    }
		});

        // DEFINE ACTIONS FOR pieHover & pieClick
        function pieHover(event, pos, obj) {
            if (!obj)
                return;
            percent = parseFloat(obj.series.percent).toFixed(2);
            $("#hover").html('<span>' + obj.series.label + ' - ' + percent + '%</span>');
        }

        function pieClick(event, pos, obj) {
            if (!obj)
                return;
            percent = parseFloat(obj.series.percent).toFixed(2);
            alert('' + obj.series.label + ': ' + percent + '%');
        }

        // DONUT
        $.plot($("#pie-donut"), data,
		{
		    series: {
		        pie: {
		            innerRadius: 50,
		            show: true
		        }
		    }
		});

        // DONUT + INTERACTIVE
        $.plot($("#pie-interactive"), data,
		{
		    series: {
		        pie: {
		            innerRadius: 50,
		            show: true
		        }
		    },
		    grid: {
		        hoverable: true,
		        clickable: true
		    }
		});

        $("#pie-interactive").bind("plothover", pieHover);
        $("#pie-interactive").bind("plotclick", pieClick);
    }
});

// Tambahkan di file common.js
document.addEventListener('DOMContentLoaded', function() {
    // Toggle button untuk sidebar
    const sidebarToggle = document.createElement('button');
    sidebarToggle.id = 'sidebarToggle';
    sidebarToggle.className = 'btn btn-link d-md-none rounded-circle mr-3';
    sidebarToggle.innerHTML = '<i class="fa fa-bars"></i>';
    
    // Tambahkan toggle button ke navbar
    document.querySelector('.navbar-inner').prepend(sidebarToggle);
    
    // Event listener untuk toggle sidebar
    sidebarToggle.addEventListener('click', function(e) {
        e.preventDefault();
        document.querySelector('.wrapper').classList.toggle('sidebar-collapsed');
        
        // Simpan state di localStorage
        const isCollapsed = document.querySelector('.wrapper').classList.contains('sidebar-collapsed');
        localStorage.setItem('sidebarState', isCollapsed ? 'collapsed' : 'expanded');
    });
    
    // Check localStorage untuk state sidebar
    const sidebarState = localStorage.getItem('sidebarState');
    if (sidebarState === 'collapsed') {
        document.querySelector('.wrapper').classList.add('sidebar-collapsed');
    }
    
    // Responsive behavior
    function checkWidth() {
        if (window.innerWidth < 768) {
            document.querySelector('.wrapper').classList.add('sidebar-collapsed');
        } else {
            if (sidebarState !== 'collapsed') {
                document.querySelector('.wrapper').classList.remove('sidebar-collapsed');
            }
        }
    }
    
    // Check on load and resize
    window.addEventListener('resize', checkWidth);
    checkWidth();
});

document.addEventListener('DOMContentLoaded', function() {
    // Sidebar toggle functionality
    const sidebarToggleBtn = document.getElementById('sidebarToggleBtn');
    const wrapper = document.querySelector('.wrapper');
    
    if (sidebarToggleBtn) {
        sidebarToggleBtn.addEventListener('click', function() {
            wrapper.classList.toggle('sidebar-active');
        });
    }

    // Submenu toggle functionality
    const submenuItems = document.querySelectorAll('.has-submenu');
    
    submenuItems.forEach(item => {
        const link = item.querySelector('.nav-link');
        
        link.addEventListener('click', function(e) {
            e.preventDefault();
            item.classList.toggle('open');
            
            const submenuIcon = this.querySelector('.submenu-icon');
            if (submenuIcon) {
                submenuIcon.classList.toggle('icon-chevron-down');
                submenuIcon.classList.toggle('icon-chevron-up');
            }
        });
    });

    // Responsive handling
    function handleResize() {
        if (window.innerWidth < 768) {
            wrapper.classList.remove('sidebar-collapsed');
            wrapper.classList.add('mobile');
        } else {
            wrapper.classList.remove('mobile');
            wrapper.classList.remove('sidebar-active');
        }
    }

    window.addEventListener('resize', handleResize);
    handleResize();
});
