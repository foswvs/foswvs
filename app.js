var button = document.getElementById('insertCoin'),
    stat = document.getElementById('stat'),
    ping = document.getElementById('ping'),
    credits = document.getElementById('credits'),
    mac_addr = document.getElementById('mac_addr'),
    waiting = document.getElementById('waiting'),
    mb_credit = document.getElementById('mb_credit');

setInterval(() => {
  fetch('/api.php')
  .then((res) => res.json() )
  .then((wifi) => {
    credits.innerText = `${wifi.total_mb_used} / ${wifi.total_mb_credit}MB`;
    mac_addr.innerText = wifi.mac_addr.toUpperCase();
    ping.innerText = `${Math.floor(wifi.ping)}ms`;

    if( wifi.ping ) {
      stat.innerText = 'Online';
    }
    else{
      stat.innerText = 'Offline';
    }

    if( wifi.insert_coin ) {
      btnCancelState();
      waiting.style.display = 'block';

      if( wifi.mb_credit ) {
        mb_credit.innerText = wifi.mb_credit + 'MB';
        mb_credit.style.display = 'block';
      }
    } else {
      btnInsertState();
      waiting.style.display = 'none';
      mb_credit.style.display = 'none';
    }
  });
},1000);

button.addEventListener('click', function(e) {
  btn = e.target;

  if( btn.innerText.toLowerCase() !== 'done' ) {
    fetch('/api.php?do=topup'); btnCancelState();
    payment_count_down(); waiting.style.display = 'block';
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
  button.innerText = 'done';
  button.style.borderColor = '#aa1111';
  button.style.backgroundColor = '#aa2222';
}

function payment_count_down() {
  var count=20, timer = setInterval(function(){ waiting.innerText = `waiting for payment (${count--})`; if(count==0) clearInterval(timer); },1000);
}
