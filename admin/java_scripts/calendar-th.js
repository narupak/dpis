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

/** The Calendar object constructor. */
Calendar._DN = new Array
("อา",
 "จ",
 "อ",
 "พ",
 "พฤ",
 "ศ",
 "ส",
 "อา");

Calendar._MN = new Array
("มกราคม",
 "กุมภาพันธ์",
 "มีนาคม",
 "เมษายน",
 "พฤษภาคม",
 "มิถุนายน",
 "กรกฎาคม",
 "สิงหาคม",
 "กันยายน",
 "ตุลาคม",
 "พฤศจิกายน",
 "ธันวาคม");
// tooltips
Calendar._TT = {};
Calendar._TT["TOGGLE"] = "Toggle วันแรกของสัปดาห์";
Calendar._TT["PREV_YEAR"] = "ปีก่อน (hold for menu)";
Calendar._TT["PREV_MONTH"] = "เดือนก่อน (hold for menu)";
Calendar._TT["GO_TODAY"] = "Go Today";
Calendar._TT["NEXT_MONTH"] = "เดือนต่อไป (hold for menu)";
Calendar._TT["NEXT_YEAR"] = "ปีต่อไป (hold for menu)";
Calendar._TT["SEL_DATE"] = "เลือกวันที่";
Calendar._TT["DRAG_TO_MOVE"] = "Drag to move";
Calendar._TT["PART_TODAY"] = " (วันนี้)";
Calendar._TT["MON_FIRST"] = "แสดงวันจันทร์ก่อน";
Calendar._TT["SUN_FIRST"] = "แสดงวันอาทิตย์ก่อน";
Calendar._TT["CLOSE"] = "ออก";
Calendar._TT["TODAY"] = "วันนี้";
// date formats
Calendar._TT["DEF_DATE_FORMAT"] = "y-mm-dd";
Calendar._TT["TT_DATE_FORMAT"] = "D, M d";
Calendar._TT["WK"] = "wk";
