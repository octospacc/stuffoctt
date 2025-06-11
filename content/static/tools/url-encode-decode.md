<!--t URL Encode/Decode t-->
<!--d Perform URL encoding or decoding on strings with various options. d-->

Perform URL encoding or decoding on strings with various options.

<div class="Percent-Encoding">
<p><label>Plain <textarea name="plain"></textarea></label></p>
<p><label>URL-encoded <textarea name="coded"></textarea></label></p>
<p><label>Steps: <input name="steps" type="number" value="1" min="1" /></label></p>
<p><button name="reset">Reset</button></p>

<script>
(function(parentEl){

(function(plainEl, codedEl, stepsEl, resetEl){

function convertText (sourceEl, destinationEl, handler) {
  var text = sourceEl.value;
  for (var i=0; i<stepsEl.value; i++) {
    text = handler(text);
  }
  destinationEl.value = text;
  stepsEl.disabled = (sourceEl.value || destinationEl.value);
}

sitoctt.utils.setTextConvertEvents(
  plainEl, codedEl,
  encodeURIComponent, decodeURIComponent,
  function (sourceEl, destinationEl, handler) {
    return (function(){
      convertText(sourceEl, destinationEl, handler);
    });
  },
);

resetEl.addEventListener('click', function () {
  plainEl.value = codedEl.value = '';
  stepsEl.value = stepsEl.getAttribute('value');
  stepsEl.disabled = false;
});

})(parentEl.querySelector('textarea[name="plain"]'), parentEl.querySelector('textarea[name="coded"]'), parentEl.querySelector('input[name="steps"]'), parentEl.querySelector('button[name="reset"]'));

})(document.querySelector('.Percent-Encoding'));

</script>

<!--
<link rel="stylesheet" href="/res/Percent-Encoding.css" />
<script src="/res/Percent-Encoding.js"></script>
-->
</div>