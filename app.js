var button = document.getElementById('insertCoin'),
    stat = document.getElementById('stat'),
    ping = document.getElementById('ping'),
    credits = document.getElementById('credits'),
    mac_addr = document.getElementById('mac_addr');

setInterval(() => {
  fetch('/api.php')
  .then((res) => res.json() )
  .then((wifi) => {
    credits.innerText = `${wifi.mb_used} / ${wifi.mb_credit}MB`;
    mac_addr.innerText = wifi.mac_addr.toUpperCase();
    ping.innerText = `${Math.floor(wifi.ping)}ms`;

    if( !wifi.ping ) stat.innerText = 'Offline';

    if( !wifi.insert_coin ) {
      btnInsertState();
    } else {
      btnCancelState();
    }
  });
},3000);

button.addEventListener('click', function(e) {
  btn = e.target;

  if( btn.innerText.toLowerCase() !== 'cancel' ) {
    fetch('/api.php?do=topup'); btnCancelState();

  }
  else {
    fetch('/api.php?do=topup_cancel'); btnInsertState();
  }
});

function btnInsertState() {
  button.innerText = 'insert coin';
  button.style.borderColor = '#11aa11';
  button.style.backgroundColor = '#22aa22';

}

function btnCancelState() {
  button.innerText = 'cancel';
  button.style.borderColor = '#aa1111';
  button.style.backgroundColor = '#aa2222';
}
