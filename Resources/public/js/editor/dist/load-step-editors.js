!function(e){var t=window.webpackJsonp;window.webpackJsonp=function(r,o,a){for(var i,c,d=0,u=[];d<r.length;d++)c=r[d],n[c]&&u.push(n[c][0]),n[c]=0;for(i in o)Object.prototype.hasOwnProperty.call(o,i)&&(e[i]=o[i]);for(t&&t(r,o,a);u.length;)u.shift()()};var r={},n={2:0};function o(t){if(r[t])return r[t].exports;var n=r[t]={i:t,l:!1,exports:{}};return e[t].call(n.exports,n,n.exports,o),n.l=!0,n.exports}o.e=function(e){var t=n[e];if(0===t)return new Promise(function(e){e()});if(t)return t[2];var r=new Promise(function(r,o){t=n[e]=[r,o]});t[2]=r;var a=document.getElementsByTagName("head")[0],i=document.createElement("script");i.type="text/javascript",i.charset="utf-8",i.async=!0,i.timeout=12e4,o.nc&&i.setAttribute("nonce",o.nc),i.src=o.p+""+({0:"bootstrap-vue-step-editor"}[e]||e)+".async.js";var c=setTimeout(d,12e4);function d(){i.onerror=i.onload=null,clearTimeout(c);var t=n[e];0!==t&&(t&&t[1](new Error("Loading chunk "+e+" failed.")),n[e]=void 0)}return i.onerror=i.onload=d,a.appendChild(i),r},o.m=e,o.c=r,o.d=function(e,t,r){o.o(e,t)||Object.defineProperty(e,t,{configurable:!1,enumerable:!0,get:r})},o.n=function(e){var t=e&&e.__esModule?function(){return e.default}:function(){return e};return o.d(t,"a",t),t},o.o=function(e,t){return Object.prototype.hasOwnProperty.call(e,t)},o.p="/bundles/idcistep/js/editor/dist/",o.oe=function(e){throw console.error(e),e},o(o.s=84)}({10:function(e,t,r){"use strict";r.d(t,"d",function(){return a}),r.d(t,"g",function(){return i}),r.d(t,"e",function(){return c}),r.d(t,"f",function(){return d}),r.d(t,"c",function(){return s}),r.d(t,"a",function(){return l}),r.d(t,"b",function(){return u});var n=r(4),o=r.n(n);function a(e,t){var r={};for(var n in e)e.hasOwnProperty(n)&&t(e[n])&&(r[n]=e[n]);return r}function i(e,t,r){var n={};void 0===r&&(r=!0),void 0===t&&(t=[]);for(var o=0,a=t.length;o<a;o++){var i=t[o];n[i]=e[i]}if(r)Object.keys(e).sort().forEach(function(r){-1===t.indexOf(r)&&(n[r]=e[r])});else for(var c in e)e.hasOwnProperty(c)&&-1===t.indexOf(c)&&(n[c]=e[c]);return n}function c(){return Math.random().toString(36).substr(2,9)}function d(e){var t=0;if(0===e.length)return t.toString();for(var r=0;r<e.length;r++){t=(t<<5)-t+e.charCodeAt(r),t|=0}return t.toString()}function u(e){for(var t,r=e.attributes,n={},o=0,a=r.length;o<a;o++)n[(t=r[o]).nodeName]=t.nodeValue;return n.value=e.value,n}function s(e,t,r,n,o,a){return'<div id="'+t+"-"+e+'" class="editor-modal modal fade '+r+" "+t+'"><div class="modal-dialog" role="document"><div class="modal-content"><div class="modal-header"><button type="button" class="close" aria-label="Close"><span aria-hidden="true">&times;</span></button><h4 class="modal-title">'+n+'</h4></div><div class="modal-body">'+o+'</div><div class="modal-footer">'+(a||"")+"</div></div></div></div>"}function l(e,t){var r="."+t+' input[required="required"]';function n(e){e.val()?e.css({"border-color":"#cccccc","background-color":"#ffffff"}):e.css({"border-color":"#c9302c","background-color":"#f3d9d9"})}o()(document).on("change",r,function(){n(o()(this))});var a=document.getElementById(e);new MutationObserver(function(e){e.forEach(function(e){(o()(e.target).hasClass(t)?o()(e.target).find('input[required="required"]'):o()(e.target).find(r)).each(function(){n(o()(this))})})}).observe(a,{childList:!0,characterData:!0,subtree:!0})}String.prototype.removeLineBreaksAnsExtraSpaces=function(){return this.replace(/\r?\n|\r/g," ").replace(/ {2,}/g," ")}},4:function(e,t){e.exports=jQuery},84:function(e,t,r){"use strict";Object.defineProperty(t,"__esModule",{value:!0});var n=r(85);Object(n.a)()},85:function(e,t,r){"use strict";t.a=function(){a()("textarea.step-editor").each(function(e){var t=this;r.e(0).then(r.bind(null,87)).then(function(r){var o="extraStepEditorComponent"+e;if(!document.getElementById(o)){var i=Object(n.b)(t),c=window[i["data-configuration-variable"]];c.componentId=o;var d=Object(n.c)(e,"extra-step-raw-mode-modal","modal-fullscreen","Editor in raw mode",'<div class="editor"><step-editor-raw></step-editor-raw></div><br>'),u='<button class="trigger-extra-step-raw-mode-modal-'+e+'">Raw mode</button>',s=Object(n.c)(e,"extra-step-advanced-visual-mode-modal","modal-fullscreen","Visual mode",'<div class="editor extra-step-editor"><step-editor></step-editor></div>',"<em>All your changes are automatically saved</em>"),l='<button class="trigger-extra-step-advanced-visual-mode-modal-'+e+'">Visual mode</button>';a()(t).after('<div class="modal-buttons">'+l+" "+u+"</div>");var f=a()("body");f.append('<div id="'+o+'">'+d+s+"</div>"),t.style.display="none";var v=["extra-step-advanced-visual-mode-modal","extra-step-raw-mode-modal"];v.forEach(function(t){!function(e,t){a()(document).on("click","button.trigger-"+e+"-"+t,function(r){r.preventDefault();var n=a()("#"+e+"-"+t);n.modal("show")})}(t,e)}),v.forEach(function(e){!function(e){var t="."+e+" .modal-body button.close-modal, ."+e+" .modal-footer > button.close-modal, ."+e+" .modal-header > button.close";a()(document).on("click",t,function(e){e.preventDefault(),a()(this).closest(".modal").modal("hide")})}(e)}),r.triggerVueStepEditor("#"+o,c,i),Object(n.a)(o,"extra-form-inputs-required")}})})};var n=r(10),o=r(4),a=r.n(o)}});