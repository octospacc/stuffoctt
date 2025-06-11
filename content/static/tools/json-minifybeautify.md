<!--t JSON Minify/Beautify t-->
<!--d Minified Beautified Indentation: Reset (function(parentEl){ (function(minifiedEl, beautifiedEl, indentEl, resetEl){ function convertText (sourceEl, d-->

<div class="JSON-Minify-Beautify">
<p><label>Minified <textarea name="minified"></textarea></label></p>
<p><label>Beautified <textarea name="beautified"></textarea></label></p>
<p><label>Indentation: <input name="indentation" type="text" value="  " /><!--<input name="indentation" type="number" value="2" min="-1" />--></label></p>
<p><button name="reset">Reset</button></p>

<script>
(function(parentEl){

(function(minifiedEl, beautifiedEl, indentEl, resetEl){

function convertText (sourceEl, destinationEl, handler) {
  destinationEl.value = handler(sourceEl.value);
  indentEl.disabled = (sourceEl.value || destinationEl.value);
}

function makeJsonTransform (indent) {
  return (function(json){
    return JSON.stringify(JSON.parse(json), null, indent);
  });
}

sitoctt.utils.setTextConvertEvents(
  minifiedEl, beautifiedEl,
  makeJsonTransform(indentEl.value), makeJsonTransform(null),
  function (sourceEl, destinationEl, handler) {
    return (function(){
      convertText(sourceEl, destinationEl, handler);
    });
  },
);

resetEl.addEventListener('click', function () {
  minifiedEl.value = beautifiedEl.value = '';
  indentEl.value = indentEl.getAttribute('value');
  indentEl.disabled = false;
});

})(parentEl.querySelector('textarea[name="minified"]'), parentEl.querySelector('textarea[name="beautified"]'), parentEl.querySelector('input[name="indentation"]'), parentEl.querySelector('button[name="reset"]'));

})(document.querySelector('.JSON-Minify-Beautify'));

</script>
</div>