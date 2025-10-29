<!--t Codice Fiscale t-->

Calcolo del Codice Fiscale grazie alla libreria [`CodiceFiscale.js`](https://github.com/lucavandro/CodiceFiscaleJS). I dati sono elaborati interamente in locale e non inviati al server.

<div class="Codice-Fiscale">
<noscript><p>Questa pagina richiede JavaScript.</p></noscript>

<script src="/themes/octozone/js/codice.fiscale.var.js"></script>

<form hidden>
<p><label>Nome: <input name="name" type="text" required /></label></p>
<p><label>Cognome: <input name="surname" type="text" required /></label></p>
<p><label>Sesso: <select name="gender" required>
  <option value="M">Maschio</option>
  <option value="F">Femmina</option>
</select></label></p>
<p><label>Data di Nascita: <input name="birthday" type="date" required /></label></p>
<p><label>Area di Nascita: <select name="birtharea" required></select></label></p>
<p><label>Luogo di Nascita: <select name="birthplace" required></select></label></p>
<p>
  <button type="button">Calcola</button>
  <button type="reset">Reset</button>
</p>
<p><label>Codice Fiscale: </label><input type="text" name="cf" readonly /></p>
</form>

<script>
CodiceFiscale.utils.birthplaceFields('.Codice-Fiscale [name="birtharea"]', '.Codice-Fiscale [name="birthplace"]');
// var parent = document.querySelector('.Codice-Fiscale button[type="button"]');
// document.querySelector('.Codice-Fiscale button[type="button"]').addEventListener('click', function(){
document.querySelector('.Codice-Fiscale button[type="button"]').type = 'submit';
document.querySelector('.Codice-Fiscale form').addEventListener('submit', function(ev){
  ev.preventDefault();
  document.querySelector('.Codice-Fiscale [name="cf"]').value = new CodiceFiscale({
    name : jQuery('.Codice-Fiscale [name="name"]').val(),
    surname : jQuery('.Codice-Fiscale [name="surname"]').val(),
    gender : jQuery('.Codice-Fiscale [name="gender"]').val(),
    birthday : jQuery('.Codice-Fiscale [name="birthday"]').val(),
    birthplace : jQuery('.Codice-Fiscale [name="birthplace"]').val()
  });
  document.querySelector('.Codice-Fiscale [name="cf"]').focus();
});
document.querySelector('.Codice-Fiscale form').hidden = false;
</script>
</div>