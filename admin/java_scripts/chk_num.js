    	function chk_num(src,float_flag) { //Check Numeric Field	scr=field,float_flag(1= ทศนิยม 2 ตำแหน่ง,อื่นๆไม่มีทศนิยม
		var src_data=src.value;
		if(src_data.length>0) {//มีข้อมูล
			var new_data="";
			var z_flag=false;
			for(var i=0;i<src_data.length;i++) { //Checking Input เช็คทีละตัว
				var ch=src_data.substring(i,i+1);
				if((ch<"0") || (ch>"9")) {
					if(ch == "-" || ch == "+"){
						alert("กรุณาป้อนเป็นจำนวนเต็มบวกไม่มีเครื่องหมาย");
						src.focus();
						src.select();
						return false;
					}	
										
					if(ch != "," && ch != ".") {
						src.focus();
						src.select();
						return false;
					}
					if(ch==".") {
						new_data= new_data + ch;
					}
				}
				else {
					if(ch =="0" && !z_flag) {
						/* Modify 19/062003 by kag*/
						if(src_data.length>1 && i>0) {
							if(src_data.substring(i-1,i)==".") {
								new_data=new_data+ch;
								z_flag=true;
							}
						}
					}
					else {
						new_data= new_data + ch;
						z_flag=true;
					}
				}
			}
			var comma_post=src_data.lastIndexOf(',');
			var point_post= src_data.indexOf('.',0);
			if(point_post == -1) { //Data no point
				if(float_flag ==1) {
					new_data= new_data + ".00";
					var point_post= new_data.indexOf('.',0);
					src.value=Find_Prefix(new_data.substring(0,point_post))+"."+new_data.substring(point_post+1,(new_data.length));
				}
				else {
					src.value=Find_Prefix(new_data);
				}

				return true;
			}
			else {
				var point_post2=src_data.lastIndexOf('.');
				if(point_post != point_post2) {
						src.focus();
						src.select();
					return false;
				}
				if(point_post<comma_post) {
						src.focus();
						src.select();
					return false;
				}
				var point_post= new_data.indexOf('.',0);
			}

			
			//var  b_data=src_data.substring(0,point_post); //Before Float
			if(point_post>=0) {
				var  b_data=new_data.substring(0,point_post); //Before Float
			}
			else {
				var  b_data=new_data; //Before Float
			}
			var tmp=new_data.substring(point_post+1,(new_data.length));
			if(tmp.length<2) {
				if(tmp.length==0) {
					tmp=tmp+ "00";
				}
				else {
					tmp=tmp + "0";
				}
			}
			else if(tmp.length>2) {
				tmp=tmp.substring(0,2);
			}
			//src.value=Find_Prefix(b_data) + "."+new_data.substring(point_post+1,(new_data.length));
			src.value=Find_Prefix(b_data) + "." + tmp;
		} // End if length >0
		return true;
	} //End function chk_num()

	function Find_Prefix(b_data) {
		if(b_data=="") { b_data="0"; }
		var n_div= b_data.length/3;
		if(n_div>1) {
			var n_rem= b_data.length % 3;
			if(n_rem==0) {
					var n_ok= n_div-1;
					var o_data="";
					var del=0;
					while(n_ok>0) {
						var start= (b_data.length)-del;
						var end=(b_data.length)-del-3;
						var tmp= "," + b_data.substring(start,end);
						o_data= tmp + o_data;
						del += 3;
						n_ok--;
						if(n_ok==0) {
							var start= b_data.length-del;
							var end=b_data.length-del-3;
							o_data=b_data.substring(start,end) + o_data;
						}
					}
					return o_data;
			} //End if n_rem
			else { // Remainder 1,2
				var o_data="";
				var del=0;
				var n_ok=Math.floor(n_div);
				while(n_ok>0) {
					var start= b_data.length-del;
					var end=b_data.length-del-3;
					var tmp= "," + b_data.substring(start,end);
					o_data= tmp + o_data;
					del += 3;
					n_ok--;
					if(n_ok==0) {
						o_data=b_data.substring(0,n_rem) + o_data;
					}
				}
				return o_data;				
			}
		}
		else {
			return b_data;
		}
	} //End function Prefix

	function chk_data(d) {
		dd = d.value.substring(0,2); /* dd/mm/yyyy */
		alert("come in chk" + "dd=" + dd);
		/*if (!chk_date(dd,mm,yy)) {
			alert("Error Date!");
			document.FORM1.rtran_date_dd.focus();
			return false;
		} */
		return true;
	}
