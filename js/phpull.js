/**
 * Tooltip code modified from the very lightweight Fading
 * JavaScript Tooltips script by Michael Leigeber which
 * can be found at:
 *
 * http://www.leigeber.com/2008/06/javascript-tooltip/
 */

var phpull_results=new Object();
var php_tooltip=function(){
var preload,phpwrapper,title,short_desc,h4_method,method,h4_desc,desc,h4_returnvals,returnvals;

var id = 'tt';
var top = 3;
var left = 3;
var maxw = 400;
var speed = 10;
var timer = 20;
var endalpha = 90;
var alpha = 0;
var tt,t,c,b,h;
var ie = document.all ? true : false;

	return{

		show:function(obj) {

			// get the function name and class
			var cl = obj.title;
			var fcn = obj.innerHTML;

			// create the element if it doesnt exists
			if(tt == null) {
				tt = document.createElement('div');
				tt.setAttribute('id', id);
				preload = document.createElement('div');
				preload.setAttribute('class', 'loading');
				phpwrapper = document.createElement('div');
				phpwrapper.setAttribute('class', 'phpwrapper');
				title = document.createElement('div');
				title.setAttribute('class', 'title');
				short_desc = document.createElement('div');
				short_desc.setAttribute('class', 'short_desc');
				h4_method = document.createElement('h4');
				method = document.createElement('div');
				method.setAttribute('class', 'method');
				h4_desc = document.createElement('h4');
				desc = document.createElement('div');
				desc.setAttribute('class', 'desc');
				h4_returnvals = document.createElement('h4');
				returnvals = document.createElement('div');
				returnvals.setAttribute('class', 'returnvals');

				tt.appendChild(preload);
				phpwrapper.appendChild(title);
				phpwrapper.appendChild(short_desc);
				phpwrapper.appendChild(h4_method);
				phpwrapper.appendChild(method);
				phpwrapper.appendChild(h4_desc);
				phpwrapper.appendChild(desc);
				phpwrapper.appendChild(h4_returnvals);
				phpwrapper.appendChild(returnvals);
				tt.appendChild(phpwrapper);
				document.body.appendChild(tt);
				tt.style.opacity = 0;
				tt.style.filter = 'alpha(opacity=0)';
			} else {
				// hide the wrapper and show preloader
				phpwrapper.style.display = 'none';
				preload.style.display = 'block';
			}

			document.onmousemove = this.pos;

			// call the ajax method to retrieve code
			phpull_request(cl, fcn);

			tt.style.display = 'block';
			tt.style.width = maxw + 'px';
			h = parseInt(tt.offsetHeight) + top;
			clearInterval(tt.timer);
			tt.timer = setInterval(function(){ php_tooltip.fade(1) }, timer);
		},
		pos:function(e){
			var u = ie ? event.clientY + document.documentElement.scrollTop : e.pageY;
			var l = ie ? event.clientX + document.documentElement.scrollLeft : e.pageX;
			tt.style.top = (u - h) + 'px';
			tt.style.left = (l + left) + 'px';
		},
		fade:function(d){
			var a = alpha;
			if((a != endalpha && d == 1) || (a != 0 && d == -1)){
				var i = speed;
				if(endalpha - a < speed && d == 1){
					i = endalpha - a;
				}else if(alpha < speed && d == -1){
					i = a;
				}
				alpha = a + (i * d);
				tt.style.opacity = alpha * .01;
				tt.style.filter = 'alpha(opacity=' + alpha + ')';
			}else{
				clearInterval(tt.timer);
				if(d == -1){
					tt.style.display = 'none';
					document.onmousemove = null;
					//tt = null;
				}
			}
		},
		hide:function(){
			clearInterval(tt.timer);
			tt.timer = setInterval(function(){php_tooltip.fade(-1)},timer);
		}
	};
}();

function phpull_request(cl, fcn) {

	if (typeof(phpull_results[fcn]) != "undefined") {
		phpull_display(phpull_results[fcn]);
		return true;
	}

	var params = (cl == null) ? "function=" + encodeURIComponent(fcn) : "class=" + encodeURIComponent(cl) + "&function=" +encodeURIComponent(fcn);
	var xhr;
	if (window.XMLHttpRequest) { // IE7+, Firefox, Chrome, Opera, Safari
		xhr = new XMLHttpRequest();
		// ensure firefox handles returned HTML properly
		if (xhr.overrideMimeType) {
			xhr.overrideMimeType('text/html');
		}
	} else if (window.ActiveXObject) { // IE6, IE5
		try {
         xhr = new ActiveXObject("Msxml2.XMLHTTP");
      } catch (e) {
         try {
            xhr = new ActiveXObject("Microsoft.XMLHTTP");
         } catch (e) {}
      }
	}

	if (!xhr) { alert('An error occurred attempting to create an xmlhttp request.'); return false; }
	xhr.onreadystatechange = function () {
		if (xhr.readyState == 4 && xhr.status == 200) {
			result = eval('('+xhr.responseText+')');
			if (typeof(result.fail) != "undefined") return false;
			phpull_display(result);
			phpull_results[fcn] = result;
			return true;
		}
	}

	xhr.open('POST', '/wordpress/wp-content/plugins/phpull/phpull_ajax.php', true);
	xhr.setRequestHeader("Content-type", "application/x-www-form-urlencoded");
	xhr.setRequestHeader("X-Requested-With", "XMLHttpRequest");
	xhr.setRequestHeader("Content-length", params.length);
	xhr.setRequestHeader("Connection", "close");
	xhr.send(params);
}

function phpull_display(data) {
	var tt = document.getElementById('tt');

	tt.childNodes[1].childNodes[0].innerHTML = '<strong>' + data.function + '</strong> <em>' + data.phpversion + '</em>';
	tt.childNodes[1].childNodes[1].innerHTML = data.short_desc;
	tt.childNodes[1].childNodes[2].innerHTML = 'Method';
	tt.childNodes[1].childNodes[3].innerHTML = data.method;
	tt.childNodes[1].childNodes[4].innerHTML = 'Description';
	tt.childNodes[1].childNodes[5].innerHTML = data.long_desc;
	tt.childNodes[1].childNodes[6].innerHTML = 'Return Values';
	tt.childNodes[1].childNodes[7].innerHTML = data.return_vals;

	tt.childNodes[0].style.display = 'none';
	tt.childNodes[1].style.display = 'block';

}
