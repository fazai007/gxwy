$(function(){

	// select默认选中项颜色
	var unSelected = "#A0A6BC";
	var selected = "#000";
	$("select").css("color", unSelected);
	$("option").css("color", selected);
	$("select").change(function () {
		
		var selItem = $(this).val();
		if (selItem == $(this).find('option:first').val()) {
			$(this).css("color", unSelected);
		} else {
			$(this).css("color", selected);
		}
	});
	
	
	
	// -121发送至邮箱
	var unSelected = "#555";
	var selected = "#555";
	$(".select-style").css("color", unSelected);
	$(".select-style option").css("color", selected);
	




})
