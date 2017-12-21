$(document).ready(function(){
	$('.insert').click(function(){
		var i = $(this).attr('d-data');
		location.href = i+'add';
	});
	$('.desc').click(function(){
		var that = this;
		var id = $(that).attr('cart-data');
		var i = $(that).attr('d-data');
		location.href = i+'desc.html?i='+id;
	});
	$('.delete').click(function(){
		if (confirm('删除后无法恢复，确认要删除该条数据吗？')) {
			var id = $(this).attr('cart-data');
			var db = $('.desct').attr('d-data');
			location.href = 'del.html?id='+id+'&db='+db;			
		}
	});
});