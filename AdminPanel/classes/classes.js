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
				
	ajaxSelectList();
	
	$("#iselclass").on("change", function() {
		
		var obj = $(this).children(":selected").attr("id");
		//takes data from the selected city and displays
		ajaxSelectID(obj);
		
		$("#idp").show();
		$('#btn_e').show();
		$('#btn_a').show();
		
	});
	
	

	$("#dclass button[type=submit]").on("click", function(){
		ajaxDeleteClass($("#iid").text());
	});
	
	$('#fn').on('submit', function(ev){
		
		if (!$('tbody', this).is(':parent')) {

		    
		}else {
		
			var oData = new FormData(this);
		
			oData.append("option", "new");
			
			var table = $("tbody", this);
			
			$("tr", table).each(function(index){
				a = $(this).find('td[name="cday"]').attr('id');
				b = $(this).find('td[name="chour"]').text();
				c = $(this).find('td[name="cdur"]').text();
    			d = $(this).find('td[name="cinstructor"]').attr('id');
				
				var arr = [a, b, c, d];   
				oData.append("cperiod[]", arr);
			});
			
			var oReq = new XMLHttpRequest();
			oReq.open("POST", "/php/h_class.php", true);
			oReq.onload = function(oEvent) {
		  		if (oReq.status == 200) {
		    		var response = JSON.parse(oReq.responseText);

					if(!response.error){
						
						var asl = ajaxSelectList();
						asl.done(function(e){
							var an = $('#nclass').find('.modal-body #fniname').val();
							$('#nclass').modal('hide');
							$('option', '#iselclass').each(function() {

								if ($(this).text() == an) {
								    $(this).prop('selected', true); 
								    $(this).trigger('change');
								}
							});
						});
					}else{
						div = $("<div>").attr("class","alert alert-danger").attr("role","alert");
						cbutton = $("<button>").attr("type","button").attr("class", "close").attr("data-dismiss", "alert").attr("aria-label", "Close");
						span = $("<span>").attr("aria-hidden", "true").append("&times;");
						strong = $("<strong>").append("Error: ");
						cbutton.append(span);
						div.append(cbutton);
						div.append(strong);
						div.append(response.db.error_message);
						
						fgdiv = $("<div>").attr("class","form-group").attr("id","fgwarning");
						fgdiv.append(div);
						$('#fn .modal-body').append(fgdiv);
						
					}
			    } else {
			    }
			};
		
			oReq.send(oData);
		}
		ev.preventDefault();
	});
	
	$('#fe').on('submit', function(ev){
		
		if (!$('#fe tbody').is(':parent')) {
		}else {
		
			var oData = new FormData(this);
		
			oData.append("option", "edit");
			
			var idclass = $("#iselclass").children(":selected").attr("id");
			oData.append("cid", idclass);
			//var idprof = $(this).find("#fniinstructor option:selected").val();
			//oData.append("cinstructor", idprof);
			
			//odata for each of the class lines, with cycle to create array
			var table = $("tbody", this);
			
			$("tr", table).each(function(index){
				
				id = $(this).attr('id');
				if(typeof id == "undefined") {
				
					a = $(this).find('td[name="cday"]').attr('id');
    				b = $(this).find('td[name="chour"]').text();
    				c = $(this).find('td[name="cdur"]').text();
    				d = $(this).find('td[name="cinstructor"]').attr('id');
					
					var arr = [a, b, c, d];   
					oData.append("cperiod[]", arr);
				}
			});
			
			var oReq = new XMLHttpRequest();
			oReq.open("POST", "/php/h_class.php", true);
			oReq.onload = function(oEvent) {
				if (oReq.status == 200) {
					var response = JSON.parse(oReq.responseText);
	  				  
	  				if(!response.error){
						var asl = ajaxSelectList();
						asl.done(function(e){
							var an = $('#eclass').find('.modal-body #fniname').val();
							$('#eclass').modal('hide');
							$('option', '#iselclass').each(function() {

								if ($(this).text() == an) {
								    $(this).prop('selected', true); 
								    $(this).trigger('change');
								}
							});
						});
					}else{
						div = $("<div>").attr("class","alert alert-danger").attr("role","alert");
						cbutton = $("<button>").attr("type","button").attr("class", "close").attr("data-dismiss", "alert").attr("aria-label", "Close");
						span = $("<span>").attr("aria-hidden", "true").append("&times;");
						strong = $("<strong>").append("Error: ");
						cbutton.append(span);
						div.append(cbutton);
						div.append(strong);
						div.append(response.db.error_message);
						
						fgdiv = $("<div>").attr("class","form-group").attr("id","fgwarning");
						fgdiv.append(div);
						$('#fe .modal-body').append(fgdiv);
						
					}
	  			} else {
				}
			};
			
			oReq.send(oData);
		}
		ev.preventDefault();
	});
	
	
	$('#eclass, #nclass').on('hidden.bs.modal', function () {
		
	    $("input", this).each(function(){
			$(this).val('');
		});
		$('table', this).hide();
		$('tbody', this).empty();
		$('#fgwarning').remove();
	});
	
	$('#eclass').on('show.bs.modal', function (event) {
		var modal = $(this);
				
		modal.find('.modal-body #fniname').val($("#iname").text());
		modal.find('.modal-body #fnidtinit').val($("#idtinit").text());
		modal.find('.modal-body #fnidtend').val($('#idtend').text());
		
		//clone();
		modal.find('.modal-body tbody').append($('#iperiod').children().clone());
		modal.find('.modal-body tbody').children().each(function(){
			btn = $("<button>").attr({
				'type':'button',
				'class':'btn btn-danger btn-xs'
				}).text("X");
			$(this).append($("<td>").append(btn));
		});
		modal.find('tbody button').on('click', function() {
				p = $(this).parent();
				gp = $(p).parent();
				
				ajaxDeletePeriod(gp.attr('id'));
				gp.remove();
		});
		
		modal.find('.modal-body #fnimax').val($("#imax").text());
		modal.find('.modal-body #fnidesc').val($("#idesc").text());
		
		modal.find('.modal-body table').show();
	});
	
	$('#eclass, #nclass').on('show.bs.modal', function () {
		
	    ajaxSelectProfList($(this).attr("id"));
	});
	
	$('#eclass, #nclass').on('shown.bs.modal', function () {
		$(this).find('.modal-body #fniname').focus();
	});
	
	$('#istate').on('change', function() {
    
    	ajaxUpdateState($("#iid").text());
	});
	
	$('.periodadd').on('click', function() {
		
		p = $(this).parent();
		gp = $(p).parent();
		
		day = $(gp).find("#iday option:selected");
		hour = $(gp).find("#itime");
		duration = $(gp).find("#idur");
		instructor = $(gp).find("#fniinstructor option:selected");
		
		if(hour.val() != "" && duration.val() != ""){
			
			
			btn = $("<button>").attr({
				'type':'button',
				'class':'btn btn-danger btn-xs'
				}).text("X");									
				
			line = $("<tr>");
			line.append($("<td>").attr('name', 'cday').attr("id", day.val()).append(day.text()));
			line.append($("<td>").attr('name', 'chour').append(hour.val()));
			line.append($("<td>").attr('name', 'cdur').append(duration.val()));
			line.append($("<td>").attr('name', 'cinstructor').attr("id", instructor.val()).append(instructor.text()));
			line.append($("<td>").append(btn));
			
			tbody = $(gp).find("tbody");
			tbody.append(line);
			
			tbody.parent().show();
			
			$('tbody button').on('click', function() {
				p = $(this).parent();
				gp = $(p).parent();
				
				gp.remove();
			});
		}
		

	});
	
});

function ajaxSelectList() {
	return $.ajax({
		url: "/php/h_class.php",
		type : "post",
		data : { "option" : "selectclasses" },
		datatype: "json" 
	}).done(function(data) {
		var parsed = JSON.parse(data);
		if(parsed!=false){
			$('#iselclass').empty();
			for(o in parsed){
				$('#iselclass').append(
			    	$('<option>').attr('id',parsed[o].id).append(parsed[o].title));
			}
		}
	}).fail(function() {
	});
}

function ajaxSelectProfList(callerid) {
	var obj = "#"+callerid+" #fniinstructor";

	return $.ajax({
		url: "/php/h_trainee.php",
		type : "post",
		data : { "option" : "selectlist",
				 "radio" : "rname",
				 "type" : "Instructor" },
		datatype: "json" 
	}).done(function(dates) {
		var parsed = JSON.parse(dates);
		$(obj).empty();
		for(o in parsed){
			$(obj).append(
		    	$('<option>').attr('value',parsed[o].id).append(parsed[o].title));
		}
				
		
		
	}).fail(function() {
	});
}

function ajaxSelectID(id) {
	return $.ajax({
		url: "/php/h_class.php",
		type : "post",
		data : { "option" : "selectid",
				 "cid" : id },
		datatype: "json" 
	}).done(function(data) {
		var parsed = JSON.parse(data);
		if(parsed!=false){
			$("#iid").text(parsed.id);
			$('#iname').text(parsed.name);
			$('#idtinit').text(parsed.dtini);
			$('#idtend').text(parsed.dtfim);
			
					$("#iperiod").empty();
					for(key in parsed.period){
						line = $("<tr>");
						line.attr("id", parsed.period[key].id);
						line.append($("<td>").attr('name', 'cday').attr("id", parsed.period[key].dayn).append(parsed.period[key].day));
						line.append($("<td>").attr('name', 'chour').append(parsed.period[key].hour));
						line.append($("<td>").attr('name', 'cdur').append(parsed.period[key].duration));
						line.append($("<td>").attr('name', 'cinstructor').append(parsed.period[key].instructor));
						
						$("#iperiod").append(line);
					}
					
			$('#imax').text(parsed.max);
			$('#idesc').text(parsed.desc);				
			$('#istate').prop('checked', parsed.state);

		}
	}).fail(function() {
	});
}

function ajaxDeleteClass(id) {
	return $.ajax({
		url: "/php/h_class.php",
		type : "post",
		data : { "option" : "delete",
				 "cid" : id },
		datatype: "json"
	}).done(function(dates) {
		var response = JSON.parse(dates);
		if(!response.error){
			window.location.reload(true);
		}
	}).fail(function() {
	});
}

function ajaxUpdateState(id) {
	return $.ajax({
		url: "/php/h_class.php",
		type : "post",
		data : { "option" : "editstate",
				 "cid" : id },
		datatype: "json"
	}).done(function(dates) {
		var response = JSON.parse(dates);
		if(response.db==0){
		}
	}).fail(function() {
	});
}

function ajaxDeletePeriod(id) {
	return $.ajax({
		url: "/php/h_class.php",
		type : "post",
		data : { "option" : "deleteperiod",
				 "pid" : id },
		datatype: "json"
	}).done(function(dates) {
		var response = JSON.parse(dates);
	}).fail(function() {
	});
}
