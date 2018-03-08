/**
 * @author Tiago Miguel Basilio Silva
 */
function timestringformater(instr) {
	
	var str = instr;
    var arr = str.split(":");
	
	var length = arr.length;
	
	var h = "h ";
	var m = "min";
	var outstr = "";
	
	switch (length) {
		case 3:
			if(arr[0].charAt(0) == "0" && arr[0].charAt(1) != "0") {
				//apagar 0 das horas
				outstr += arr[0].charAt(1);
				outstr += h;
			}
			if(arr[0].charAt(0) != "0") {
				// nao apaga nada das horas
				outstr += arr[0];
				outstr += h;
			}
			if(arr[1].charAt(0) == "0" && arr[1].charAt(1) != "0") {
				//apagar 0 dos minutos
				outstr += arr[1].charAt(1);
				outstr += m;
			}
			if(arr[1].charAt(0) != "0") {
				// noa apaga nada dos minutos
				outstr += arr[1];
				outstr += m;
			}

			break;
		default:
			
			break;
	}
	
	return outstr;
}
