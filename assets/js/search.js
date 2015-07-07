/*
	Author: Ano Tisam
	Email: an0tis@gmail.com
	Website: http://www.whupi.com
	Description: JavaScript Search logic for Korero Open Dictionary
	LICENSE: Free for personal and commercial use licensed under the Creative Commons Attribution 3.0 License, which means you can:

				Use them for personal stuff
				Use them for commercial stuff
				Change them however you like

			... all for free, yo. In exchange, just give the AUthor credit for the program and tell your friends about it :)
*/

$( document ).ready(function() {
	$('#two').addClass('hidden');
});
	
$('#search_query').click(function() {
   $('#two').removeClass('hidden');
});
$('#search_query').keyup(function(){
	search();
});

function search(){
	
	var query = $('#search_query').val();
	var url = 'json.php?q=' + query;
	var output = '';
	
	if (query.length) {
		$.getJSON(url, function(data) {
			if(data != null){
				var options = {
				  keys: ['maori', 'english', 'description']
				};
				var f = new Fuse(data, options);
				var result = f.search(query);
				
				$.each(data, function(key, val) {
					$("#searchresults").empty();
					console.log(val);
					output += '<h2 class="major"><a href="manage/wordsedit.php?id=' + val.id + '" target="_blank" >' + val.maori + '</a></h2>';
					output += '<p>' + val.english + '<br /><a href="manage/wordsview.php?showdetail=&id=' + val.id + '" target="_blank" >more info</a></p>';				
					$('#searchresults').append(output);
					$('#two').removeClass('hidden');					
			   });		
			}
			else {
				var none = '<header class="major"><p>No words found.</p><header> <p><a href="manage/wordsadd.php" class="button special">Add</a></p>';
				$("#searchresults").empty();
				$('#searchresults').append(none);
				$('#two').removeClass('hidden');
			}
		});	
	}
	else {
		$('#two').addClass('hidden');
	}
}