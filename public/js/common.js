function dataadd ($db) {
	location.href="/admin.php/admin/add?db="+$db;
}
function checkall () {
    $('input[name=check[]]').prop('checked','true');
}
function checkdel () {
	if (confirm('记录删除后无法恢复，确认要删除这些记录吗？')) {
		$('#delform').submit();
	}
}
function del ($db,$id) {
	if (confirm('记录删除后无法恢复，确认要删除这些记录吗？')) {
		location.href="/admin.php/admin/del?db="+$db+"&id="+$id;
	}
}
function edit ($db,$id) {
	location.href="/admin.php/admin/edit?db="+$db+"&id="+$id;
}