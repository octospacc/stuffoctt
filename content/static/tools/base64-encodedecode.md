<!--t Base64 Encode/Decode t-->
<!--d Plaintext Encoded Steps: Reset (function(parentEl){ (function(plainEl, codedEl, stepsEl, resetEl, errorEl){ function convertText (sourceEl, d-->

<div class="Base64-Encode-Decode">
<p name="error"></p>
<p><label>Plaintext <textarea name="plain"></textarea></label></p>
<p><label>Encoded <textarea name="coded"></textarea></label></p>
<p><label>Steps: <input name="steps" type="number" value="1" min="1" /></label></p>
<p><button name="reset" type="button">Reset</button></p>

<script>
(function(parentEl){

(function(plainEl, codedEl, stepsEl, resetEl, errorEl){

function convertText (sourceEl, destinationEl, handler) {
  errorEl.textContent = '';
  var text = sourceEl.value;
  for (var i=0; i<stepsEl.value; i++) {
    try {
      text = handler(text);
    } catch (err) {
      errorEl.textContent = err;
      destinationEl.value = '';
      return;
    }
  }
  destinationEl.value = text;
  stepsEl.disabled = (sourceEl.value || destinationEl.value);
}

sitoctt.utils.setTextConvertEvents(
  plainEl, codedEl,
  btoa, atob,
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
  errorEl.textContent = '';
});

})(parentEl.querySelector('textarea[name="plain"]'), parentEl.querySelector('textarea[name="coded"]'), parentEl.querySelector('input[name="steps"]'), parentEl.querySelector('button[name="reset"]'), parentEl.querySelector('p[name="error"]'));

})(document.querySelector('.Base64-Encode-Decode'));

</script>
</div>