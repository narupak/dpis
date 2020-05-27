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
("��",
 "�",
 "�",
 "�",
 "��",
 "�",
 "�",
 "��");

Calendar._MN = new Array
("���Ҥ�",
 "����Ҿѹ��",
 "�չҤ�",
 "����¹",
 "����Ҥ�",
 "�Զع�¹",
 "�á�Ҥ�",
 "�ԧ�Ҥ�",
 "�ѹ��¹",
 "���Ҥ�",
 "��Ȩԡ�¹",
 "�ѹ�Ҥ�");
// tooltips
Calendar._TT = {};
Calendar._TT["TOGGLE"] = "Toggle �ѹ�á�ͧ�ѻ����";
Calendar._TT["PREV_YEAR"] = "�ա�͹ (hold for menu)";
Calendar._TT["PREV_MONTH"] = "��͹��͹ (hold for menu)";
Calendar._TT["GO_TODAY"] = "Go Today";
Calendar._TT["NEXT_MONTH"] = "��͹���� (hold for menu)";
Calendar._TT["NEXT_YEAR"] = "�յ��� (hold for menu)";
Calendar._TT["SEL_DATE"] = "���͡�ѹ���";
Calendar._TT["DRAG_TO_MOVE"] = "Drag to move";
Calendar._TT["PART_TODAY"] = " (�ѹ���)";
Calendar._TT["MON_FIRST"] = "�ʴ��ѹ�ѹ����͹";
Calendar._TT["SUN_FIRST"] = "�ʴ��ѹ�ҷԵ���͹";
Calendar._TT["CLOSE"] = "�͡";
Calendar._TT["TODAY"] = "�ѹ���";
// date formats
Calendar._TT["DEF_DATE_FORMAT"] = "y-mm-dd";
Calendar._TT["TT_DATE_FORMAT"] = "D, M d";
Calendar._TT["WK"] = "wk";
