var initr = false,
    stat = document.getElementById('stat'),
    ping = document.getElementById('ping'),
    button = document.getElementById('insert'),
    credits = document.getElementById('credits'),
    mb_credit = document.getElementById('mb_credit'),
    mac_addr = document.getElementById('mac_addr'),
    waiting = document.getElementById('waiting');

setInterval(() => {
  fetch('/api.php')
  .then((res) => res.json() )
  .then((wifi) => {
    initr = wifi.initr;
    credits.innerText = `${wifi.total_mb_used} / ${wifi.total_mb_credit}MB`;
    mac_addr.innerText = wifi.mac_addr;
    ping.innerText = `${Math.floor(wifi.ping)}ms`;

    if( wifi.ping ) {
      stat.innerText = 'Online';
    }
    else{
      stat.innerText = 'Offline';
    }

    if( initr && wifi.insert_coin ) {
      btnCancelState();

      if( wifi.mb_credit ) {
        mb_credit.innerText = wifi.mb_credit + 'MB';
        mb_credit.style.display = 'block';
      }
    } else {
      btnInsertState();
    }
  });
},3000);

button.addEventListener('click', function(e) {
  btn = e.target;

  if( !initr ) {
    alert("Coinslot is not ready.");
    return false;
  }

  if( btn.innerText.toLowerCase() !== 'done' ) {
    fetch('/api.php?do=topup');
    btnCancelState();
  }
  else {
    fetch('/api.php?do=topup_cancel');
    btnInsertState();
  }
});

function btnInsertState() {
  if( button.innerText.toLowerCase() == 'done' ) {
    button.innerText = 'insert coin';
    button.style.borderColor = '#11aa11';
    button.style.backgroundColor = '#22aa22';
    waiting.style.display = 'none';
    mb_credit.style.display = 'none';
  }
}

function btnCancelState() {
  if( button.innerText.toLowerCase() !== 'done' ) {
    button.innerText = 'done';
    button.style.borderColor = '#aa1111';
    button.style.backgroundColor = '#aa2222';
    waiting.style.display = 'block';
    payment_count_down();
  }
}

function payment_count_down() {
  /* fix for https://github.com/ligrevx/foswvs-php/blob/7405c0d747b98a6d5fc2310a97a1b1eea351981d/app.js#L66 */
  var count = 20, timer = setInterval(function(){ if(button.innerText.toLowerCase()!=='done'){ clearInterval(timer);count=20;} waiting.innerText = `waiting for payment (${count})`;count--;},1000);
}
