jQuery(function($){
	$('#escl-search-user').keyup(function(){
		var searchterm = $(this).val();
		if(searchterm != ""){
			escl_usersearch(searchterm);
		}
		return false;
	});
	
	$('a.liveUserResult').live('click', function(){
		var classname = $(this).parent('li').attr('class');
		var userid = classname.split('-'), userid = userid[userid.length-1];
		escl_addusertogroup(userid);
		return false;
	});
	
	$('a.rmFromGroup').click(function(){
		var classname = $(this).parent('li').attr('class'), classname = classname.split(' '), classname = classname[1];
		var userid = classname.split('-'), userid = userid[1];
		escl_removeuserfromgroup(userid);
		return false;
	});
});
