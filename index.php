<html>
	<head>
		<title>Keepa</title>
		<link rel="stylesheet" href="../styles/main.css">
		<link rel="stylesheet" href="../styles/menu.css">
        <script src="jquery.min.js"></script>
	</head>
	<body>
		<script>
			function findGetParameter(parameterName) {
    var result = null,
        tmp = [];
    location.search
    .substr(1)
        .split("&")
        .forEach(function (item) {
        tmp = item.split("=");
        if (tmp[0] === parameterName) result = decodeURIComponent(tmp[1]);
    });
    return result;
}	

			function checkKeyCreation(info, name){
				if (info["status"]=="success"){
					manifestAnswerKey(info["id"], name);
				}else{
					alert("Failure to create object");
				}
			}
			function manifestAnswerKey(id, name){
				document.getElementById("keytable").innerHTML += "<tr><td id=nameof"+id+" onclick='rename(\"name\", "+id+")'>"+name+"</td><td><button onclick='location.href=\"?op=editkey&id="+id+"\"'>edit</button></td><td><button onclick='location.href=\"?op=mankeys&rmid="+id+"\"'>delete</button></td>";
				$(document).on('blur','#txt_nameof'+id, function(){
					var nombre = $(this).val();
					if(nombre=="")return;
					$.ajax({type: 'post',
						url: 'rename.php?id='+id+'&name='+window.btoa(nombre),
						success:function(d){
							console.log(d);
							$('#nameof'+id).text(nombre);
							}
						});
					});
			}
			function createAnswerKey(name){
				$.post("./newkey.php", {
					"name":name}, function(d){checkKeyCreation(d, name)}, "json");
			}/*
			function rename(id){
				var dis = document.getElementById("nameof"+id);
				var old = $(dis).text();
				$(dis).html(''); 
				$('<input></input')
				.attr({'type':'text','id':'txt_nameof'+id, 'value':old, 'size':'16'}).appendTo("#nameof"+id);
				$("#txt_nameof"+id).focus();
			}*/
			function rename(subject, id){
				var dis = document.getElementById(subject+"of"+id);
				var old = $(dis).text();
				$(dis).html(''); 
				$('<input></input')
				.attr({'type':'text','id':'txt_'+subject+'of'+id, 'value':old, 'size':'16'}).appendTo("#"+subject+"of"+id);
				$("#txt_"+subject+"of"+id).focus();
			}
			function manifestProblem(id, question, answer){
				document.getElementById("problemtable").innerHTML += "<tr><td>"+id+"</td><td id=questionof"+id+" onclick='rename(\"question\","+id+")'>"+question+"</td><td id=answerof"+id+" onclick='rename(\"answer\","+id+")'>"+answer+"</td><td><button onclick='location.href=\"?op=editkey&id="+findGetParameter("id")+"&rmid="+id+"\"'>delete</button></td></tr>";
				$(document).on('blur','#txt_questionof'+id, function(){
					var nombre = $(this).val();
					if(nombre=="")return;
					$.ajax({type: 'post',
						url: 'updateproblem.php?parameter=question&id='+id+'&value='+window.btoa(nombre),
						success:function(d){
							console.log(d);
							$('#questionof'+id).text(nombre);
							}
						});
					});
				$(document).on('blur','#txt_answerof'+id, function(){
					var nombre = $(this).val();
					if(nombre=="")return;
					$.ajax({type: 'post',
						url: 'updateproblem.php?parameter=answer&id='+id+'&value='+window.btoa(nombre),
						success:function(d){
							console.log(d);
							$('#answerof'+id).text(nombre);
							}
						});
					});
			}
			function createProblem(parent_id, question, answer){
				$.post("./newproblem.php", {
						"parent_id":parent_id,
						"question":question,"answer":answer},function(d){checkProblemCreation(d, question,answer)},"json");
			}
			function checkProblemCreation(info, question, answer){
				if(info["status"]=="success"){
					manifestProblem(info["id"], question, answer);
				}else{
					alert("There was a problem whilst creating a problem");
				}
			}
		</script>
		<?php
			error_reporting(E_ALL);
			ini_set("display_errors", "On");
		?>
		
		<div class="topcenter">
		<?php
			//Define functions here
			function escapeText($text)
			{
				$text=str_replace("&","&amp;",$text);
				$text=str_replace("<","&lt;",$text);
				return $text;
			}

		?>
		</div>
	</body>
</html>
