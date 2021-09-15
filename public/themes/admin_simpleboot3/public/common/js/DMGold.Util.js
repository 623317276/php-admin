(function() {

    var DMGold;
    DMGold = this.DMGold;
    DMGold.Util.extend({

        getWindowSize: function() {

            var width = 0;

            var height = 0;

            if (window.innerWidth) {

                width = window.innerWidth;

            } else if ((document.body) && (document.body.clientWidth)) {

                width = document.body.clientWidth;
            }

            if (window.innerHeight) {

                height = window.innerHeight;

            } else if ((document.body) && (document.body.clientHeight)) {

                height = document.body.clientHeight;
            }

            if (document.documentElement && document.documentElement.clientHeight && document.documentElement.clientWidth) {

                width = document.documentElement.clientWidth;
                height = document.documentElement.clientHeight;
            }
            return width + ":" + height;
        },
        disable: function(target) {
            function disable(el) {
                var childrens = $(el).children();
                if (childrens.length > 0) {
                    childrens.each(function() {
                        $(this).attr("disabled", "true");
                        disable($(this));
                    });
                }
            }

            disable(target);
        },

        getDataChooser: function(datalist) {

            var el = $(datalist).find("input[type='radio']:checked,input[type='checkbox']:checked");

            return {

                el: el,

                isChoosen: el.length == 1,

                find: function(attribute) {

                    if (attribute == 'id') {

                        return el.val();

                    } else {

                        return el.parent().siblings("td[name='" + attribute + "']").html();

                    }

                }

            };

        },

        /**













		 * 刷新区域数据  通过name匹配













		 * @param zoom  jquery对象或css选择器   值













		 * @param data	数据  













		 */

        refreshData: function(zoom, data) {

            function refreshSingleData(zoom, data, i) {

                for (var attribute in data) {

                    var targert = $(zoom).find("[name=" + attribute + "]").eq(i || 0);

                    if (targert.is("input")) {

                        targert.val(data[attribute]);

                    } else {

                        targert.html(data[attribute]);

                    }

                }

            }

            if (Spine.isArray(data)) {

                for (var i in data) {

                    refreshSingleData(zoom, data[i], i);

                }

            } else {

                refreshSingleData(zoom, data);

            }

        },

        /**













		 * 用于判断函数是否存在













		 */

        funcexist: function() {

            if ($.isFunction(this)) {

                return true;

            } else {

                return false;

            }

        },

        /**













		 * 用于判断对象是否存在。













		 */

        exist: function() {

            if (this.length > 0) {

                return true;

            } else {

                return false;

            }

        },

        /**

		 * 用于判断对象是有效。

		*/

        enabled: function() {

            if ($(this).attr("enabled") == "true") {

                return true;

            } else {

                return false;

            }

        },

        /**

	    * Javasvript的onpropertychange事件模拟
	    */

        propertychange: function(event) {

            $(this).bind("input propertychange", event);

        },

        /**

	    * 判断element是否选中
	    */

        checked: function() {

            var size = $("*[name='" + this.selector + "']:checked").val();

            if (size == null) {

                return false;

            } else {

                return true;

            }

        },

        /**


	    * 多行文本溢出显示省略号,传入样式
	    */

        overflowDisplayEllipsis: function(contentClass) {

            $("." + contentClass).each(function(i) {

                var divH = $(this).height();

                var $p = $("p", $(this)).eq(0);

                while ($p.outerHeight() > divH) {

                    $p.text($p.text().replace(/(\s)*([a-zA-Z0-9]+|\W)(\.\.\.)?$/, "..."));

                };

            });

        },

        /**

	    * 强制输入数值


	    * decimal: 整数、小数状态

	    */

        forcenumeric: function(decimal) {

            $(this).css("ime-mode", "disabled");

            this.bind("keypress",
            function() {

                if (decimal == null) {

                    return event.keyCode >= 47 && event.keyCode <= 57;

                } else {

                    if (event.keyCode == 46) {

                        if (this.value.indexOf(".") != -1) { //判断是否有小数点

                            return false;

                        }

                    } else {

                        return event.keyCode >= 46 && event.keyCode <= 57;

                    }

                }

            });

            this.bind("blur",
            function() {

                if (this.value.lastIndexOf(".") == (this.value.length - 1)) {

                    this.value = this.value.substr(0, this.value.length - 1);

                } else if (isNaN(this.value)) {

                    this.value = "";

                }

            });

            this.bind("paste",
            function() {

                var s = clipboardData.getData('text');

                if (!/\D/.test(s)) {

                    value = s.replace(/^0*/, '');

                }

                return false;

            });

            this.bind("dragenter",
            function() {

                return false;

            });

            this.bind("keyup",
            function() {

                if (/(^0+)/.test(this.value)) {

                    this.value = this.value.replace(/^0*/, '');

                    //this.value = this.value; //判断是电话号码的时候首字0上种写法被替换了，固写此种

                }

            });

        },

        /**

		 * 复制控件

		 */

        copy: function(options) {

            var clip = new ZeroClipboard.Client(); // 新建一个对象

            var defaultOptions = {

                content: window.location.href,

                message: '复制链接成功！',

                id: ""

            };

            options = jQuery.extend(defaultOptions, options);

            clip.setHandCursor(true); //设置手型  

            clip.addEventListener("mouseUp",
            function(client) { //创建监听

                clip.setText(options["content"]); // 设置要复制的文本。

                Dialog.show(options["message"], "success");

            });

            clip.glue(options["id"]);

        },

        /**

		  * 正则表达式校验（[正则表达式]，[校验的文本]）

		 */

        is: function(regex, str) {

            if (!regex.test(str)) return false;

            return true;

        },

        /**

		  * 数字校验

		 */

        isNumber: function(str) {

            if (!/^(-?\d+)(\.\d+)?$/.test(str)) return false;

            return true;

        },

        /**

		  * 手机号校验

		 */

        isMobile: function(str) {

            if (!/^1[34578][0-9]{9}$/.test(str)) return false;

            return true;

        },

        /**

		  * 省份证校验

		 */

        isIdCar: function(str) {

            if (!/(^\d{15}$)|(^\d{18}$)|(^\d{17}(\d|X|x)$)/.test(str)) return false;

            return true;

        },

        /**


		  * 邮箱校验

		 */

        isMail: function(str) {

            if (!/^([a-zA-Z0-9_\.\-])+\@(([a-zA-Z0-9\-])+\.)+([a-zA-Z0-9]{2,4})+$/.test(str)) return false;

            return true;

        },

        /**


		  * 账号校验


		 */

        isAccount: function(str) {

            if (!/^[A-Za-z0-9]+$/.test(str)) return false;

            return true;

        },

        /**













		  * 密码校验













		 */

        validatePassword: function(str) {

            if (str.length < 8) {

                return false;

            }

            if (!/^(?![a-zA-z]+$)(?!\d+$)(?![!@#$%^&*]+$)(?![a-zA-z\d]+$)(?![a-zA-z!@#$%^&*]+$)(?![\d!@#$%^&*]+$)[a-zA-Z\d!@#$%^&*]+$/.test(str)) return false;

            return true;

        },

        /**













		 *加法函数，用来得到精确的加法结果













		 *说明：javascript的加法结果会有误差，在两个浮点数相加的时候会比较明显。这个函数返回较为精确的加法结果。













		 *调用：accAdd(arg1,arg2)













		 *返回值：arg1加上arg2的精确结果 













		 */

        add: function(arg1, arg2) {

            var r1, r2, m;

            try {
                r1 = arg1.toString().split(".")[1].length;
            } catch(e) {
                r1 = 0;
            }

            try {
                r2 = arg2.toString().split(".")[1].length;
            } catch(e) {
                r2 = 0;
            }

            m = Math.pow(10, Math.max(r1, r2));

            return Number((arg1 * m + arg2 * m) / m);

        },

        /**













		 *加法函数，用来得到精确的加法结果













		 *说明：javascript的减法法结果会有误差，在两个浮点数相减的时候会比较明显。这个函数返回较为精确的减法结果。













		 *调用：accSub(arg1,arg2)













		 *返回值：arg1加上arg2的精确结果 













		 */

        sub: function(arg1, arg2) {

            var r1, r2, m, n;

            try {
                r1 = arg1.toString().split(".")[1].length;
            } catch(e) {
                r1 = 0;
            }

            try {
                r2 = arg2.toString().split(".")[1].length;
            } catch(e) {
                r2 = 0;
            }

            m = Math.pow(10, Math.max(r1, r2));

            //动态控制精度长度

            n = (r1 >= r2) ? r1: r2;

            return Number(((arg1 * m - arg2 * m) / m).toFixed(n));

        },

        /**













		*乘法函数，用来得到精确的乘法结果













		*说明：javascript的乘法结果会有误差，在两个浮点数相乘的时候会比较明显。这个函数返回较为精确的乘法结果。













		*调用：accMul(arg1,arg2)













		*返回值：arg1乘以arg2的精确结果 













		*/

        mul: function(arg1, arg2) {

            var m = 0,
            s1 = arg1.toString(),
            s2 = arg2.toString();

            try {
                m += s1.split(".")[1].length;
            } catch(e) {}

            try {
                m += s2.split(".")[1].length;
            } catch(e) {}

            return Number(s1.replace(".", "")) * Number(s2.replace(".", "")) / Math.pow(10, m);

        },

        /**













		 *除法函数，用来得到精确的除法结果













	     *说明：javascript的除法结果会有误差，在两个浮点数相除的时候会比较明显。这个函数返回较为精确的除法结果。













		 *调用：accDiv(arg1,arg2)













		 *返回值：arg1除以arg2的精确结果













		 */

        div: function(arg1, arg2) {

            var t1 = 0,
            t2 = 0,
            r1, r2;

            try {
                t1 = arg1.toString().split(".")[1].length;
            } catch(e) {}

            try {
                t2 = arg2.toString().split(".")[1].length;
            } catch(e) {}

            with(Math) {

                r1 = Number(arg1.toString().replace(".", ""));

                r2 = Number(arg2.toString().replace(".", ""));

                return (r1 / r2) * pow(10, t2 - t1);

            }

        },

        //发送验证码

        sendCode: function(options) {

            var _self = this,
            url = "common/sendMsgty";

            if (!options["url"]) {

                options["url"] = url; //如果传入了数据源地址则以传入地址请求数据

            }

            //清除失效样式

            $("#" + options["id"]).removeAttr("disabled");

            if (options["isBindClick"] == false || !options["isBindClick"]) {

                //按钮点击事件

                $("#" + options["id"]).click(function() {

                    _self.sendPhoneOrEmail(options);

                });

            } else {

                _self.sendPhoneOrEmail(options);

            }

        },

        sendPhoneOrEmail: function(options) {

            var _self = this;

            //获取失效样式值

            var disabled = $("#" + options["id"]).attr("disabled");

            //获取手机号

            var phone = $("#" + options["phoneId"]).val();

            if (!phone) {

                Dialog.show("请先输入手机号!", "error");

                return;

            }

            if (!disabled) { //可以点击发送验证码

                //请求后台发送短信验证码

                DMGold.ajax({

                    url: options["url"],

                    data: {
                        param: phone,
                        type: options["type"]
                    },

                    async: true,

                    success: function(data) {

                        if (data.code != "1") {

                            Dialog.show(data.msg, "error");

                        } else {

                            _self.changeSendButtonCss("#" + options["id"]);

                        }

                    },

                    error: function() {

                        Dialog.confirm({

                            msg: "您的登录已失效,请重新登录!",

                            picClass: "error",

                            title: "提示信息",

                            showCancel: true,

                            callBack: function() {

                                //跳转到我的已发货页面

                                window.location.href = basePath + "login";

                            }

                        });

                    }

                });

            }

        },

        //发送验证码按钮样式改变

        changeSendButtonCss: function(obj) {

            var time = 60,
            refreshid = "";

            $(obj).attr("disabled", "disabled");

            if (obj == '#getVerifyButton' || obj == '#getDealVerifyButton') {

                $(obj).removeClass().addClass("forget_code");

            } else if (obj == '#sendMsgVerifyCode') {

                // 不做处理

            } else {

                $(obj).removeClass().addClass("yzm_full");

            }

            $(obj).val(time + "秒后可重新获取");

            refreshid = window.setInterval(function() {

                time--;

                if (time < 0) {

                    $(obj).removeAttr("disabled");

                    if (obj == '#getVerifyButton' || obj == '#getDealVerifyButton') {

                        $(obj).removeClass().addClass("forget_code");

                    } else if (obj == '#sendMsgVerifyCode') {

                        // 不做处理

                    } else {

                        $(obj).removeClass().addClass("btn02");

                    }

                    clearInterval(refreshid); //清除定时器

                } else if (time == 0) {

                    $(obj).removeAttr("disabled");

                    $(obj).val("发送验证码");

                } else {

                    $(obj).val(time + "秒后可重新获取");

                }

            },
            1000);

        },

        //加载区域信息

        loadAreaInfo: function(options) {

            var url = "api/ajaxGetRegion",
            isAll = true;

            if (options["url"]) {

                url = options["url"]; //如果传入了数据源地址则以传入地址请求数据

            }

            if (options["isAll"] == 'N') {

                isAll = false;

            }

            var setting = {

                selProvinceId: options["selProvinceId"],
                //省市县DOM id

                selCityId: options["selCityId"],

                selAreaId: options["selAreaId"],

                provinceOptionText: "请选择",
                // 省份第一项的字符

                provinceId: options["provinceId"],

                cityOptionText: "请选择",
                // 地级市第一项的字符

                cityId: options["cityId"],

                areaOptionText: "请选择",
                // 市、县级市、县第一项的字符

                areaId: options["areaId"],

                isLoadOnInit: true,
                // 是否init的时候就加载省份

                url: basePath + url,

                isAll: isAll,
                //是否是查询所有的省市（实际上是在请求后台的方法后拼了一个标示位，后台根据该标示查询对应数据,为true时查询所有省市信息，为false时只查询有店铺的省市）

                hasFilter: options["hasFilter"] //是否过滤直辖市

            };

            AreaSelector().init(setting);

        },

        //获取下拉框被选中的项的文本信息，传入select的id

        getOptValue: function(selectId) {

            var result = "";

            var selectIndex = $("#" + selectId)[0].selectedIndex; //获得是第几个被选中了

            result = $("#" + selectId)[0].options[selectIndex].text; //获得被选中的项目的文本

            return result;

        },

        //清除下拉框选项,传入select的id

        deleteOptions: function(selectId) {

            $("#" + selectId).empty();

            //var obj=$("#"+selectId)[0];

            // var options=obj.options;

            // for(var i=0;i <=options.length;i++)  

            // {  

            //     obj.removeChild(options[0]);  

            // } 

        },

        //清空选中的上传附件

        clearFileInput: function(fileId) {

            var id = fileId.split(",");

            for (var i = 0; i < id.length; i++) {

                var file = document.getElementById(id[i]);

                var form = document.createElement('form');

                document.body.appendChild(form);

                //记住file在旧表单中的的位置

                var pos = file.nextSibling;

                form.appendChild(file);

                form.reset();

                pos.parentNode.insertBefore(file, pos);

                document.body.removeChild(form);

            }

        },

        /**













		 * 传入的参数有：selectId,dataList,key,value,headKey,suffix,isBracket,seperator













		 * 其中selectId,dataList,key,value为必传参数













		 * 重置下拉框选项，













		 * 1、selectId:为要重置的下拉框id。













		 * 2、dataList:为数据源。













		 * 3、key:数据源集合中对象中作为key值得属性名，













		 * 4、value：数据源集合中对象中作为value值得属性名













		 * 5、headKey：要初始被选中的选项key值













		 * 6、suffix:需要在后value拼接的内容(suffix如果传入的是属性名则会拼接上对应属性的数据，如果传入的是固定的常量则会拼接上固定的常量，













		 *    也可传入多个属性名，传入多个属性名时务必以英文","隔开)













		 * 7、isBracket:是否在要拼接括号













		 * 8、seperator：隔开符（当拼接参数“suffix”传入的是多个属性名时，如果传入该参数则，会以传入的符号隔开各属性值，默认以空格隔开）













		 */

        resetOptions: function(options) {

            //下拉框对象

            var select = $("#" + options["selectId"])[0];

            var suffixContent = "";

            //如果数据为空

            if (options["dataList"] == null || options["dataList"].length == 0) {

                //清空下拉框选项

                this.deleteOptions(options["selectId"]);

                return;

            }

            //选项对象

            var newOption;

            //添加option选项

            for (var i = 0; i < options["dataList"].length; i++) {

                opt = options["dataList"][i];

                if (options["suffix"]) {

                    if (options["suffix"].indexOf(",") != -1) { //传入了多个属性

                        var names = options["suffix"].split(",");

                        //传入多个属性对应的属性值集合

                        var temps = [];

                        if (names) {

                            for (var i = 0; i < names.length; i++) {

                                temps.push(opt[names[i]]);

                            }

                        }

                        if (options["seperator"]) { //多个属性值之间默认以空格隔开拼接，如果传入了分割符则以用户传入分隔符隔开

                            suffixContent = temps.join(options["seperator"]);

                        } else {

                            suffixContent = temps.join("  ");

                        }

                    } else {

                        if (!opt[options["suffix"]]) {

                            suffixContent = options["suffix"]; //拼接传入的常量  

                        } else {

                            suffixContent = opt[options["suffix"]]; //评价传入属性名对应的数据	

                        }

                    }

                    //是否拼接（）

                    if (options["isBracket"]) {

                        suffixContent = "(" + suffixContent + ")";

                    }

                }

                //构建选项对象

                newOption = new Option(opt[options["value"]] + " " + suffixContent, opt[options["key"]]);

                if (opt[options["key"]] == options["headKey"]) newOption.selected = true;

                select.options[select.options.length] = newOption;

            }

        },

        //给传入字符串打码

        maskString: function(str, startIndex, endIndex) {

            if (!str) return "";

            var resultStr = "";

            if (startIndex && endIndex) {

                var startStr = str.substring(0, startIndex),

                endStr = str.substring(endIndex, str.length),

                midStr = str.substring(startIndex, endIndex),
                mask = [];

                for (var i = 0; i < midStr.length; i++) {

                    mask.push("*");

                }

                resultStr = startStr + mask.join("") + endStr;

            } else if (startIndex && !endIndex) {

                var startStr = str.substring(0, startIndex),

                endStr = str.substring(startIndex, str.length - startIndex),

                mask = [];

                for (var i = 0; i < endStr.length; i++) {

                    mask.push("*");

                }

                resultStr = startStr + mask.join("");

            }

            return resultStr;

        },

        //校验实名认证，交易密码设置，手机号认证

        authentication: function() {

            var result = {
                code: "",
                msg: ""
            };

            DMGold.ajax({

                url: "api/ckeckUserAuthority",

                success: function(data) {

                    var temp = [];

                    if (data.result.data.singleResult.idcardFlag != 'Y') {

                        temp.push("实名认证");

                    }

                    if (data.result.data.singleResult.dealFlag != 'Y') {

                        temp.push("交易密码认证");

                    }

                    if (data.result.data.singleResult.phoneFlag != 'Y') {

                        temp.push("手机认证");

                    }

                    if (temp.length > 0) {

                        result.code = "000001";

                        result.msg = "请先进行" + temp.join(",");

                    } else {

                        result.code = "000000";

                    }

                },

                error: function() {

                    Dialog.confirm({

                        msg: "您的登录已失效,请重新登录!",

                        picClass: "error",

                        title: "提示信息",

                        showCancel: true,

                        callBack: function() {

                            //跳转到我的已发货页面

                            window.location.href = basePath + "login";

                        }

                    });

                }

            });

            return result;

        },

        md5: function(str) {

            return hex_md5(str);

        },

        //进行18位身份证的基本验证和第18位的验证

        checkidcard: function(idCard) {

            if (idCard.length == 18) {

                var a_idCard = idCard.split(""); // 得到身份证数组   

                if (this.isValidityBrithBy18IdCard(idCard) && this.isTrueValidateCodeBy18IdCard(a_idCard)) { //进行18位身份证的基本验证和第18位的验证

                    return true;

                } else {

                    return false;

                }

            } else {

                return false;

            }

        },

        /**  













		 * 判断身份证号码为18位时最后的验证位是否正确  













		 * @param a_idCard 身份证号码数组  













		 * @return  













		 */

        isTrueValidateCodeBy18IdCard: function(a_idCard) {

            var sum = 0; // 声明加权求和变量   

            if (a_idCard[17].toLowerCase() == 'x') {

                a_idCard[17] = 10; // 将最后位为x的验证码替换为10方便后续操作   

            }

            for (var i = 0; i < 17; i++) {

                sum += this.idCardConfig.Wi[i] * a_idCard[i]; // 加权求和   

            }

            valCodePosition = sum % 11; // 得到验证码所位置   

            if (a_idCard[17] == this.idCardConfig.ValideCode[valCodePosition]) {

                return true;

            } else {

                return false;

            }

        },

        isValidityBrithBy18IdCard: function(idCard18) {

            var year = idCard18.substring(6, 10);

            var month = idCard18.substring(10, 12);

            var day = idCard18.substring(12, 14);

            var temp_date = new Date(year, parseFloat(month) - 1, parseFloat(day));

            // 这里用getFullYear()获取年份，避免千年虫问题   

            if (temp_date.getFullYear() != parseFloat(year)

            || temp_date.getMonth() != parseFloat(month) - 1

            || temp_date.getDate() != parseFloat(day)) {

                return false;

            } else {

                return true;

            }

        },

        /**













		 * ajax表单提交(支持附件上传，附件上传时，ie浏览器有兼容问题，需设置类型为text/html才行)













		 */

        ajaxForm: function(options) {

            //默认设置

            var defaultOptions = {

                type: "POST",

                dataType: "json",

                clearForm: false,
                // 成功提交后，清除所有表单元素的值

                resetForm: false,
                // 成功提交后，重置所有表单元素的值

                timeout: 3000 // 限制请求的时间，当请求大于3秒后，跳出请求

            };

            options = $.extend(defaultOptions, options);

            $('#' + options["formId"]).attr("enctype", "multipart/form-data");

            $('#' + options["formId"]).ajaxForm({

                url: basePath + options["url"],
                //默认是form的action，如果申明，则会覆盖

                type: options["type"],
                // 默认值是form的method("GET" or "POST")，如果声明，则会覆盖

                dataType: options["dataType"],
                // html（默认）、xml、script、json接受服务器端返回的类型

                clearForm: options["clearForm"],
                // 成功提交后，清除所有表单元素的值

                resetForm: options["resetForm"],
                // 成功提交后，重置所有表单元素的值

                timeout: options["timeout"],
                // 限制请求的时间，当请求大于3秒后，跳出请求

                headers: {

                    "ClientCallMode": "ajax"

                },

                beforeSubmit: options["beforeSubmit"],
                // 提交前的回调函数

                success: options["success"] // 提交后的回调函数

            });

            $('#' + options["formId"]).submit(function() {

                $(this).ajaxSubmit();

                return false;

            });

        },

        //将字符串转换为时间格式,适用各种浏览器,格式如2011-08-03 09:15:11

        getTimeByTimeStr: function(dateString) {

            var timeArr = dateStr.split(" ");

            var d = timeArr[0].split("-");

            var t = timeArr[1].split(":");

            new Date(d[0], (d[1] - 1), d[2], t[0], t[1], t[2]);

        },

        //将时间转换为字符串格式,适用各种浏览器

        getTimeStrByTime: function(time, stringType) {

            var y = time.getFullYear();

            var M = time.getMonth() + 1;

            var d = time.getDate();

            var h = time.getHours();

            var m = date.getMinutes();

            if (stringType == 1)

            return y + '-' + (M < 10 ? ('0' + M) : M) + '-' + (d < 10 ? ('0' + d) : d) + " " + (h < 10 ? ('0' + h) : h) + ":" + (m < 10 ? ('0' + m) : m);

            return y + '/' + (M < 10 ? ('0' + M) : M) + '/' + (d < 10 ? ('0' + d) : d) + " " + (h < 10 ? ('0' + h) : h) + ":" + (m < 10 ? ('0' + m) : m);

        },

        //在传入的日期上加上days天

        getNewDay: function(dateTemp, days) {

            var nDate = this.getDate(dateTemp);

            var millSeconds = Math.abs(nDate) + (days * 24 * 60 * 60 * 1000);

            var rDate = new Date(millSeconds);

            var year = rDate.getFullYear();

            var month = rDate.getMonth() + 1;

            if (month < 10) month = "0" + month;

            var date = rDate.getDate();

            if (date < 10) date = "0" + date;

            return (year + "-" + month + "-" + date);

        },

        //将字符串转换成date类型

        getDate: function(strDate) {

            var date = eval('new Date(' + strDate.replace(/\d+(?=-[^-]+$)/,

            function(a) {
                return parseInt(a, 10) - 1;
            }).match(/\d+/g) + ')');

            return date;

        },

        idCardConfig: {

            Wi: [7, 9, 10, 5, 8, 4, 2, 1, 6, 3, 7, 9, 10, 5, 8, 4, 2, 1],
            // 加权因子   

            ValideCode: [1, 0, 10, 9, 8, 7, 6, 5, 4, 3, 2] // 身份证验证位值.10代表X  

        }

    });

    //add by LvNing 2015-10-28

    String.prototype.trim = function() {

        return this.replace(/^\s+|\s+$/g, "");

    };

    //给Number类型增加一个add方法，调用起来更加方便。

    Number.prototype.add = function(arg) {

        return DMGold.Util.add(arg, this);

    };

    //给Number类型增加一个sub方法，调用起来更加方便。

    Number.prototype.sub = function(arg) {

        return DMGold.Util.sub(this, arg);

    };

    //给Number类型增加一个mul方法，调用起来更加方便。

    Number.prototype.mul = function(arg) {

        return DMGold.Util.mul(arg, this);

    };

    //给Number类型增加一个div方法，调用起来更加方便。

    Number.prototype.div = function(arg) {

        return DMGold.Util.div(this, arg);

    };

    // 重写toFixed方法

    Number.prototype.toFixed = function(fractionDigits)

    {

        //没有对fractionDigits做任何处理，假设它是合法输入  

        return (parseInt(this * Math.pow(10, fractionDigits) + 0.5) / Math.pow(10, fractionDigits)).toString();

    };

}).call(this);