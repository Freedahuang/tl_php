/*
 * Calendar EN language
 * Author: Mihai Bazon, <mihai_bazon@yahoo.com>
 */

// Encoding: any
// Distributed under the same terms as the calendar itself.

// For translators: please use UTF-8 if possible.  We strongly believe that
// Unicode is the answer to a real internationalized world.  Also please
// include your contact information in the header, as can be seen above.

// full day names
Calendar._DN = new Array
("星期天",
 "星期一",
 "星期二",
 "星期三",
 "星期四",
 "星期五",
 "星期六",
 "星期天");

// Please note that the following array of short day names (and the same goes
// for short month names, _SMN) isn't absolutely necessary.  We give it here
// for exemplification on how one can customize the short day names, but if
// they are simply the first N letters of the full name you can simply say:
//
//   Calendar._SDN_len = N; // short day name length
//   Calendar._SMN_len = N; // short month name length
//
// If N = 3 then this is not needed either since we assume a value of 3 if not
// present, to be compatible with translation files that were written before
// this feature.

// short day names
Calendar._SDN = new Array
("日",
 "一",
 "二",
 "三",
 "四",
 "五",
 "六",
 "日");

// First day of the week. "0" means display Sunday first, "1" means display
// Monday first, etc.
Calendar._FD = 0;

// full month names
Calendar._MN = new Array
("一月",
 "二月",
 "三月",
 "四月",
 "五月",
 "六月",
 "七月",
 "八月",
 "九月",
 "十月",
 "十一月",
 "十二月");

// short month names
Calendar._SMN = new Array
("一",
 "二",
 "三",
 "四",
 "五",
 "六",
 "七",
 "八",
 "九",
 "十",
 "十一",
 "十二");

// tooltips
Calendar._TT = {};
Calendar._TT["INFO"] = "关于日历程序及帮助信息";

Calendar._TT["ABOUT"] =
"DHTML Date/Time Selector\n" +
"(c) dynarch.com 2002-2005 / Author: Mihai Bazon\n" + // don't translate this this ;-)
"For latest version visit: http://www.dynarch.com/projects/calendar/\n" +
"Distributed under GNU LGPL.  See http://gnu.org/licenses/lgpl.html for details." +
"\n\n" +
"日期选择:\n" +
"- 用 \xab, \xbb 键选择年份\n" +
"- 用 " + String.fromCharCode(0x2039) + ", " + String.fromCharCode(0x203a) + " 键选择月份\n" +
"- 按住鼠标键将出现快速选择下拉菜单";
Calendar._TT["ABOUT_TIME"] = "\n\n" +
"时间选择:\n" +
"- 鼠标左键单击数字增加\n" +
"- 鼠标左键加Shift单击数字减少\n" +
"- 鼠标左键左右拖动可以快速增加/减少";

Calendar._TT["PREV_YEAR"] = "上年 (按住鼠标键将出现下拉菜单)";
Calendar._TT["PREV_MONTH"] = "上月 (按住鼠标键将出现下拉菜单)";
Calendar._TT["GO_TODAY"] = "选择今天";
Calendar._TT["NEXT_MONTH"] = "下月 (按住鼠标键将出现下拉菜单)";
Calendar._TT["NEXT_YEAR"] = "下年 (按住鼠标键将出现下拉菜单)";
Calendar._TT["SEL_DATE"] = "选择日期";
Calendar._TT["DRAG_TO_MOVE"] = "拖动";
Calendar._TT["PART_TODAY"] = " (今天)";

// the following is to inform that "%s" is to be the first day of week
// %s will be replaced with the day name.
Calendar._TT["DAY_FIRST"] = " %s 前置";

// This may be locale-dependent.  It specifies the week-end days, as an array
// of comma-separated numbers.  The numbers are from 0 to 6: 0 means Sunday, 1
// means Monday, etc.
Calendar._TT["WEEKEND"] = "0,6";

Calendar._TT["CLOSE"] = "关闭";
Calendar._TT["TODAY"] = "今天";
Calendar._TT["TIME_PART"] = "鼠标左键左右拖动可以快速增加/减少";

// date formats
Calendar._TT["DEF_DATE_FORMAT"] = "%Y-%m-%d";
Calendar._TT["TT_DATE_FORMAT"] = "%A, %B %e 日";

Calendar._TT["WK"] = "星期";
Calendar._TT["TIME"] = "时间:";