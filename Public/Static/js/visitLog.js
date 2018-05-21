(function () {
    var i = 0;
    var timer = setInterval(function(){
        if(!_visit_uid && !_self) {
            //clearInterval(timer);
            return false;
        }else{
            _self += '?visit_uid='+_visit_uid+'&t='+Math.random();
            var xmlhttp;
            if(window.XMLHttpRequest){
                xmlhttp = new XMLHttpRequest();
            }else{
                xmlhttp = new ActiveXObject("Microsoft.XMLHTTP");
            }
            xmlhttp.open('GET',_self,true);
            xmlhttp.send();
            //console.log(++i);
        }
    }, 1000);

    // 兼容各个浏览器
    var hiddenProperty = ('hidden' in document) ? 'hidden' :
        (('webkitHidden' in document) ? 'webkitHidden' : (('mozHidden' in document) ? 'mozHidden' : null));
    var visitbilityChangeEvent = hiddenProperty.replace(/hidden/i, 'visibilitychange');
    var onVisibilityChange = function(){
        if(!document[hiddenProperty]){
            location.reload();
        }else{
            // 关闭定时器
            clearInterval(timer);
        }
    }
    document.addEventListener(visitbilityChangeEvent, onVisibilityChange);
})();