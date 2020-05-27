/*  Copyright Mihai Bazon, 2002  |  http://students.infoiasi.ro/~mishoo
 * ---------------------------------------------------------------------
 *
 * The DHTML Calendar, version 0.9.2 "The art of date selection"
 *
 * Details and latest version at:
 * http://students.infoiasi.ro/~mishoo/site/calendar.epl
 *
 * Feel free to use this script under the terms of the GNU Lesser General
 * Public License, as long as you do not remove or alter this notice.
 */

// $Id: calendar.js,v 1.7 2003/02/13 11:19:45 mbazon Exp $

function IDGenerator (nextID)
{
	this.nextID = nextID;
	this.GenerateID = IDGeneratorGenerateID;
}
function IDGeneratorGenerateID () {return this.nextID++;}
var oldLink = null;
var MINUTE = 60 * 1000;
var HOUR = 60 * MINUTE;
var DAY = 24 * HOUR;
var WEEK = 7 * DAY;
function setActiveStyleSheet (link, title)
{
	var i, a, main;
	for (i=0; (a = document.getElementsByTagName("link")[i]); i++)
	{
		if (a.getAttribute("rel").indexOf("style") != -1 && a.getAttribute("title"))
		{
			a.disabled = true;
			if(a.getAttribute("title") == title) a.disabled = false;
		}
	}
	if (oldLink) oldLink.style.fontWeight = 'normal';
	oldLink = link;
	link.style.fontWeight = 'bold';
	return false;
}
// This function gets called when the end-user clicks on some date.
function selected (cal, date)
{
	cal.sel.value = date;// just update the date in the input field.
	//keng
	cal.callCloseHandler();
/*
	if (cal.sel.id == "sel1" || cal.sel.id == "sel2" || cal.sel.id == "sel3" || 
		cal.sel.id == "sel4"|| cal.sel.id == "sel5" || cal.sel.id == "sel6"|| 
		cal.sel.id == "sel7"|| cal.sel.id == "sel8"|| cal.sel.id == "sel9"|| cal.sel.id == "sel10")
	// if we add this call we close the calendar on single-click.
	// just to exemplify both cases, we are using this only for the 1st
	// and the 3rd field, while 2nd and 4th will still require double-click.
	cal.callCloseHandler();
*/	
}
// And this gets called when the end-user clicks on the _selected_ date,
// or clicks on the "Close" button.  It just hides the calendar without
// destroying it.
function closeHandler (cal)
{
   //keng
   	if (cal.sel.onchange)
		cal.sel.onchange();
	cal.hide();// hide the calendar
}
// This function shows the calendar under the element having the given id.
// It takes care of catching "mousedown" signals on document and hiding the
// calendar if the click was outside.	
// holiday=�ش�ͧ�ѹ�Ӥѭ�ҡ database, f_no_weekholi=�������������� check ��͹�ѹ��ش�������
function showCalendar (id, format, holiday, f_no_weekholi, f_CYear)
{
	var el = document.getElementById(id);
//	alert("el=" + el.value);

	if (calendar != null)
	{
//		alert("holiday:"+holiday);
		if (holiday && holiday != "undefined") {
			var arr_buff = holiday.split("&");
			for(i = 0; i < arr_buff.length; i++) {
				buff = arr_buff[i].split("|");
				df = buff[0].split("-");	// format from holiday database is yyyy-mm-dd
				hdate = new Date(Number(df[0]), Number(df[1])-1, Number(df[2])); // �ҡ database �� java month ��ͧ -1
				hdatestr = hdate.getFullYear()+"-"+hdate.getMonth()+"-"+hdate.getDate();
				calendar.holiday_date.push(hdatestr);	// array of holiday date
				calendar.holiday_name.push(buff[1]);	// array of holiday name
			}
		} else {
			calendar.holiday_date = new Array();
			calendar.holiday_name = new Array();
		}
		if (f_no_weekholi == undefined) calendar.f_no_weekholi = ""; else calendar.f_no_weekholi = f_no_weekholi;
	
//		alert("1..holiday:"+calendar.holiday_date.toString()+", f_no_weekholi:"+calendar.f_no_weekholi);
		// we already have some calendar created
		calendar.hide();// so we hide it first.
	}
	else
	{
		// first-time call, create the calendar.
		var cal = new Calendar(false, null, selected, closeHandler, f_CYear);
		// uncomment the following line to hide the week numbers
		cal.weekNumbers = false;
		calendar = cal;// remember it in the global var
		cal.setRange(1900, 2070);// min/max year allowed.
//		alert("holiday:"+holiday);
		if (holiday && holiday != "undefined") {
			var arr_buff = holiday.split("&");
			for(i = 0; i < arr_buff.length; i++) {
				buff = arr_buff[i].split("|");
				df = buff[0].split("-");	// format from holiday database is yyyy-mm-dd
				hdate = new Date(Number(df[0]), Number(df[1])-1, Number(df[2])); // �ҡ database �� java month ��ͧ -1
				hdatestr = hdate.getFullYear()+"-"+hdate.getMonth()+"-"+hdate.getDate();
				cal.holiday_date.push(hdatestr);	// array of holiday date
//				cal.holiday_name.push(buff[1]);	// array of holiday name
				cal.holiday_name.push("��ش�Ҫ���");	// array of holiday name
			}
		} else {
			cal.holiday_date = new Array();
			cal.holiday_name = new Array();
		}
		if (f_no_weekholi == undefined) cal.f_no_weekholi = ""; else cal.f_no_weekholi = f_no_weekholi;

//		alert("2..holiday:"+cal.holiday_date.toString()+", f_no_weekholi:"+cal.f_no_weekholi);
		cal.create();
	}
	calendar.setDateFormat(format);// set the specified date format
	calendar.parseDate(el.value);// try to parse the text in field
	calendar.sel = el;// inform it what input field we use
	
	calendar.showAtElement(el);// show the calendar below it	
	return false;
}
// If this handler returns true then the "date" given as
// parameter will be disabled.  In this example we enable
// only days within a range of 10 days from the current
// date.
// You can use the functions date.getFullYear() -- returns the year
// as 4 digit number, date.getMonth() -- returns the month as 0..11,
// and date.getDate() -- returns the date of the month as 1..31, to
// make heavy calculations here.  However, beware that this function
// should be very fast, as it is called for each day in a month when
// the calendar is (re)constructed.
function isDisabled (date)
{
	var today = new Date();
	return (Math.abs(date.getTime() - today.getTime()) / DAY) > 10;
}
function flatSelected (cal, date)
{
	var el = document.getElementById("preview");
	//alert("come in flatSelected,cal=" +cal + ",date="+date);
	el.innerHTML = date;
}
//--- end script ---


/** The Calendar object constructor. */
Calendar = function (mondayFirst, dateStr, onSelected, onClose, f_CYear) {
	// member variables
	this.activeDiv = null;
	this.currentDateEl = null;
	this.checkDisabled = null;
	this.timeout = null;
	this.onSelected = onSelected || null;
	this.onClose = onClose || null;
	this.dragging = false;
	this.hidden = false;
	this.minYear = 1970;
	this.maxYear = 2050;
	this.dateFormat = Calendar._TT["DEF_DATE_FORMAT"];
	this.ttDateFormat = Calendar._TT["TT_DATE_FORMAT"];
	this.isPopup = true;
	this.weekNumbers = true;
	this.mondayFirst = mondayFirst;
	this.dateStr = dateStr;
	this.ar_days = null;
	// HTML elements
	this.table = null;
	this.element = null;
	this.tbody = null;
	this.firstdayname = null;
	// Combo boxes
	this.monthsCombo = null;
	this.yearsCombo = null;
	this.hilitedMonth = null;
	this.activeMonth = null;
	this.hilitedYear = null;
	this.activeYear = null;
	this.holiday_date = new Array();
	this.holiday_name = new Array();
	this.f_no_weekholi = "";
	this.f_CYear = (f_CYear ? f_CYear : 0);

	// one-time initializations
	if (!Calendar._DN3) {
		// table of short day names
		var ar = new Array();
		for (var i = 8; i > 0;) {
			ar[--i] = Calendar._DN[i].substr(0, 3);
		}
		Calendar._DN3 = ar;
		// table of short month names
		ar = new Array();
		for (var i = 12; i > 0;) {
			ar[--i] = Calendar._MN[i].substr(0, 3);
		}
		Calendar._MN3 = ar;
	}
};

// ** constants

/// "static", needed for event handlers.
Calendar._C = null;

/// detect a special case of "web browser"
Calendar.is_ie = ( (navigator.userAgent.toLowerCase().indexOf("msie") != -1) &&
		   (navigator.userAgent.toLowerCase().indexOf("opera") == -1) );

// short day names array (initialized at first constructor call)
Calendar._DN3 = null;

// short month names array (initialized at first constructor call)
Calendar._MN3 = null;

// BEGIN: UTILITY FUNCTIONS; beware that these might be moved into a separate
//        library, at some point.

Calendar.getAbsolutePos = function(el) {
	var r = { x: el.offsetLeft, y: el.offsetTop };
	if (el.offsetParent) {
		var tmp = Calendar.getAbsolutePos(el.offsetParent);
		r.x += tmp.x;
		r.y += tmp.y;
	}
	return r;
};

Calendar.isRelated = function (el, evt) {
	var related = evt.relatedTarget;
	if (!related) {
		var type = evt.type;
		if (type == "mouseover") {
			related = evt.fromElement;
		} else if (type == "mouseout") {
			related = evt.toElement;
		}
	}
	while (related) {
		if (related == el) {
			return true;
		}
		related = related.parentNode;
	}
	return false;
};

Calendar.removeClass = function(el, className) {
	if (!(el && el.className)) {
		return;
	}
	var cls = el.className.split(" ");
	var ar = new Array();
	for (var i = cls.length; i > 0;) {
		if (cls[--i] != className) {
			ar[ar.length] = cls[i];
		}
	}
	el.className = ar.join(" ");
};

Calendar.addClass = function(el, className) {
	Calendar.removeClass(el, className);
	el.className += " " + className;
};

Calendar.getElement = function(ev) {
	if (Calendar.is_ie) {
		return window.event.srcElement;
	} else {
		return ev.currentTarget;
	}
};

Calendar.getTargetElement = function(ev) {
	if (Calendar.is_ie) {
		return window.event.srcElement;
	} else {
		return ev.target;
	}
};

Calendar.stopEvent = function(ev) {
	if (Calendar.is_ie) {
		window.event.cancelBubble = true;
		window.event.returnValue = false;
	} else {
		ev.preventDefault();
		ev.stopPropagation();
	}
};

Calendar.addEvent = function(el, evname, func) {
	if (Calendar.is_ie) {
		el.attachEvent("on" + evname, func);
	} else {
		el.addEventListener(evname, func, true);
	}
};

Calendar.removeEvent = function(el, evname, func) {
	if (Calendar.is_ie) {
		el.detachEvent("on" + evname, func);
	} else {
		el.removeEventListener(evname, func, true);
	}
};

Calendar.createElement = function(type, parent) {
	var el = null;
	if (document.createElementNS) {
		// use the XHTML namespace; IE won't normally get here unless
		// _they_ "fix" the DOM2 implementation.
		el = document.createElementNS("http://www.w3.org/1999/xhtml", type);
	} else {
		el = document.createElement(type);
	}
	if (typeof parent != "undefined") {
		parent.appendChild(el);
	}
	return el;
};

// END: UTILITY FUNCTIONS

// BEGIN: CALENDAR STATIC FUNCTIONS

/** Internal -- adds a set of events to make some element behave like a button. */
Calendar._add_evs = function(el) {
	with (Calendar) {
		addEvent(el, "mouseover", dayMouseOver);
		addEvent(el, "mousedown", dayMouseDown);
		addEvent(el, "mouseout", dayMouseOut);
		if (is_ie) {
			addEvent(el, "dblclick", dayMouseDblClick);
			el.setAttribute("unselectable", true);
		}
	}
};

Calendar.findMonth = function(el) {
	if (typeof el.month != "undefined") {
		return el;
	} else if (typeof el.parentNode.month != "undefined") {
		return el.parentNode;
	}
	return null;
};

Calendar.findYear = function(el) {
	if (typeof el.year != "undefined") {
		return el;
	} else if (typeof el.parentNode.year != "undefined") {
		return el.parentNode;
	}
	return null;
};

Calendar.showMonthsCombo = function () {
	var cal = Calendar._C;
	if (!cal) {
		return false;
	}
	var cal = cal;
	var cd = cal.activeDiv;
	var mc = cal.monthsCombo;

	if (cal.hilitedMonth) {
		Calendar.removeClass(cal.hilitedMonth, "hilite");
	}
	if (cal.activeMonth) {
		Calendar.removeClass(cal.activeMonth, "active");
	}
	var mon = cal.monthsCombo.getElementsByTagName("div")[cal.date.getMonth()];
	Calendar.addClass(mon, "active");
	cal.activeMonth = mon;
	mc.style.left = cd.offsetLeft + "px";
	mc.style.top = (cd.offsetTop + cd.offsetHeight) + "px";
	mc.style.display = "block";
};

Calendar.showYearsCombo = function (fwd) {
	var cal = Calendar._C;
	if (!cal) {
		return false;
	}
	var cal = cal;
	var cd = cal.activeDiv;
	var yc = cal.yearsCombo;
	if (cal.hilitedYear) {
		Calendar.removeClass(cal.hilitedYear, "hilite");
	}
	if (cal.activeYear) {
		Calendar.removeClass(cal.activeYear, "active");
	}
	cal.activeYear = null;
//	alert("year="+cal.date.getFullYear()+", fwd="+fwd);
	var Y = cal.date.getFullYear() + (fwd ? 1 : -1);
	var yr = yc.firstChild;
	var show = false;
	for (var i = 12; i > 0; --i) {
		if (Y >= cal.minYear && Y <= cal.maxYear) {
			yr.firstChild.data = Y+(cal.f_CYear==1 ? 0 : 543);
			yr.year = Y;
			yr.style.display = "block";
			show = true;
		} else {
			yr.style.display = "none";
		}
		yr = yr.nextSibling;
		Y += fwd ? 2 : -2;
	}
	if (show) {
		yc.style.left = cd.offsetLeft + "px";
		yc.style.top = (cd.offsetTop + cd.offsetHeight) + "px";
		yc.style.display = "block";
	}
};

// event handlers

Calendar.tableMouseUp = function(ev) {
	var cal = Calendar._C;
	if (!cal) {
		return false;
	}
	if (cal.timeout) {
		clearTimeout(cal.timeout);
	}
	var el = cal.activeDiv;
	if (!el) {
		return false;
	}
	var target = Calendar.getTargetElement(ev);
	Calendar.removeClass(el, "active");
	if (target == el || target.parentNode == el) {
		Calendar.cellClick(el);
	}
	var mon = Calendar.findMonth(target);
	var date = null;
	if (mon) {
		date = new Date(cal.date);
		if (mon.month != date.getMonth()) {
			date.setMonth(mon.month);
			cal.setDate(date);
		}
	} else {		
		var year = Calendar.findYear(target);
		if (year) {
			date = new Date(cal.date);
			if (year.year != date.getFullYear()) {
				date.setFullYear(year.year);
				cal.setDate(date);
			}
		}
	}
	with (Calendar) {
		removeEvent(document, "mouseup", tableMouseUp);
		removeEvent(document, "mouseover", tableMouseOver);
		removeEvent(document, "mousemove", tableMouseOver);
		cal._hideCombos();
		stopEvent(ev);
		_C = null;
	}
};

Calendar.tableMouseOver = function (ev) {
	var cal = Calendar._C;
	if (!cal) {
		return;
	}
	var el = cal.activeDiv;
	var target = Calendar.getTargetElement(ev);
	if (target == el || target.parentNode == el) {
		Calendar.addClass(el, "hilite active");
		Calendar.addClass(el.parentNode, "rowhilite");
	} else {
		Calendar.removeClass(el, "active");
		Calendar.removeClass(el, "hilite");
		Calendar.removeClass(el.parentNode, "rowhilite");
	}
	var mon = Calendar.findMonth(target);
	if (mon) {
		if (mon.month != cal.date.getMonth()) {
			if (cal.hilitedMonth) {
				Calendar.removeClass(cal.hilitedMonth, "hilite");
			}
			Calendar.addClass(mon, "hilite");
			cal.hilitedMonth = mon;
		} else if (cal.hilitedMonth) {
			Calendar.removeClass(cal.hilitedMonth, "hilite");
		}
	} else {
		var year = Calendar.findYear(target);
		if (year) {
			if (year.year != cal.date.getFullYear()) {
				if (cal.hilitedYear) {
					Calendar.removeClass(cal.hilitedYear, "hilite");
				}
				Calendar.addClass(year, "hilite");
				cal.hilitedYear = year;
			} else if (cal.hilitedYear) {
				Calendar.removeClass(cal.hilitedYear, "hilite");
			}
		}
	}
	Calendar.stopEvent(ev);
};

Calendar.tableMouseDown = function (ev) {
	if (Calendar.getTargetElement(ev) == Calendar.getElement(ev)) {
		Calendar.stopEvent(ev);
	}
};

Calendar.calDragIt = function (ev) {
	var cal = Calendar._C;
	if (!(cal && cal.dragging)) {
		return false;
	}
	var posX;
	var posY;
	if (Calendar.is_ie) {
		posY = window.event.clientY + document.body.scrollTop;
		posX = window.event.clientX + document.body.scrollLeft;
	} else {
		posX = ev.pageX;
		posY = ev.pageY;
	}
	cal.hideShowCovered();
	var st = cal.element.style;
	st.left = (posX - cal.xOffs) + "px";
	st.top = (posY - cal.yOffs) + "px";
	Calendar.stopEvent(ev);
};

Calendar.calDragEnd = function (ev) {
	var cal = Calendar._C;
	if (!cal) {
		return false;
	}
	cal.dragging = false;
	with (Calendar) {
		removeEvent(document, "mousemove", calDragIt);
		removeEvent(document, "mouseover", stopEvent);
		removeEvent(document, "mouseup", calDragEnd);
		tableMouseUp(ev);
	}
	cal.hideShowCovered();
};

Calendar.dayMouseDown = function(ev) {
	var el = Calendar.getElement(ev);
	if (el.disabled) {
		return false;
	}
	var cal = el.calendar;
	cal.activeDiv = el;
	Calendar._C = cal;
	if (el.navtype != 300) with (Calendar) {
		addClass(el, "hilite active");
		addEvent(document, "mouseover", tableMouseOver);
		addEvent(document, "mousemove", tableMouseOver);
		addEvent(document, "mouseup", tableMouseUp);
	} else if (cal.isPopup) {
		cal._dragStart(ev);
	}
	Calendar.stopEvent(ev);
	if (el.navtype == -1 || el.navtype == 1) {
		cal.timeout = setTimeout("Calendar.showMonthsCombo()", 250);
	} else if (el.navtype == -2 || el.navtype == 2) {
		cal.timeout = setTimeout((el.navtype > 0) ? "Calendar.showYearsCombo(true)" : "Calendar.showYearsCombo(false)", 250);
	} else {
		cal.timeout = null;
	}
};

Calendar.dayMouseDblClick = function(ev) {
	Calendar.cellClick(Calendar.getElement(ev));
	if (Calendar.is_ie) {
		document.selection.empty();
	}
};

Calendar.dayMouseOver = function(ev) {
	var el = Calendar.getElement(ev);
	if (Calendar.isRelated(el, ev) || Calendar._C || el.disabled) {
		return false;
	}
	if (el.ttip) {
		if (el.ttip.substr(0, 1) == "_") {
			var date = null;
			with (el.calendar.date) {
				date = new Date(getFullYear(), getMonth(), el.caldate);
			}
			el.ttip = date.print(el.calendar.ttDateFormat) + el.ttip.substr(1);
		}
		el.calendar.tooltips.firstChild.data = el.ttip;
	}
	if (el.navtype != 300) {
		Calendar.addClass(el, "hilite");
		if (el.caldate) {
			Calendar.addClass(el.parentNode, "rowhilite");
		}
	}
	Calendar.stopEvent(ev);
};

Calendar.dayMouseOut = function(ev) {
	with (Calendar) {
		var el = getElement(ev);
		if (isRelated(el, ev) || _C || el.disabled) {
			return false;
		}
		removeClass(el, "hilite");
		if (el.caldate) {
			removeClass(el.parentNode, "rowhilite");
		}
		el.calendar.tooltips.firstChild.data = _TT["SEL_DATE"];
		stopEvent(ev);
	}
};

/**
 *  A generic "click" handler :) handles all types of buttons defined in this
 *  calendar.
 */
Calendar.cellClick = function(el) {
	var cal = el.calendar;
	var closing = false;
	var newdate = false;
	var date = null;
	if (typeof el.navtype == "undefined") {
//		alert("date="+el.caldate+"/"+cal.date.getMonth()+"/"+cal.date.getFullYear()+" , "+cal.f_no_weekholi);
		if (cal.f_no_weekholi.length > 0) {	// ����觤������� "confirm", "noconfirm"
			rundate = new Date(cal.date.getFullYear(), cal.date.getMonth(), el.caldate);
			rundatestr = rundate.getFullYear()+"-"+rundate.getMonth()+"-"+rundate.getDate();

			var SAT = cal.mondayFirst ? 5 : 6;
			var SUN = cal.mondayFirst ? 6 : 0;
			var wday = rundate.getDay();
			if (cal.mondayFirst) {
				wday = (wday > 0) ? (wday - 1) : 6;
			}

			var wstr = "";
			if (SAT==wday) { wstr = "�����"; }
			else if (SUN == wday) { wstr = "�ҷԵ��"; }

//			alert(cal.holiday_date.toString()+">>>>"+rundatestr+"(sat:"+SAT+", sun:"+SUN+", wday:"+wday+")");

			var idxholiday = -1;
			for(dd = 0; dd < cal.holiday_date.length; dd++) {
				if (cal.holiday_date[dd]==rundatestr) {
					idxholiday = dd;
					break;
				}
			}
			var w_h_confirm = "";
			var w_h_alert = "";
			if (wstr) {
				w_h_confirm = "���ѹ "+wstr+" ��ͧ������͡�ѹ�����͹?..";
				w_h_alert = "���ѹ "+wstr+" �������ö���͡��..";
			} else if (idxholiday > -1) {
				w_h_confirm = "���ѹ "+cal.holiday_name[idxholiday]+" ��ͧ������͡�ѹ�����͹?..";
				w_h_alert = "���ѹ "+cal.holiday_name[idxholiday]+" �������ö���͡��..";
			}
			if (w_h_confirm) {
				if (cal.f_no_weekholi == "confirm") {
					if (confirm(w_h_confirm)) {
						Calendar.removeClass(cal.currentDateEl, "selected");
						Calendar.addClass(el, "selected");
						closing = (cal.currentDateEl == el);
						if (!closing) {
							cal.currentDateEl = el;
						}
						cal.date.setDate(el.caldate);
						date = cal.date;
						newdate = true;
					}
				} else if (cal.f_no_weekholi == "alert") {
					alert(w_h_alert);
				}
			} else {
				Calendar.removeClass(cal.currentDateEl, "selected");
				Calendar.addClass(el, "selected");
				closing = (cal.currentDateEl == el);
				if (!closing) {
					cal.currentDateEl = el;
				}
				cal.date.setDate(el.caldate);
				date = cal.date;
				newdate = true;
			}
		} else {	// else if (cal.f_no_weekholi)
			Calendar.removeClass(cal.currentDateEl, "selected");
			Calendar.addClass(el, "selected");
			closing = (cal.currentDateEl == el);
			if (!closing) {
				cal.currentDateEl = el;
			}
			cal.date.setDate(el.caldate);
			date = cal.date;
			newdate = true;
		}	// if (cal.f_no_weekholi)
	} else {
		if (el.navtype == 200) {
			Calendar.removeClass(el, "hilite");
			cal.callCloseHandler();
			return;
		}
		date = (el.navtype == 0) ? new Date() : new Date(cal.date);
		var year = date.getFullYear();
		var mon = date.getMonth();
		function setMonth(m) {
			var day = date.getDate();
			var max = date.getMonthDays(m);
			if (day > max) {
				date.setDate(max);
			}
			date.setMonth(m);
		};
		switch (el.navtype) {
		    case -2:
			if (year > cal.minYear) {
				date.setFullYear(year - 1);
			}
			break;
		    case -1:
			if (mon > 0) {
				setMonth(mon - 1);
			} else if (year-- > cal.minYear) {
				date.setFullYear(year);
				setMonth(11);
			}
			break;
		    case 1:
			if (mon < 11) {
				setMonth(mon + 1);
			} else if (year < cal.maxYear) {
				date.setFullYear(year + 1);
				setMonth(0);
			}
			break;
		    case 2:
			if (year < cal.maxYear) {
				date.setFullYear(year + 1);
			}
			break;
		    case 100:
			cal.setMondayFirst(!cal.mondayFirst);
			return;
		}
		if (!date.equalsTo(cal.date)) {
			cal.setDate(date);
			newdate = el.navtype == 0;
		}
	}
	if (newdate) {
		cal.callHandler();
	}
	if (closing) {
		Calendar.removeClass(el, "hilite");
		cal.callCloseHandler();
	}
};

// END: CALENDAR STATIC FUNCTIONS

// BEGIN: CALENDAR OBJECT FUNCTIONS

/**
 *  This function creates the calendar inside the given parent.  If _par is
 *  null than it creates a popup calendar inside the BODY element.  If _par is
 *  an element, be it BODY, then it creates a non-popup calendar (still
 *  hidden).  Some properties need to be set before calling this function.
 */
Calendar.prototype.create = function (_par) {
	var parent = null;
	if (! _par) {
		// default parent is the document body, in which case we create
		// a popup calendar.
		parent = document.getElementsByTagName("body")[0];
		this.isPopup = true;
	} else {
		parent = _par;
		this.isPopup = false;
	}
	this.date = this.dateStr ? new Date(this.dateStr) : new Date();
	var table = Calendar.createElement("table");
	this.table = table;
	table.cellSpacing = 0;
	table.cellPadding = 0;
	table.calendar = this;
	Calendar.addEvent(table, "mousedown", Calendar.tableMouseDown);

	var div = Calendar.createElement("div");
	this.element = div;
	div.className = "calendar";
	if (this.isPopup) {
		div.style.position = "absolute";
		div.style.display = "none";
	}
	div.appendChild(table);

	var thead = Calendar.createElement("thead", table);
	var cell = null;
	var row = null;

	var cal = this;
	var hh = function (text, cs, navtype) {
		cell = Calendar.createElement("td", row);
		cell.colSpan = cs;
		cell.className = "button";
		Calendar._add_evs(cell);
		cell.calendar = cal;
		cell.navtype = navtype;
		if (text.substr(0, 1) != "&") {
			cell.appendChild(document.createTextNode(text));
		}
		else {
			// FIXME: dirty hack for entities
			cell.innerHTML = text;
		}
		return cell;
	};

	row = Calendar.createElement("tr", thead);
	var title_length = 6;
	(this.isPopup) && --title_length;
	(this.weekNumbers) && ++title_length;

	hh("-", 1, 100).ttip = Calendar._TT["TOGGLE"];
	this.title = hh("", title_length, 300);
	this.title.className = "title";
	if (this.isPopup) {
		this.title.ttip = Calendar._TT["DRAG_TO_MOVE"];
		this.title.style.cursor = "move";
		hh("&#x00d7;", 1, 200).ttip = Calendar._TT["CLOSE"];
	}

	row = Calendar.createElement("tr", thead);
	row.className = "headrow";

	this._nav_py = hh("&#x00ab;", 1, -2);
	this._nav_py.ttip = Calendar._TT["PREV_YEAR"];

	this._nav_pm = hh("&#x2039;", 1, -1);
	this._nav_pm.ttip = Calendar._TT["PREV_MONTH"];

	this._nav_now = hh(Calendar._TT["TODAY"], this.weekNumbers ? 4 : 3, 0);
	this._nav_now.ttip = Calendar._TT["GO_TODAY"];

	this._nav_nm = hh("&#x203a;", 1, 1);
	this._nav_nm.ttip = Calendar._TT["NEXT_MONTH"];

	this._nav_ny = hh("&#x00bb;", 1, 2);
	this._nav_ny.ttip = Calendar._TT["NEXT_YEAR"]

	// day names
	row = Calendar.createElement("tr", thead);
	row.className = "daynames";
	if (this.weekNumbers) {
		cell = Calendar.createElement("td", row);
		cell.className = "name wn";
		cell.appendChild(document.createTextNode(Calendar._TT["WK"]));
	}
	for (var i = 7; i > 0; --i) {
		cell = Calendar.createElement("td", row);
		cell.appendChild(document.createTextNode(""));
		if (!i) {
			cell.navtype = 100;
			cell.calendar = this;
			Calendar._add_evs(cell);
		}
	}
	this.firstdayname = (this.weekNumbers) ? row.firstChild.nextSibling : row.firstChild;
	this._displayWeekdays();

	var tbody = Calendar.createElement("tbody", table);
	this.tbody = tbody;

	for (i = 6; i > 0; --i) {
		row = Calendar.createElement("tr", tbody);
		if (this.weekNumbers) {
			cell = Calendar.createElement("td", row);
			cell.appendChild(document.createTextNode(""));
		}
		for (var j = 7; j > 0; --j) {
			cell = Calendar.createElement("td", row);
			cell.appendChild(document.createTextNode(""));
			cell.calendar = this;
			Calendar._add_evs(cell);
		}
	}

	var tfoot = Calendar.createElement("tfoot", table);

	row = Calendar.createElement("tr", tfoot);
	row.className = "footrow";

	cell = hh(Calendar._TT["SEL_DATE"], this.weekNumbers ? 8 : 7, 300);
	cell.className = "ttip";
	if (this.isPopup) {
		cell.ttip = Calendar._TT["DRAG_TO_MOVE"];
		cell.style.cursor = "move";
	}
	this.tooltips = cell;

	div = Calendar.createElement("div", this.element);
	this.monthsCombo = div;
	div.className = "combo";
	for (i = 0; i < Calendar._MN.length; ++i) {
		var mn = Calendar.createElement("div");
		mn.className = "label";
		mn.month = i;
		mn.appendChild(document.createTextNode(Calendar._MN3[i]));
		div.appendChild(mn);
	}

	div = Calendar.createElement("div", this.element);
	this.yearsCombo = div;
	div.className = "combo";
	for (i = 12; i > 0; --i) {
		var yr = Calendar.createElement("div");
		yr.className = "label";
		yr.appendChild(document.createTextNode(""));
		div.appendChild(yr);
	}
	this._init(this.mondayFirst, this.date);
	parent.appendChild(this.element);
};

/** keyboard navigation, only for popup calendars */
Calendar._keyEvent = function(ev) {
	if (!window.calendar) {
		return false;
	}
	(Calendar.is_ie) && (ev = window.event);
	var cal = window.calendar;
	var act = (Calendar.is_ie || ev.type == "keypress");
	if (ev.ctrlKey) {
		switch (ev.keyCode) {
		    case 37: // KEY left
			act && Calendar.cellClick(cal._nav_pm);
			break;
		    case 38: // KEY up
			act && Calendar.cellClick(cal._nav_py);
			break;
		    case 39: // KEY right
			act && Calendar.cellClick(cal._nav_nm);
			break;
		    case 40: // KEY down
			act && Calendar.cellClick(cal._nav_ny);
			break;
		    default:
			return false;
		}
	} else switch (ev.keyCode) {
	    case 32: // KEY space (now)
		Calendar.cellClick(cal._nav_now);
		break;
	    case 27: // KEY esc
		act && cal.hide();
		break;
	    case 37: // KEY left
	    case 38: // KEY up
	    case 39: // KEY right
	    case 40: // KEY down
		if (act) {
			var date = cal.date.getDate() - 1;
			var el = cal.currentDateEl;
			var ne = null;
			var prev = (ev.keyCode == 37) || (ev.keyCode == 38);
			switch (ev.keyCode) {
			    case 37: // KEY left
				(--date >= 0) && (ne = cal.ar_days[date]);
				break;
			    case 38: // KEY up
				date -= 7;
				(date >= 0) && (ne = cal.ar_days[date]);
				break;
			    case 39: // KEY right
				(++date < cal.ar_days.length) && (ne = cal.ar_days[date]);
				break;
			    case 40: // KEY down
				date += 7;
				(date < cal.ar_days.length) && (ne = cal.ar_days[date]);
				break;
			}
			if (!ne) {
				if (prev) {
					Calendar.cellClick(cal._nav_pm);
				} else {
					Calendar.cellClick(cal._nav_nm);
				}
				date = (prev) ? cal.date.getMonthDays() : 1;
				el = cal.currentDateEl;
				ne = cal.ar_days[date - 1];
			}
			Calendar.removeClass(el, "selected");
			Calendar.addClass(ne, "selected");
			cal.date.setDate(ne.caldate);
			cal.currentDateEl = ne;
		}
		break;
	    case 13: // KEY enter
		if (act) {
			cal.callHandler();
			cal.hide();
		}
		break;
	    default:
		return false;
	}
	Calendar.stopEvent(ev);
};

/**
 *  (RE)Initializes the calendar to the given date and style (if mondayFirst is
 *  true it makes Monday the first day of week, otherwise the weeks start on
 *  Sunday.
 */
Calendar.prototype._init = function (mondayFirst, dates) {
	//alert("come in _init dates=" + dates);
	var today = new Date();
	var year = dates.getFullYear();
	if (parseInt(year) > 2500)
	{
		year =  parseInt(year) - 543;
	}

	if (year < this.minYear) {
		year = this.minYear;
		dates.setFullYear(year);
	} else if (year > this.maxYear) {
		year = this.maxYear;
		dates.setFullYear(year);
	}
	
	this.mondayFirst = mondayFirst;
	this.date = new Date(dates);
	var month = dates.getMonth();
	var mday = dates.getDate();
	var no_days = dates.getMonthDays();
	dates.setDate(1);
	var wday = dates.getDay();
	var MON = mondayFirst ? 1 : 0;
	var SAT = mondayFirst ? 5 : 6;
	var SUN = mondayFirst ? 6 : 0;
	if (mondayFirst) {
		wday = (wday > 0) ? (wday - 1) : 6;
	}
	var iday = 1;
	var row = this.tbody.firstChild;
	var MN = Calendar._MN3[month];
	var hasToday = ((today.getFullYear() == year) && (today.getMonth() == month));
	var todayDate = today.getDate();
	var week_number = dates.getWeekNumber();
	var ar_days = new Array();
	for (var i = 0; i < 6; ++i) {
		if (iday > no_days) {
			row.className = "emptyrow";
			row = row.nextSibling;
			continue;
		}
		var cell = row.firstChild;
		if (this.weekNumbers) {
			cell.className = "day wn";
			cell.firstChild.data = week_number;
			cell = cell.nextSibling;
		}
		++week_number;
		row.className = "daysrow";
		for (var j = 0; j < 7; ++j) {
			cell.className = "day";
			if ((!i && j < wday) || iday > no_days) {
				// cell.className = "emptycell";
				cell.innerHTML = "&nbsp;";
				cell.disabled = true;
				cell = cell.nextSibling;
				continue;
			}
			cell.disabled = false;
			cell.firstChild.data = iday;
			if (typeof this.checkDisabled == "function") {
				date.setDate(iday);
				if (this.checkDisabled(date)) {
					cell.className += " disabled";
					cell.disabled = true;
				}
			}
			if (!cell.disabled) {
				ar_days[ar_days.length] = cell;
				cell.caldate = iday;
				cell.ttip = "_";
				if (iday == mday) {
					cell.className += " selected";
					this.currentDateEl = cell;
				}
				if (hasToday && (iday == todayDate)) {
					cell.className += " today";
					cell.ttip += Calendar._TT["PART_TODAY"];
				}
				rundate = new Date(year, month, iday);
				rundatestr = rundate.getFullYear()+"-"+rundate.getMonth()+"-"+rundate.getDate();
//				alert(this.holiday_date.toString()+">>>>"+rundatestr);
//				var idxholiday = this.holiday_date.indexOf(rundatestr);

				var idxholiday = -1;
				for(dd = 0; dd < this.holiday_date.length; dd++) {
					if (this.holiday_date[dd]==rundatestr) {
						idxholiday = dd;
						break;
					}
				}

//				alert(this.holiday_date.toString()+">>>>"+rundatestr+"    [idxholiday="+idxholiday+"]");
				if (wday == SAT || wday == SUN) {
					if (idxholiday >= 0) {
						cell.className += " wholiday";
						cell.ttip += " <"+this.holiday_name[idxholiday]+">";
					} else
						cell.className += " weekend";
				} else if (idxholiday >= 0) {
					cell.className += " holiday";
					cell.ttip += " <"+this.holiday_name[idxholiday]+">";
				}
			}
			++iday;
			((++wday) ^ 7) || (wday = 0);
			cell = cell.nextSibling;
		}
		row = row.nextSibling;
	}
	this.ar_days = ar_days;
	this.title.firstChild.data = Calendar._MN[month] + ", " + (parseInt(year)+543);
	// PROFILE
	// this.tooltips.firstChild.data = "Generated in " + ((new Date()) - today) + " ms";
};

/**
 *  Calls _init function above for going to a certain date (but only if the
 *  date is different than the currently selected one).
 */
Calendar.prototype.setDate = function (date) {
	if (!date.equalsTo(this.date)) {
		this._init(this.mondayFirst, date);
	}
};

/** Modifies the "mondayFirst" parameter (EU/US style). */
Calendar.prototype.setMondayFirst = function (mondayFirst) {
	this._init(mondayFirst, this.date);
	this._displayWeekdays();
};

/**
 *  Allows customization of what dates are enabled.  The "unaryFunction"
 *  parameter must be a function object that receives the date (as a JS Date
 *  object) and returns a boolean value.  If the returned value is true then
 *  the passed date will be marked as disabled.
 */
Calendar.prototype.setDisabledHandler = function (unaryFunction) {
	this.checkDisabled = unaryFunction;
};

/** Customization of allowed year range for the calendar. */
Calendar.prototype.setRange = function (a, z) {
	this.minYear = a;
	this.maxYear = z;
};

/** Calls the first user handler (selectedHandler). */
Calendar.prototype.callHandler = function () {
	if (this.onSelected) {
		this.onSelected(this, this.date.print(this.dateFormat));
	}
};

/** Calls the second user handler (closeHandler). */
Calendar.prototype.callCloseHandler = function () {
	if (this.onClose) {
		this.onClose(this);
	}
	this.hideShowCovered();
};

/** Removes the calendar object from the DOM tree and destroys it. */
Calendar.prototype.destroy = function () {
	var el = this.element.parentNode;
	el.removeChild(this.element);
	Calendar._C = null;
	delete el;
};

/**
 *  Moves the calendar element to a different section in the DOM tree (changes
 *  its parent).
 */
Calendar.prototype.reparent = function (new_parent) {
	var el = this.element;
	el.parentNode.removeChild(el);
	new_parent.appendChild(el);
};

// This gets called when the user presses a mouse button anywhere in the
// document, if the calendar is shown.  If the click was outside the open
// calendar this function closes it.
Calendar._checkCalendar = function(ev) {
	if (!window.calendar) {
		return false;
	}
	var el = Calendar.is_ie ? Calendar.getElement(ev) : Calendar.getTargetElement(ev);
	for (; el != null && el != calendar.element; el = el.parentNode);
	if (el == null) {
		// calls closeHandler which should hide the calendar.
		window.calendar.callCloseHandler();
		Calendar.stopEvent(ev);
	}
};

/** Shows the calendar. */
Calendar.prototype.show = function () {
	var rows = this.table.getElementsByTagName("tr");
	for (var i = rows.length; i > 0;) {
		var row = rows[--i];
		Calendar.removeClass(row, "rowhilite");
		var cells = row.getElementsByTagName("td");
		for (var j = cells.length; j > 0;) {
			var cell = cells[--j];
			Calendar.removeClass(cell, "hilite");
			Calendar.removeClass(cell, "active");
		}
	}
	this.element.style.display = "block";
	this.hidden = false;
	if (this.isPopup) {
		window.calendar = this;
		Calendar.addEvent(document, "keydown", Calendar._keyEvent);
		Calendar.addEvent(document, "keypress", Calendar._keyEvent);
		Calendar.addEvent(document, "mousedown", Calendar._checkCalendar);
	}
	this.hideShowCovered();
};

/**
 *  Hides the calendar.  Also removes any "hilite" from the class of any TD
 *  element.
 */
Calendar.prototype.hide = function () {
	if (this.isPopup) {
		Calendar.removeEvent(document, "keydown", Calendar._keyEvent);
		Calendar.removeEvent(document, "keypress", Calendar._keyEvent);
		Calendar.removeEvent(document, "mousedown", Calendar._checkCalendar);
	}
	this.element.style.display = "none";
	this.hidden = true;
	this.hideShowCovered();
};

/**
 *  Shows the calendar at a given absolute position (beware that, depending on
 *  the calendar element style -- position property -- this might be relative
 *  to the parent's containing rectangle).
 */
Calendar.prototype.showAt = function (x, y) {
	var s = this.element.style;
	s.left = x + "px";
	s.top = y + "px";
	this.show();
};

/** Shows the calendar near a given element. */
Calendar.prototype.showAtElement = function (el) {
	var p = Calendar.getAbsolutePos(el);
	var calendarHeight = 178;
	var relativeHeight = document.body.clientHeight + document.body.scrollTop;
	if((p.y + el.offsetHeight + calendarHeight) <= relativeHeight) this.showAt(p.x, (p.y + el.offsetHeight));
	else this.showAt(p.x, (p.y + el.offsetHeight - calendarHeight));
};

/** Customizes the date format. */
Calendar.prototype.setDateFormat = function (str) {
	this.dateFormat = str;
	//this.dateFormat = "dd-mm-y";
};

/** Customizes the tooltip date format. */
Calendar.prototype.setTtDateFormat = function (str) {
	this.ttDateFormat = str;
};

/**
 *  Tries to identify the date represented in a string.  If successful it also
 *  calls this.setDate which moves the calendar to the given date.
 */
Calendar.prototype.parseDate = function (str, fmt) {
	var y = 0;
	var m = -1;
	var d = 0;
	fmt="dd-mm-y";
	//alert("in parseDate,str=" + str + ",fmt=" + fmt);
	var a = str.split(/\W+/);
	if (!fmt) {
		fmt = this.dateFormat;
	}
	//alert("come in parseDate, a.length=" + a.length);
	var b = fmt.split(/\W+/);
	var i = 0, j = 0;
	for (i = 0; i < a.length; ++i) {
		//alert("a["+i+"]="+ a[i]);
		//alert("b["+i+"]="+b[i]);
		if (b[i] == "D" || b[i] == "DD") {
			continue;
		}
		if (b[i] == "d" || b[i] == "dd") {
			//d = parseInt(a[i]);
			d = a[i];
			//alert("value of d=" + d);
		}
		if (b[i] == "m" || b[i] == "mm") {
			//m = parseInt(a[i]) - 1;
			m = a[i];
			m--;
			//alert("value of m=" + m);			
		}

		if (b[i] == "y") {
			y = parseInt(a[i]) -543;  //Modified by Aod
			//alert("come in y in parseDate,y=" + y + ",a=" + a[i]);
		}
		if (b[i] == "yy") {
			y = parseInt(a[i]) + 1900 - 543; //Modified by Aod
			//alert("com in yy in parseDate,yy=" + yy);
		}
		if (b[i] == "M" || b[i] == "MM") {
			for (j = 0; j < 12; ++j) {
				if (Calendar._MN[j].substr(0, a[i].length).toLowerCase() == a[i].toLowerCase()) { m = j; break; }
			}
		}
	}
	//alert(" parseDate =>d=" + d + ",m=" + m + ",y=" + y);
	if (y != 0 && m != -1 && d != 0) {
		this.setDate(new Date(y, m, d));
		return;
	}
	y = 0; m = -1; d = 0;
	for (i = 0; i < a.length; ++i) {
		if (a[i].search(/[a-zA-Z]+/) != -1) {
			var t = -1;
			for (j = 0; j < 12; ++j) {
				if (Calendar._MN[j].substr(0, a[i].length).toLowerCase() == a[i].toLowerCase()) { t = j; break; }
			}
			if (t != -1) {
				if (m != -1) {
					d = m+1;
				}
				m = t;
			}
		} else if (parseInt(a[i]) <= 12 && m == -1) {
			m = a[i]-1;
		} else if (parseInt(a[i]) > 31 && y == 0) {
			y = a[i];
		} else if (d == 0) {
			d = a[i];
		}
	}
	if (y == 0) {
		var today = new Date();
		y = today.getFullYear();
	}
	//alert("in parsedate,d=" + d + ",m=" + m);
	if (m != -1 && d != 0) {
		this.setDate(new Date(y, m, d));
	}
};

Calendar.prototype.hideShowCovered = function () {
	var tags = new Array("applet", "iframe", "select");
	var el = this.element;

	var p = Calendar.getAbsolutePos(el);
	var EX1 = p.x;
	var EX2 = el.offsetWidth + EX1;
	var EY1 = p.y;
	var EY2 = el.offsetHeight + EY1;

	for (var k = tags.length; k > 0; ) {
		var ar = document.getElementsByTagName(tags[--k]);
		var cc = null;

		for (var i = ar.length; i > 0;) {
			cc = ar[--i];

			p = Calendar.getAbsolutePos(cc);
			var CX1 = p.x;
			var CX2 = cc.offsetWidth + CX1;
			var CY1 = p.y;
			var CY2 = cc.offsetHeight + CY1;

			if (this.hidden || (CX1 > EX2) || (CX2 < EX1) || (CY1 > EY2) || (CY2 < EY1)) {
				cc.style.visibility = "visible";
			} else {
				cc.style.visibility = "hidden";
			}
		}
	}
};

/** Internal function; it displays the bar with the names of the weekday. */
Calendar.prototype._displayWeekdays = function () {
	var MON = this.mondayFirst ? 0 : 1;
	var SUN = this.mondayFirst ? 6 : 0;
	var SAT = this.mondayFirst ? 5 : 6;
	var cell = this.firstdayname;
	for (var i = 0; i < 7; ++i) {
		cell.className = "day name";
		if (!i) {
			cell.ttip = this.mondayFirst ? Calendar._TT["SUN_FIRST"] : Calendar._TT["MON_FIRST"];
			cell.navtype = 100;
			cell.calendar = this;
			Calendar._add_evs(cell);
		}
		if (i == SUN || i == SAT) {
			Calendar.addClass(cell, "weekend");
		}
		cell.firstChild.data = Calendar._DN3[i + 1 - MON];
		cell = cell.nextSibling;
	}
};

/** Internal function.  Hides all combo boxes that might be displayed. */
Calendar.prototype._hideCombos = function () {
	this.monthsCombo.style.display = "none";
	this.yearsCombo.style.display = "none";
};

/** Internal function.  Starts dragging the element. */
Calendar.prototype._dragStart = function (ev) {
	if (this.dragging) {
		return;
	}
	this.dragging = true;
	var posX;
	var posY;
	if (Calendar.is_ie) {
		posY = window.event.clientY + document.body.scrollTop;
		posX = window.event.clientX + document.body.scrollLeft;
	} else {
		posY = ev.clientY + window.scrollY;
		posX = ev.clientX + window.scrollX;
	}
	var st = this.element.style;
	this.xOffs = posX - parseInt(st.left);
	this.yOffs = posY - parseInt(st.top);
	with (Calendar) {
		addEvent(document, "mousemove", calDragIt);
		addEvent(document, "mouseover", stopEvent);
		addEvent(document, "mouseup", calDragEnd);
	}
};

// BEGIN: DATE OBJECT PATCHES

/** Adds the number of days array to the Date object. */
Date._MD = new Array(31,28,31,30,31,30,31,31,30,31,30,31);

/** Constants used for time computations */
Date.SECOND = 1000 /* milliseconds */;
Date.MINUTE = 60 * Date.SECOND;
Date.HOUR   = 60 * Date.MINUTE;
Date.DAY    = 24 * Date.HOUR;
Date.WEEK   =  7 * Date.DAY;

/** Returns the number of days in the current month */
Date.prototype.getMonthDays = function(month) {
	var year = this.getFullYear();
	if (typeof month == "undefined") {
		month = this.getMonth();
	}
	if (((0 == (year%4)) && ( (0 != (year%100)) || (0 == (year%400)))) && month == 1) {
		return 29;
	} else {
		return Date._MD[month];
	}
};

/** Returns the number of the week.  The algorithm was "stolen" from PPK's
 * website, hope it's correct :) http://www.xs4all.nl/~ppk/js/week.html */
Date.prototype.getWeekNumber = function() {
	var now = new Date(this.getFullYear(), this.getMonth(), this.getDate(), 0, 0, 0);
	var then = new Date(this.getFullYear(), 0, 1, 0, 0, 0);
	var time = now - then;
	var day = then.getDay();
	(day > 3) && (day -= 4) || (day += 3);
	return Math.round(((time / Date.DAY) + day) / 7);
};

/** Checks dates equality (ignores time) */
Date.prototype.equalsTo = function(date) {
	return ((this.getFullYear() == date.getFullYear()) &&
		(this.getMonth() == date.getMonth()) &&
		(this.getDate() == date.getDate()));
};

/** Prints the date in a string according to the given format. */
Date.prototype.print = function (frm) {
	var str = new String(frm);
	var m = this.getMonth();
	var d = this.getDate();
	var y = this.getFullYear();
	var wn = this.getWeekNumber();
	var w = this.getDay();
	var s = new Array();
	s["d"] = d;
	s["dd"] = (d < 10) ? ("0" + d) : d;
	s["m"] = 1+m;
	s["mm"] = (m < 9) ? ("0" + (1+m)) : (1+m);
	s["y"] = y;
	s["yy"] = new String(y).substr(2, 2);
	s["w"] = wn;
	s["ww"] = (wn < 10) ? ("0" + wn) : wn;
	with (Calendar) {
		s["D"] = _DN3[w];
		s["DD"] = _DN[w];
		s["M"] = _MN3[m];
		s["MM"] = _MN[m];
	}
	var re = /(.*)(\W|^)(d|dd|m|mm|y|yy|MM|M|DD|D|w|ww)(\W|$)(.*)/;
	//alert("before str=" + str);
	//alert("s[y]=" + s["y"]);
	//alert("s[yy]=" + s["yy"]);
	/*** change year to budhist year ***/
	s["yy"] = parseInt(s["yy"]) + 43;  // Modified by Aod
	//alert("after change s[yy]=" + s["yy"]);
	s["y"]  = parseInt(s["y"]) + 543;  // Modified by Aod
	//alert("after change s[y]=" + s["y"]);
	/*** end change ***/
	while (re.exec(str) != null) {
		str = RegExp.$1 + RegExp.$2 + s[RegExp.$3] + RegExp.$4 + RegExp.$5;
	}
	//alert("regExp1=" + RegExp.$1 + ",regExp2=" + RegExp.$2 + ",regexp3=" + RegExp.$3  + ",regexp4=" + RegExp.$4 + ",regexp5=" + RegExp.$5);

	//alert("str1289=" + str);
	return str;
};

// END: DATE OBJECT PATCHES

// global object that remembers the calendar
window.calendar = null;
