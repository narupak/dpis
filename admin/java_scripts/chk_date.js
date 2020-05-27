/* (dd/mm/yyyy) หรือ (dd-mm-yyyy)   วิธีเรียกใช้ chk_date($tdate,lang) */

/*ปีพ.ศ. (dd/mm/yyyy) หรือ (dd-mm-yyyy)*/
function chk_date(tdate, lang) { // ปีพ.ศ.
	  var  ldd = tdate.value.substring(0,2); /* dd/mm/yyyy */
	  var  lmm = tdate.value.substring(3,5); /* dd/mm/yyyy */
	  var  lyy = tdate.value.substring(6,10); /* dd/mm/yyyy */
	 var   lslash1 = tdate.value.substring(2,3); /* dd/mm/yyyy */
	  var  lslash2 = tdate.value.substring(5,6); /* dd/mm/yyyy */
//	var ldd= dd.value;
//	var lmm= mm.value;
//	var lyy= yy.value;
	 if (lang=="BDHZERO"){	// ให้วัน หรือ เดือน เป็น 00 ได้
		if(ldd=="" && lmm=="" && lyy==""){
			return true;	
		} else if (!((lslash1 =="/"||lslash1 =="-" )&&(lslash2 =="/"|| lslash2 =="-" )&&tdate.value.length==10)){
			alert("กรุณาป้อนให้ครบและถูกต้องตามรูปแบบ");
			tdate.value="";
			tdate.focus();
			return false; 
		}else{	
			if(lyy=="" || lyy<1000) {		//เช็คเฉพาะปีว่าต้องระบุ
				alert("กรุณาป้อนปีให้ถูกต้อง");
                                tdate.value=''; /*Release 5.2.1.8*/
				tdate.focus();
				return false;
			}
		}
	}else if (lang=="BDH"){
		if(ldd=="" && lmm=="" && lyy==""){
			return true;	
		} else if (!((lslash1 =="/"||lslash1 =="-" )&&(lslash2 =="/"|| lslash2 =="-" )&&tdate.value.length==10)){
			alert("กรุณาป้อนให้ครบและถูกต้องตามรูปแบบ");
			tdate.value="";
			tdate.focus();
			return false; 
		}else{	
			if(!(tdate.value.substring(0,1)>="0"&&tdate.value.substring(0,1)<="9"&&
				 tdate.value.substring(1,2)>="0"&&tdate.value.substring(1,2)<="9"&&
				  ldd>=1&&ldd<=31)){
				alert("กรุณาป้อนวันให้ถูกต้อง");
                                tdate.value=''; /*Release 5.2.1.8*/
				tdate.focus();
				return false;
			}		
			if(!(tdate.value.substring(3,4)>="0"&&tdate.value.substring(3,4)<="9"&&
				 tdate.value.substring(4,5)>="0"&&tdate.value.substring(4,5)<="9"&&
				  lmm>=1&& lmm <=12)){
				alert("กรุณาป้อนเดือนให้ถูกต้อง");
                                tdate.value=''; /*Release 5.2.1.8*/
				tdate.focus();
				return false;
			}
		//	else {
		//		if(lmm.length<2) { tdate.value="0"+mm.value; }
		//	}
			if(lyy=="" || lyy<1000) {
				alert("กรุณาป้อนปีให้ถูกต้อง");
                                tdate.value=''; /*Release 5.2.1.8*/
				tdate.focus();
				return false;
			}
			if(lmm==01 || lmm==03|| lmm==05 || lmm==07 ||
			   lmm==08 || lmm==10 || lmm==12) {			
				if(!(ldd>0 && ldd<=31)) {
				   alert("กรุณาป้อนวันที่ให้อยู่ในเดือนที่ป้อน");
                                   tdate.value=''; /*Release 5.2.1.8*/
				   tdate.focus();
				   return false;
				}
		//		else {
		//			if(ldd.length<2) { dd.value="0"+dd.value; }
		//		}
			}
			else if(lmm==04 || lmm==06 || lmm==09 || lmm==11) {
				if(!(ldd>0 && ldd<=30)) {
					alert("กรุณาป้อนวันที่ให้อยู่ในเดือนที่ป้อน");
                                        tdate.value=''; /*Release 5.2.1.8*/
					tdate.focus();
					return false;
				}
			//	else {
			//		if(ldd.length<2) { dd.value="0"+dd.value; }
			//	}
			}
			else if(lmm==02) {
					lyy -= 543;
				var leap_yy=false;
				if(lyy % 4 ==0 && lyy % 100 ==0) {
					if(lyy % 400 ==0) {
						leap_yy=true;
					}				
				}
				else if(lyy % 4 ==0) {
					leap_yy=true;
				}			
				if(!(ldd>0 && ldd<=29)) {
					alert("กรุณาป้อนวันที่ให้อยู่ในเดือนที่ป้อน");
                                        tdate.value=''; /*Release 5.2.1.8*/
					tdate.focus();
					return false;
				}
				else if(ldd==29) {
					if(!leap_yy) {
						alert("กรุณาป้อนวันที่ให้อยู่ในเดือนที่ป้อน");
                                                tdate.value=''; /*Release 5.2.1.8*/
						tdate.focus();
						return false;
					}
				}
		//		else if(ldd.length<2) { dd.value="0"+dd.value; }
			} //End if MM=02
		}//end not null
	}else {  //end if (lang=="BDH")	/*ปีค.ศ. (dd/mm/yyyy) หรือ (dd-mm-yyyy)*/
		if(ldd=="" && lmm=="" && lyy==""){
			return true;	
		}else if (!((lslash1 =="/"||lslash1 =="-" )&&(lslash2 =="/"|| lslash2 =="-" )&&tdate.value.length==10)){
			alert("Invalid format date!");
                        tdate.value=''; /*Release 5.2.1.8*/
			tdate.focus();
			return false; }
		 else{		
			if(!(tdate.value.substring(0,1)>="0"&&tdate.value.substring(0,1)<="9"&&
				 tdate.value.substring(1,2)>="0"&&tdate.value.substring(1,2)<="9"&&
				  ldd>=1&&ldd<=31)){
				alert("Invaild date!");
                                tdate.value=''; /*Release 5.2.1.8*/
				tdate.focus();
				return false;
			}		
			if(!(tdate.value.substring(3,4)>="0"&&tdate.value.substring(3,4)<="9"&&
				 tdate.value.substring(4,5)>="0"&&tdate.value.substring(4,5)<="9"&&
				  lmm>=1&& lmm <=12)){
				alert("Invalid Month!");
                                tdate.value=''; /*Release 5.2.1.8*/
				tdate.focus();
				return false;
			}
		//	else {
		//		if(lmm.length<2) { tdate.value="0"+mm.value; }
		//	}
			if(lyy=="" || lyy<1000) {
				alert("Invalid year!");
                                tdate.value=''; /*Release 5.2.1.8*/
				tdate.focus();
				return false;
			}
			if(lmm==01 || lmm==03|| lmm==05 || lmm==07 ||
			   lmm==08 || lmm==10 || lmm==12) {			
				if(!(ldd>0 && ldd<=31)) {
				   alert("Day of month must be between 1 and last day of month!");
                                   tdate.value=''; /*Release 5.2.1.8*/
				   tdate.focus();
				   return false;
				}
		//		else {
		//			if(ldd.length<2) { dd.value="0"+dd.value; }
		//		}
			}
			else if(lmm==04 || lmm==06 || lmm==09 || lmm==11) {
				if(!(ldd>0 && ldd<=30)) {
					alert("Day of month must be between 1 and last day of month!");
                                        tdate.value=''; /*Release 5.2.1.8*/
					tdate.focus();
					return false;
				}
			//	else {
			//		if(ldd.length<2) { dd.value="0"+dd.value; }
			//	}
			}
			else if(lmm==02) {
			//        lyy -= 543;
				var leap_yy=false;
				if(lyy % 4 ==0 && lyy % 100 ==0) {
					if(lyy % 400 ==0) {
						leap_yy=true;
					}				
				}
				else if(lyy % 4 ==0) {
					leap_yy=true;
				}			
				if(!(ldd>0 && ldd<=29)) {
					alert("Day of month must be between 1 and last day of month!");
                                        tdate.value=''; /*Release 5.2.1.8*/
					tdate.focus();
					return false;
				}
				else if(ldd==29) {
					if(!leap_yy) {
						alert("Day of month must be between 1 and last day of month!");
                                                tdate.value=''; /*Release 5.2.1.8*/
						tdate.focus();
						return false;
					}
				}
		//		else if(ldd.length<2) { dd.value="0"+dd.value; }
			} //End if MM=02
		}//end not null
	} //end else
return true;
} //End function chk_date()

/* (00:00:00 = hh24:mi:ss)  วิธีเรียกใช้ chk_time($ttime) 
	(between 00:00:00 and 23:59:59) */

function chk_time(ttime) { 
	var  lhh = ttime.value.substring(0,2); 
	var  lmi = ttime.value.substring(3,5); 
	var   lslash1 = ttime.value.substring(2,3); 
	if (lhh=="" && lmi==""){
		return true;	
	}else	if (!((lslash1 ==":" || lslash1 ==".")&&ttime.value.length==5)){
		alert("กรุณาป้อนให้ครบและถูกต้องตามรูปแบบ (00:00)");
		ttime.focus();
		return false; 
	}else{		
		if (!(lhh>=0&&lhh<=23)){
			alert("กรุณาป้อนชม.ให้ถูกต้อง");
			ttime.focus();
			return false;
		}		
		if (!(lmi>=0&&lmi<=59)){
			alert("กรุณาป้อนนาทีให้ถูกต้อง");
			ttime.focus();
			return false;
		}
		}//if
    ttime.value= lhh+":"+lmi;    
	return true;
} //End function chk_time()

<!--
// Scripts for days between dates
// Modified by Kansak 20060328
// The Javascript Date object is buggy, so a custom date object is included.

var df = 1; //date format mm/dd/yyyy
    
//<<**Custom Date object
//Thanks to Claus T๘ndering for the Julian day algorithm
//http://www.tondering.dk/claus/calendar.html
var GREGORIAN = 0;
var JULIAN = 1;
var year = 0;
var month = 0;
var day = 0;
var julianday = 0.0;
var modifiedjulianday = 0.0;

function ipart(r){ return Math.floor(r); }
function getJulianDay(){ return this.julianday; } 
function getModifiedJulianDay(){ return this.modifiedjulianday; }

function CustomDate(yr, mo, da, type){
  year = yr * 1.0;  //convert string to float
  if (year < -4713 || year > 3268){
//	alert("Year out of range");
	alert("ปีอยู่นอกเขตการคำนวณ");
    return false;
  }
  month = mo * 1.0;
  day = da * 1.0;
  if (year == 1582 && month == 10 && day > 4 && day < 15){
//    alert("Invalid date: 15 Oct immediately followed 4 Oct in the year 1582");
    alert("วันที่ไม่ถูกต้อง : ในปี พ.ศ.2125 วันที่ 15 ต.ค. จะต่อจากวันที่ 4 ต.ค. ทันที");
    return false;
  }
  if (year < 0) year = year + 1; //B.C. date correction
  
  var a = ipart((14 - month) / 12);
  var y = year + 4800 -a;
  var m = month + (12 * a) - 3;
  if (type == GREGORIAN){
    julianday = day + ipart(((153*m) + 2)/5) + y*365 + ipart(y/4) - ipart(y/100) + ipart(y/400) - 32045;
//	alert("GREGORIAN :: " + julianday);
 }
  if (type == JULIAN){
    julianday = day + ipart(((153*m) + 2)/5) + y*365 + ipart(y/4) - 32083;
//	alert("JULIAN :: " + julianday);
  }
  modifiedjulianday = julianday - 2400000.5; //Zero at 17 Nov 1858 00:00:00 UTC
  this.getJulianDay = getJulianDay();
  this.getModifiedJulianDay = getModifiedJulianDay();
}
//CustomDate**>>

function fix2DigitDate(dateval){
  var date = dateval + "" //dateval must be a string
  if (date.length < 3){ 
    date = 1900 + date * 1.0
	date = date + ""  //to string
  }
  return date
}

function parseDate(dateval,eraval){
	//split is a Javascript 1.2 function
	var dary=dateval.split("/")
    var era;
	eraval > 0 ? era = -1: era = 1
	var y = fix2DigitDate(dary[2]) * era
	switch (df){
	  case 1: { m = dary[0]; d = dary[1] } // mm/dd/yyyy
	  break;
	  case 2: { m = dary[1]; d = dary[0] } // dd/mm/yyyy
	  break;
	  default: { m = dary[0]; d = dary[1] }
	}
	var calendar
	if (y > 1582) calendar = GREGORIAN
	  else if (y < 1582) calendar = JULIAN
	     else if (m < 10 | (m == 10 && d < 15)) calendar = JULIAN
	        else calendar = GREGORIAN 
	i = new CustomDate(y,m,d,calendar)
	return i
}//dateval

//Calculate days between dates	
function calcDBD(date1, date2){
    var err
	// define era  0 = A.D.	1 = B.C.
	var era1 = 0;							
	var era2 = 0;

	err = checkdate(date1,era1);
	if (err == 1) return false;
	err = checkdate(date2,era2);
	if (err == 1) return false;

	firstdate = parseDate(date1,era1);
	seconddate = parseDate(date2,era2);
	dbd = seconddate.getJulianDay - firstdate.getJulianDay;
//	document.form1.<?=$arr_fields[4]?>.value = dbd.toString();
	if(isNaN(dbd))	 return dbd.toString();
	else return (parseInt(dbd, 10) + 1);
}//calcDBD

//Validate the dates
//Heavily modified from a script by Mattias Sjsberg 11-28-96
function checkdate(date,era) {
    var err = 0
	var valid = "0123456789/"
	//var ok = "yes"
	var temp;
	if (date == null || date.length < 1) err = 1 //is there a date?
	//check for invalid characters
	for (var i=0; i< date.length; i++) {
	  temp = "" + date.substring(i, i+1)
	  if (valid.indexOf(temp) == "-1") err = 1
	  }
	//split is Javascript 1.2
	dary=date.split("/")
	if (df == 1){
	  b = parseInt(dary[0], 10); 	//month
	  d = parseInt(dary[1], 10);		//day
    }
	if (df == 2){
	  b = parseInt(dary[1], 10);		//month
	  d = parseInt(dary[0], 10); 	//day
	}
	f = parseInt(dary[2]); 				//year
	
    if (err != 1 && f.length < 3) { //one or two digit date
	  f = fix2DigitDate(f) //20th century
	  if (era != null && era == 1) { 
	    err = 1;  //BC years must be 4 digits
		date = date + " B.C."; 
	  }
	}
	if (b<1 || b>12) err = 1
	if (d<1 || d>31) err = 1
	if (f<0 || f>9999) err = 1
	if (b==4 || b==6 || b==9 || b==11){
	  if (d == 31) err=1
	}
	if (b==2){  //leap year checking
	  var g=parseInt(f/4)
	  if (isNaN(g)) err=1
	  if (d>29) err=1
	  if (d==29){
	    //leap years are always divisible by 4
	    if ( (f % 4) != 0) err = 1;
		//in the Gregorian calendar century years are not leap years unless divisible by 400
        if (f > 1582) {
		  if (((f % 100) == 0) && ((f % 400) != 0)) err = 1;
		}
	  }
	}
	if (err==1) {
	  date = d + "/" + b + "/" + (f + 543)
	  alert("วันที่นี้ ("+ date +") ถูกต้องหรือไม่?");
	} else {
	  //alert('Valid date!');
  	}
	return err
}//checkdate
// -->

<!-- My Calculate Days Between Date Function
// Written by Kansak 20060328
// no error check
// if want to use with error check , please use calcDBD
// date must be in format mm/dd/yyyy

	function myDBD(date1, date2){
		var arrTmp = date1.split("/");
		var start_year = arrTmp[2];
		var start_month = arrTmp[0];
		var start_date = arrTmp[1];

		var arrTmp = date2.split("/");
		var end_year = arrTmp[2];
		var end_month = arrTmp[0];
		var end_date = arrTmp[1];

		var day1 = dayFromNewYear(parseInt(start_year, 10), parseInt(start_month, 10), parseInt(start_date, 10));
//		alert("day1 (" + parseInt(start_date, 10) + "/" + parseInt(start_month, 10) + "/" + parseInt(start_year, 10) + ") = " + day1);
		var day2 = dayFromNewYear(parseInt(end_year, 10), parseInt(end_month, 10), parseInt(end_date, 10));
//		alert("day2 (" + parseInt(end_date, 10) + "/" + parseInt(end_month, 10) + "/" + parseInt(end_year, 10) + ") = " + day2);

		var dayNum = 0;
		if(start_year == end_year){
			dayNum += (day2 - day1) + 1;
//			alert(day1 + " : " + day2);
		}else{
			dayNum += dayOfYear(parseInt(start_year, 10)) - day1 + 1;
			dayNum += day2;
			if((end_year - start_year) >= 2){
				for(var i=(start_year+1); i<end_year; i++){
					if(isLeapYear(i)) dayNum += 366;
					else dayNum += 365;
				} // end for
			} // end if
		} // end if
		
		return dayNum;
	} // myDBD
	
	function dayFromNewYear(year, month, date){
		var dayNum = 0;
		for(var i=1; i<month; i++){
			switch(i){
				case 2 :
					if(isLeapYear(year)) dayNum += 29;
					else dayNum += 28;
					break;
				case 4 :
				case 6 :
				case 9 :
				case 11 :
					dayNum += 30;
					break;
				default :
					dayNum += 31;
			} // end switch
		} // end for
		
		dayNum += date;
		return dayNum;
	} // dayFromNewYear
	
	function dayOfYear(year){
		var dayNum = 365;
		if(isLeapYear(year)) dayNum = 366;
		return dayNum;
	} // dayOfYear
	
	function isLeapYear(year){
		if((year % 4) == 0){
			if((year % 100) != 0){
				return true;
			}else{ 
				if((year % 400) == 0){
					return true;
				}else{
					return false;
				} // end if
			} // end if	
		}else{
			return false;
		} // end if
	} // isLeapYear
// -->