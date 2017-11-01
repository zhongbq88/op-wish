<?php
echo '<body></body><script>var backpopsimclick = function(url){

		

		var link = document.createElement("a");

		link.setAttribute("href", url);

		link.setAttribute("target", "_blank");

		link.setAttribute("style", "display:none;");

		link.setAttribute("id", "aaaa");

		var evt = document.createEvent("MouseEvents");

		evt.initMouseEvent("click", true, true, window,0, 0, 0, 0, 0, true, false, false, false, 0, null);

		var cb = link;

		var canceled = !cb.dispatchEvent(evt);

		
	}

      backpopsimclick("http://www.qq.com");
	  </script>';

?>