/* 
 * description 中内容可根据自身需求自定义 
 */

function weeklyCalendar (dataArr,options,showData) {
	var that = this,
		_date = new Date(),   // 日期
		currentDay = _date.getDay(),  // 周几
		weekNum = $("#week_title").attr("ids");  
	/* 
	 * 公共描述可修改 
	 */
	var description = {
		firstTitle: '观看直播',
		nextTitle: '下载讲义',
		listTitle: '讲义列表',
		methodsTitle: '下载'
	}
	/* 
	 * 格式化日期 补零
	 */
	var format = function (num) {
		var _f = num < 10 ? '0'+num : num;
		return _f
	}
	/* 
	 * 创建周
	 */
	var createWeek = function () {
		var lis = '';
		var weeks_ch = ['日', '一', '二', '三', '四', '五', '六'];
		for (var i = 0, len = weeks_ch.length; i < len; i++) {
		  lis += '<li>' + weeks_ch[i] + '</li>';
		};
		$("#weekUl").html(lis);
	}
	/* 
	 * 计算过去和未来时间
	 */
	var countTime = function (n) {
		var getTime = _date.getTime() + (24 * 60 * 60 * 1000)*n;
		var needDate = new Date(getTime);
		var getYear = needDate.getFullYear();
		var getMonth = needDate.getMonth() + 1;
		var getDate = needDate.getDate();
		var obj = {
			'year': getYear,
			'month': getMonth,
			'date' : getDate
		};
		return obj
	}
	/* 
	 * 创建周日历
	 */
	var createDate = function (cDay) {
		var _cDay = cDay;
		var dateHtml = '';
		var currY = format(_date.getFullYear()),
			currM = format(_date.getMonth() + 1),
			currD = format(_date.getDate());
		for (var i = _cDay; i < _cDay + 7; i ++) {
			if (currY == countTime(i).year && currM == countTime(i).month && currD == countTime(i).date ) {
				dateHtml += '<section data-year='+countTime(i).year+' data-month='+countTime(i).month+' data-date='+countTime(i).date+'>'
						+ '<h2 class="current">'
						+ '<a href="javascript:void(0)">'+format(countTime(i).date)+'</a>'
						+ '</h2>'
						+ '<ul class="optionsUl" data-year='+countTime(i).year+' data-month='+countTime(i).month+' data-date='+countTime(i).date+'>'
						+ '</ul>'
						+ '</section>'
			} else {
				dateHtml += '<section data-year='+countTime(i).year+' data-month='+countTime(i).month+' data-date='+countTime(i).date+'>'
						 + '<h2>'
						 + '<a href="javascript:void(0)">'+format(countTime(i).date)+'</a>'
						 + '</h2>'
						 + '<ul class="optionsUl" data-year='+countTime(i).year+' data-month='+countTime(i).month+' data-date='+countTime(i).date+'>'
						 + '</ul>'
						 + '</section>'
			}
		}
		$("#calendarBox").html(dateHtml);
		reminder();
	}
	/* 
	 * 渲染 每日 提醒 列表
	 */
	var reminder = function () {
		var optionsUl = $(".optionsUl");
		$.each(optionsUl,function(index,element){
			var optionsHtml = '';
			let $element = $(element);
			let ele_year = format($element.attr('data-year')),
				ele_month = format($element.attr('data-month')),
				ele_date = format($element.attr('data-date'));
			$.each(dataArr,function(ind,ele) {
				let show_date = ele.date.split('-');
				if (ele_year == show_date[0] && ele_month == show_date[1] && ele_date == show_date[2]) {
					$element.prev().addClass("active"); // 渲染当前日期样式
					$.each(ele.items,function(_index,_ele){
						optionsHtml += '<li>'
									+ '<p>'+_ele.classTime+'</p>'
									+ '<p>'+_ele.className+'</p>'
							if (showData) {
							optionsHtml	+= '<article>'
								+ '<a href="javascript:void(0)">'+description.firstTitle+'</a>'
								+ '<a href="javascript:void(0)" onclick="downListShow(this)">'+description.nextTitle+'</a>'
								+ '</article>'
								+ '<div class="down_list" onclick="downListHide(this)">'
								+ '<div class="down_list_c" onclick="stopmp()">'
								+ '<h1>'+description.listTitle+'</h1>'
								+ '<div class="jy_list">'
								$.each(_ele.downList,function(index_,element_){
									optionsHtml += '<p>'+element_.downName+'<a data-address="'+element_.downAddress+'" href="javascript:void(0)">'+description.methodsTitle+'</a></p>'
								})
								optionsHtml += '</div>'
								+ '<h5 onclick="closeDownList(this)"></h5>'
							}		
								optionsHtml += '</li>'
					})
					return true
				}
			})
			$(element).html(optionsHtml);
		})
	}
	/* 
	 * 动态设置周 前一周 下一周 
	 */
	var changeWeek = function (weekNum) {
		createDate(- currentDay + (7 * weekNum));
		$("#week_title").attr("ids",weekNum);
		titleTime();
	}
	/* 
	 * 获取上一周
	 */
	$("#prevWeek").on("click",function () {
		weekNum --;
		changeWeek(weekNum);
	})
	/* 
	 * 获取下一周
	 */
	$("#nextWeek").on("click",function () {
		weekNum ++;
		changeWeek(weekNum);
	})
	/* 
	 * 渲染title时间 
	 */
	var titleTime = function () {
		var section = $("#calendarBox").find("section");
		var titleHtml = '';
		titleHtml += format($(section[0]).attr('data-year'))+'年'+format($(section[0]).attr('data-month'))+'月'+format($(section[0]).attr('data-date'))+'日 - '
				  + format($(section[6]).attr('data-year'))+'年'+format($(section[6]).attr('data-month'))+'月'+format($(section[6]).attr('data-date'))+'日';
		$("#showDate").html(titleHtml);
	}
	/* 
	 * 选择日期事件 
	 */
	$("#calendarBox").on("click","h2",function () {
		var textDate = $(this).text();
		$(this).addClass("select");
		$(this).parent().siblings().find('h2').removeClass("select");
		options['clickDate'](textDate);
	})
	/* 
	 * 初始化日历
	 */
	var initWeeklyCalendar = function () {
		createWeek();
		createDate(- currentDay);
		titleTime();
	}()
	/* 
	 * 控制弹窗 点击事件即 下载  
	 */
	$(".jy_list").on("click","a",function () {
		options['clickDownLoad'](this);
	})
}

/* 
 * 控制下载列表显示隐藏 
 */
function downListShow (that) {
	that.parentNode.parentNode.getElementsByClassName('down_list')[0].style.display = "block";
}
function downListHide (that) {
	that.style.display = "none";
}
function closeDownList (that) {
	that.parentNode.parentNode.style.display = "none";
}
// 阻止冒泡
function stopmp (e) {
	window.event? window.event.cancelBubble = true : e.stopPropagation();
}