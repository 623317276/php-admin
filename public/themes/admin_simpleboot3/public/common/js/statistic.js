function iFrameHeight() {
    var ifm= document.getElementById("iframepage");
    var subWeb = document.frames ? document.frames["iframepage"].document : ifm.contentDocument;
    if(ifm != null && subWeb != null) {
       ifm.height = subWeb.body.scrollHeight;
    }
}


/**
 * 设置日历控件
 * @param id
 * @returns
 */
function getDate(id){
	return document.getElementById(id).value;
}

function changeType(type){
	document.getElementById("type").value=type;
	var startDate = document.getElementById("startTime").value;
	var endDate = document.getElementById("endTime").value;
	if(startDate == "" || endDate == ""){
		dm.alert("日期不能为空");
		return;
	}
	var year1 = startDate.split("-");
	var year2 = endDate.split("-");
	if(type == 'season' || type == 'month'){
		if(year1[0] != year2[0]){
			dm.alert("按月或者按季度统计，日期必须选择同一年");
			return;
		}
	}
	var stime = Date.parse(startDate); //开始时间秒数
    var etime = Date.parse(endDate); //开始时间秒数

       if(stime > etime){
           dm.alert("开始时间不能大于结束时间！");
            return;
           }   
	document.getElementById("submitForm").submit();
}

/*function exportData(formid,url){
		if(!formid || !$("#"+formid))	return;
		var action = $("#"+formid).attr("action");
		$("#"+formid).attr("action",url).attr("target","_blank").submit().attr("action",action).attr("target","_self");
}
*/
