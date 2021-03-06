/*
 * OnPointBox a schedule manager for group classes
 * Copyright (C) 2015  Tiago Miguel Basilio da Silva
 *
 * This program is free software: you can redistribute it and/or modify
 * it under the terms of the GNU General Public License as published by
 * the Free Software Foundation, either version 3 of the License, or
 * (at your option) any later version.
 *
 * This program is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * GNU General Public License for more details.
 *
 * You should have received a copy of the GNU General Public License
 * along with this program.  If not, see <http://www.gnu.org/licenses/>.
 */

/**
 * @author Tiago Miguel Basilio Silva
 */
$(document).ready(function(){
	var asl;
	
	
	$("dd input[type=radio]").on( "click", function(e){
		
		asl = ajaxSelectList($( "input[type=radio]:checked" ).val());
	});

	$("#dtrainee button[type=submit]").on("click", function(){
		
		ajaxDeleteUser($("#iemail").text());
	});
	
	$('#fn').on('submit', function(ev){
		var oData = new FormData(this);
	
		oData.append("utipo", "Trainee");
		
		var oReq = new XMLHttpRequest();
		oReq.open("POST", "/php/h_user_new.php", true);
		oReq.onload = function(oEvent) {
	  		if (oReq.status == 200) {
	    		var response = JSON.parse(oReq.responseText);

				if(!response.error){
					$("#radmail").prop( "checked", true );
					$("#radmail").click();
					asl.done(function(e){
						var ml = $('#ntrainee').find('.modal-body #fniemail').val();
						$('#ntrainee').modal('hide');
						$('#acomp').val(ml).trigger('paste.xdsoft').trigger('pick.xdsoft');
					});
				}else{
					div = $("<div>").attr("class","alert alert-danger").attr("role","alert");
					cbutton = $("<button>").attr("type","button").attr("class", "close").attr("data-dismiss", "alert").attr("aria-label", "Close");
					span = $("<span>").attr("aria-hidden", "true").append("&times;");
					strong = $("<strong>").append("Error: ");
					cbutton.append(span);
					div.append(cbutton);
					div.append(strong);
					div.append(response.error_message);
					
					fgdiv = $("<div>").attr("class","form-group").attr("id","fgwarning");
					fgdiv.append(div);
					$('#fn .modal-body').append(fgdiv);
					
				}
		    } else {
		    }
		};
	
		oReq.send(oData);
		ev.preventDefault();
	});
	
	$('#fe').on('submit', function(ev){
		var oData = new FormData(this);
	
		oData.append("option", "edit");
		oData.append("oldemail", $("#iemail").text());
				
		var oReq = new XMLHttpRequest();
		oReq.open("POST", "/php/h_trainee.php", true);
		oReq.onload = function(oEvent) {
			if (oReq.status == 200) {
				var response = JSON.parse(oReq.responseText);
  				  
  				if(!response.error){
					$("#radmail").prop( "checked", true );
					$("#radmail").click();
					asl.done(function(e){
						var ml = $('#etrainee').find('.modal-body #fniemail').val();
						$('#etrainee').modal('hide');
						$('#acomp').val(ml).trigger('paste.xdsoft').trigger('pick.xdsoft');
					});
				}else{
					div = $("<div>").attr("class","alert alert-danger").attr("role","alert");
					cbutton = $("<button>").attr("type","button").attr("class", "close").attr("data-dismiss", "alert").attr("aria-label", "Close");
					span = $("<span>").attr("aria-hidden", "true").append("&times;");
					strong = $("<strong>").append("Error: ");
					cbutton.append(span);
					div.append(cbutton);
					div.append(strong);
					div.append(response.error_message);
					
					fgdiv = $("<div>").attr("class","form-group").attr("id","fgwarning");
					fgdiv.append(div);
					$('#fe .modal-body').append(fgdiv);
					
				}
  			} else {
			}
		};
		
		oReq.send(oData);
		ev.preventDefault();
	});
	
	
	$('#etrainee, #ntrainee').on('hidden.bs.modal', function () {
		
	    $("input[type!=radio]", this).each(function(){
			$(this).val('');
		});
		$("input[type=radio]", this).each(function(){
			$(this).prop('checked', false);
		});
		$('#fgwarning').remove();
	});
	
	$('#etrainee').on('show.bs.modal', function (event) {
		var modal = $(this);
				
		modal.find('.modal-body #fniname').val($("#iname").text());
		modal.find('.modal-body #fnidtborn').val($("#idtborn").text());
		
		var gender = $("#igender").text();
		if(gender!="") modal.find('.modal-body #'+gender).prop('checked', true);
		
		modal.find('.modal-body #fniemail').val($("#iemail").text());
		modal.find('.modal-body #fniiphone').val($('#iphone').text());
	});
	
	$('#etrainee, #ntrainee').on('shown.bs.modal', function () {
		$(this).find('.modal-body #fniname').focus();
	});
	
	$('#istate').on('change', function() {
    
    	ajaxUpdateState($("#iemail").text());
	});
});

function ajaxSelectList(rop) {
	return $.ajax({
		url: "/php/h_trainee.php",
		type : "POST",
		data : { "option" : "selectlist",
				 "radio" : rop,
				 "type" : "Trainee" },
		datatype: "json" 
	}).done(function(dates) {
		var parsed = JSON.parse(dates);
		
		
		
		acparent = $('#acomp').parent();
		$('#acomp').remove();
		acparent.append("<input type='text' class='form-control' placeholder='Search trainee' id='acomp'>");
		
		
		$('#acomp').autocomplete({
		limit:5,
  		valueKey:'title',
	  	source:[{
	  		data:parsed,
	  		
			getTitle:function(item){
				return item.title;
			},
			getValue:function(item){
				return item.title;
			}
			}]
		}).on('selected.xdsoft',function(e,datum){
			
			$.ajax({
				url: "/php/h_trainee.php",
				type : "post",
				data : { "option" : "selectid",
						 "uid" : datum.id },
				datatype: "json"
			}).done(function(datass) {
				var parsedd = JSON.parse(datass);
				
				$('#iimg').attr("src", "/"+parsedd.img);
				$('#iname').text(parsedd.name);
				$('#idtborn').text(parsedd.dtnasc);
				if(parsedd.gender=="M") {$('#igender').text("Male");}
				if(parsedd.gender=="F") {$('#igender').text("Female");}
				if(parsedd.gender=="") {$('#igender').text("");}
				$('#iemail').text(parsedd.email);
				$('#iphone').text(parsedd.phone);
				$('#istate').prop('checked', parsedd.state);
				$('#idp').show();
				$('#btn_e').show();
				$('#btn_a').show();
				
				
			}).fail(function() {
			});
			
		});
		
	}).fail(function() {
	});
}

function ajaxDeleteUser(email) {
	return $.ajax({
		url: "/php/h_trainee.php",
		type : "POST",
		data : { "option" : "delete",
				 "uemail" : email },
		datatype: "json"
	}).done(function(dates) {
		var response = JSON.parse(dates);
		if(!response.error){
			window.location.reload(true);
		}
	}).fail(function() {
	});
}

function ajaxUpdateState(email) {
	return $.ajax({
		url: "/php/h_trainee.php",
		type : "POST",
		data : { "option" : "editstate",
				 "uemail" : email },
		datatype: "json"
	}).done(function(dates) {
		var response = JSON.parse(dates);
		if(response.db==0){
			$("#radmail").prop( "checked", true );
			$("#radmail").click();
			$('#acomp').val(email).trigger('paste.xdsoft').trigger('pick.xdsoft');
		}
	}).fail(function() {
	});
}


